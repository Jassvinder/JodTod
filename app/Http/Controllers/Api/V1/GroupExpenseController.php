<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Notifications\GroupExpenseAdded;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class GroupExpenseController extends Controller
{
    /**
     * List group expenses paginated with filters.
     */
    public function index(Request $request, Group $group): JsonResponse
    {
        $this->ensureMember($group);

        $query = $group->expenses()
            ->with(['category', 'payer'])
            ->whereNull('deleted_at');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $paginator = $query->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginated($paginator);
    }

    /**
     * Create a group expense with splits.
     */
    public function store(Request $request, Group $group): JsonResponse
    {
        $this->ensureMember($group);

        $memberIds = $group->activeMembers()->pluck('users.id')->toArray();
        $memberIdList = implode(',', $memberIds);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'paid_by' => ['required', 'integer', "in:{$memberIdList}"],
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date|before_or_equal:now',
            'split_type' => 'required|in:equal,custom,percentage',
            'splits' => 'required|array|min:1',
            'splits.*.user_id' => ['required', 'integer', "in:{$memberIdList}"],
            'splits.*.share_amount' => 'required|numeric|min:0.01',
            'splits.*.percentage' => 'nullable|numeric|min:0|max:100',
            'image_1' => 'nullable|image|max:5120',
            'image_2' => 'nullable|image|max:5120',
        ]);

        $this->validateSplits($validated);

        $imagePaths = [];
        foreach (['image_1', 'image_2'] as $field) {
            if ($request->hasFile($field)) {
                $imagePaths[$field] = $request->file($field)->store('expenses', 'public');
            }
        }

        $expense = DB::transaction(function () use ($validated, $group, $imagePaths) {
            $expense = Expense::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(),
                'paid_by' => $validated['paid_by'],
                'amount' => $validated['amount'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'expense_date' => $validated['expense_date'],
                'split_type' => $validated['split_type'],
                ...$imagePaths,
            ]);

            $splits = $this->prepareSplits($validated);

            foreach ($splits as $split) {
                $expense->splits()->create($split);
            }

            return $expense;
        });

        // Notify all group members except the creator
        $payer = Auth::user();
        $membersToNotify = $group->members()->where('users.id', '!=', $payer->id)->get();
        Notification::send($membersToNotify, new GroupExpenseAdded($group, $expense, $payer->name));

        $expense->load(['category', 'payer', 'splits.user']);

        return $this->created($expense, 'Expense added successfully.');
    }

    /**
     * Single expense with splits loaded.
     */
    public function show(Group $group, Expense $expense): JsonResponse
    {
        $this->ensureMember($group);
        $this->ensureExpenseBelongsToGroup($expense, $group);

        $expense->load(['category', 'payer', 'splits.user']);

        return $this->success($expense);
    }

    /**
     * Update expense and recalculate splits. Creator or admin only.
     */
    public function update(Request $request, Group $group, Expense $expense): JsonResponse
    {
        $this->ensureMember($group);
        $this->ensureExpenseBelongsToGroup($expense, $group);
        $this->ensureCanModify($group, $expense);

        $memberIds = $group->activeMembers()->pluck('users.id')->toArray();
        $memberIdList = implode(',', $memberIds);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'paid_by' => ['required', 'integer', "in:{$memberIdList}"],
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date|before_or_equal:now',
            'split_type' => 'required|in:equal,custom,percentage',
            'splits' => 'required|array|min:1',
            'splits.*.user_id' => ['required', 'integer', "in:{$memberIdList}"],
            'splits.*.share_amount' => 'required|numeric|min:0.01',
            'splits.*.percentage' => 'nullable|numeric|min:0|max:100',
            'image_1' => 'nullable|image|max:5120',
            'image_2' => 'nullable|image|max:5120',
            'remove_image_1' => 'nullable|boolean',
            'remove_image_2' => 'nullable|boolean',
        ]);

        $this->validateSplits($validated);

        $imageUpdates = [];
        foreach (['image_1', 'image_2'] as $field) {
            $removeKey = "remove_{$field}";
            if ($request->boolean($removeKey) && $expense->{$field}) {
                \Storage::disk('public')->delete($expense->{$field});
                $imageUpdates[$field] = null;
            } elseif ($request->hasFile($field)) {
                if ($expense->{$field}) {
                    \Storage::disk('public')->delete($expense->{$field});
                }
                $imageUpdates[$field] = $request->file($field)->store('expenses', 'public');
            }
        }

        DB::transaction(function () use ($validated, $expense, $imageUpdates) {
            $expense->update([
                'paid_by' => $validated['paid_by'],
                'amount' => $validated['amount'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'expense_date' => $validated['expense_date'],
                'split_type' => $validated['split_type'],
                ...$imageUpdates,
            ]);

            // Delete old splits and create new ones
            $expense->splits()->delete();

            $splits = $this->prepareSplits($validated);

            foreach ($splits as $split) {
                $expense->splits()->create($split);
            }
        });

        $expense->load(['category', 'payer', 'splits.user']);

        return $this->success($expense, 'Expense updated successfully.');
    }

    /**
     * Soft delete a group expense. Creator or admin only.
     */
    public function destroy(Group $group, Expense $expense): JsonResponse
    {
        $this->ensureMember($group);
        $this->ensureExpenseBelongsToGroup($expense, $group);
        $this->ensureCanModify($group, $expense);

        $expense->delete(); // soft delete

        return $this->success(null, 'Expense deleted successfully.');
    }

    /**
     * Return member-wise balances for the group.
     */
    public function balances(Group $group): JsonResponse
    {
        $this->ensureMember($group);

        return $this->success($this->calculateBalances($group));
    }

    /**
     * Calculate balance for each member in the group.
     *
     * Balance = total_paid - total_share
     * Positive = others owe them, Negative = they owe others
     */
    private function calculateBalances(Group $group): array
    {
        $members = $group->members()->get();

        $unsettledExpenseIds = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->pluck('id');

        $balances = [];

        foreach ($members as $member) {
            $totalPaid = $group->expenses()
                ->where('paid_by', $member->id)
                ->where('is_settled', false)
                ->whereNull('deleted_at')
                ->sum('amount');

            $totalShare = ExpenseSplit::whereIn('expense_id', $unsettledExpenseIds)
                ->where('user_id', $member->id)
                ->where('is_settled', false)
                ->sum('share_amount');

            $balance = round((float) $totalPaid - (float) $totalShare, 2);

            $balances[] = [
                'user_id' => $member->id,
                'name' => $member->name,
                'avatar' => $member->avatar,
                'total_paid' => round((float) $totalPaid, 2),
                'total_share' => round((float) $totalShare, 2),
                'balance' => $balance,
            ];
        }

        return $balances;
    }

    /**
     * Validate that splits sum matches the expense amount.
     */
    private function validateSplits(array $validated): void
    {
        $amount = (float) $validated['amount'];
        $splitType = $validated['split_type'];
        $splits = $validated['splits'];

        // Validate split amounts sum to total (within 0.01 tolerance for rounding)
        $splitSum = array_sum(array_column($splits, 'share_amount'));
        if (abs($splitSum - $amount) > 0.01) {
            abort(response()->json([
                'success' => false,
                'message' => 'Split amounts must equal the total expense amount.',
            ], 422));
        }

        // For percentage split, validate percentages sum to 100
        if ($splitType === 'percentage') {
            $percentageSum = array_sum(array_column($splits, 'percentage'));
            if (abs($percentageSum - 100) > 0.01) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Split percentages must equal 100.',
                ], 422));
            }
        }
    }

    /**
     * Prepare split records from validated data.
     * Handles equal split rounding by adding remainder to first split.
     */
    private function prepareSplits(array $validated): array
    {
        $splits = $validated['splits'];
        $amount = (float) $validated['amount'];
        $splitType = $validated['split_type'];

        if ($splitType === 'equal') {
            $count = count($splits);
            $perPerson = floor($amount * 100 / $count) / 100;
            $remainder = round($amount - ($perPerson * $count), 2);

            return array_map(function ($split, $index) use ($perPerson, $remainder) {
                $shareAmount = $index === 0
                    ? round($perPerson + $remainder, 2)
                    : $perPerson;

                return [
                    'user_id' => $split['user_id'],
                    'share_amount' => $shareAmount,
                    'percentage' => null,
                ];
            }, $splits, array_keys($splits));
        }

        return array_map(function ($split) {
            return [
                'user_id' => $split['user_id'],
                'share_amount' => $split['share_amount'],
                'percentage' => $split['percentage'] ?? null,
            ];
        }, $splits);
    }

    /**
     * Ensure the authenticated user is a member of the group.
     */
    private function ensureMember(Group $group): void
    {
        if (!$group->isMember(Auth::user())) {
            abort(response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403));
        }
    }

    /**
     * Ensure the expense belongs to the given group.
     */
    private function ensureExpenseBelongsToGroup(Expense $expense, Group $group): void
    {
        if ($expense->group_id !== $group->id) {
            abort(response()->json([
                'success' => false,
                'message' => 'Expense not found in this group.',
            ], 404));
        }
    }

    /**
     * Ensure the user can modify the expense (creator or group admin).
     */
    private function ensureCanModify(Group $group, Expense $expense): void
    {
        $user = Auth::user();

        if ($expense->user_id !== $user->id && !$group->isAdmin($user)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Only the expense creator or group admin can modify this expense.',
            ], 403));
        }
    }
}
