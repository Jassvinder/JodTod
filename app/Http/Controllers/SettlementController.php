<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\Settlement;
use App\Models\Category;
use App\Models\User;
use App\Notifications\SettlementCompleted;
use App\Notifications\SettlementRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SettlementController extends Controller
{
    /**
     * Settlement summary + history for a group.
     */
    public function index(Request $request, Group $group)
    {
        $this->ensureMember($group);

        $balances = $this->calculateBalances($group);
        $transactions = $this->calculateOptimizedTransactions($group);

        // Enrich transactions with user details
        $members = $group->members()->get()->keyBy('id');
        $suggestedTransactions = array_map(function ($t) use ($members) {
            $fromUser = $members->get($t['from']);
            $toUser = $members->get($t['to']);

            return [
                'from' => [
                    'id' => $t['from'],
                    'name' => $fromUser?->name ?? 'Unknown',
                    'avatar' => $fromUser?->avatar,
                ],
                'to' => [
                    'id' => $t['to'],
                    'name' => $toUser?->name ?? 'Unknown',
                    'avatar' => $toUser?->avatar,
                ],
                'amount' => $t['amount'],
            ];
        }, $transactions);

        // Calculate each member's total share from unsettled expenses
        $memberShares = $this->calculateMemberShares($group);

        // Settlement history
        $settlements = Settlement::where('group_id', $group->id)
            ->with(['fromUser:id,name,avatar', 'toUser:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Groups/Settlements/Index', [
            'group' => $group->load('members'),
            'balances' => $balances,
            'suggestedTransactions' => $suggestedTransactions,
            'settlements' => $settlements,
            'memberShares' => $memberShares,
        ]);
    }

    /**
     * Create settlement records from optimized transactions.
     */
    public function settle(Request $request, Group $group)
    {
        $this->ensureMember($group);

        // Only group admin can initiate settle up
        if (!$group->isAdmin(Auth::user())) {
            abort(403, 'Only group admin can initiate settlements.');
        }

        $result = DB::transaction(function () use ($group) {
            // Lock check: prevent duplicate pending settlements (race condition safe)
            $pendingCount = Settlement::where('group_id', $group->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->count();

            if ($pendingCount > 0) {
                return 'pending_exists';
            }

            $transactions = $this->calculateOptimizedTransactions($group);

            if (empty($transactions)) {
                return 'no_expenses';
            }

            $members = $group->members()->get()->keyBy('id');

            foreach ($transactions as $transaction) {
                Settlement::create([
                    'group_id' => $group->id,
                    'from_user' => $transaction['from'],
                    'to_user' => $transaction['to'],
                    'amount' => $transaction['amount'],
                    'status' => 'pending',
                ]);

                $fromUser = $members->get($transaction['from']);
                $toUser = $members->get($transaction['to']);
                if ($fromUser && $toUser) {
                    $fromUser->notify(new SettlementRequested($group, $transaction['amount'], $toUser->name));
                }
            }

            return 'success';
        });

        if ($result === 'pending_exists') {
            return redirect()->back()->with('info', 'Pending settlements already exist. Complete or settle them first before creating new ones.');
        }

        if ($result === 'no_expenses') {
            return redirect()->back()->with('info', 'No unsettled expenses found.');
        }

        return redirect()->route('groups.settlements.index', $group)
            ->with('success', 'Settlement created successfully. Pending confirmation from members.');
    }

    /**
     * Mark an individual settlement as completed.
     */
    public function markCompleted(Request $request, Group $group, Settlement $settlement)
    {
        $this->ensureMember($group);

        // Verify settlement belongs to this group
        if ($settlement->group_id !== $group->id) {
            abort(404);
        }

        // Prevent double completion
        if ($settlement->status === 'completed') {
            return redirect()->back()->with('info', 'Settlement is already completed.');
        }

        // Verify user is either from_user, to_user, or group admin
        $user = Auth::user();
        if (
            $settlement->from_user !== $user->id &&
            $settlement->to_user !== $user->id &&
            !$group->isAdmin($user)
        ) {
            abort(403, 'You are not authorized to mark this settlement as completed.');
        }

        DB::transaction(function () use ($settlement, $group) {
            $settlement->update([
                'status' => 'completed',
                'settled_at' => now(),
            ]);

            // Check if ALL settlements for this group are now completed
            $pendingCount = Settlement::where('group_id', $group->id)
                ->where('status', 'pending')
                ->count();

            if ($pendingCount === 0) {
                $this->markExpensesAsSettled($group);
            }
        });

        // Add as personal expense for the payer (from_user)
        $this->addSettlementToPersonalExpense($settlement, $group);

        // Notify the creditor (to_user) that payment was received
        $toUser = User::find($settlement->to_user);
        $fromUser = User::find($settlement->from_user);
        if ($toUser && $fromUser) {
            $toUser->notify(new SettlementCompleted($group, (float) $settlement->amount, $fromUser->name));
        }

        return redirect()->back()->with('success', 'Settlement marked as completed.');
    }

    /**
     * Mark ALL pending settlements as completed (admin only).
     */
    public function settleAll(Request $request, Group $group)
    {
        $this->ensureMember($group);

        if (!$group->isAdmin(Auth::user())) {
            abort(403, 'Only group admin can settle all at once.');
        }

        DB::transaction(function () use ($group) {
            $pendingSettlements = Settlement::where('group_id', $group->id)
                ->where('status', 'pending')
                ->get();

            foreach ($pendingSettlements as $settlement) {
                $settlement->update([
                    'status' => 'completed',
                    'settled_at' => now(),
                ]);
                $this->addSettlementToPersonalExpense($settlement, $group);
            }

            $this->markExpensesAsSettled($group);
        });

        return redirect()->back()->with('success', 'All settlements marked as completed.');
    }

    /**
     * Mark all unsettled expenses and their splits as settled for a group.
     */
    private function markExpensesAsSettled(Group $group): void
    {
        $unsettledExpenseIds = Expense::where('group_id', $group->id)
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->pluck('id');

        Expense::where('group_id', $group->id)
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->update(['is_settled' => true]);

        ExpenseSplit::whereIn('expense_id', $unsettledExpenseIds)
            ->where('is_settled', false)
            ->update(['is_settled' => true]);
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
                'balance' => $balance,
            ];
        }

        return $balances;
    }

    /**
     * Calculate each member's total share from unsettled expenses.
     */
    private function calculateMemberShares(Group $group): array
    {
        $members = $group->members()->get();

        $unsettledExpenseIds = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->pluck('id');

        $totalExpenseAmount = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->sum('amount');

        $shares = [];

        foreach ($members as $member) {
            $totalShare = ExpenseSplit::whereIn('expense_id', $unsettledExpenseIds)
                ->where('user_id', $member->id)
                ->where('is_settled', false)
                ->sum('share_amount');

            $shares[] = [
                'user_id' => $member->id,
                'name' => $member->name,
                'avatar' => $member->avatar,
                'total_share' => round((float) $totalShare, 2),
            ];
        }

        return [
            'total_expense' => round((float) $totalExpenseAmount, 2),
            'members' => $shares,
        ];
    }

    /**
     * Calculate optimized settlement transactions using greedy min-transfers algorithm.
     */
    private function calculateOptimizedTransactions(Group $group): array
    {
        // Step 1: Get unsettled expenses
        $expenses = Expense::where('group_id', $group->id)
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->with('splits')
            ->get();

        if ($expenses->isEmpty()) {
            return [];
        }

        // Step 2: Calculate total paid per member
        $totalPaid = [];
        foreach ($expenses as $expense) {
            $totalPaid[$expense->paid_by] = ($totalPaid[$expense->paid_by] ?? 0) + (float) $expense->amount;
        }

        // Step 3: Calculate total share per member
        $totalShare = [];
        foreach ($expenses as $expense) {
            foreach ($expense->splits as $split) {
                $totalShare[$split->user_id] = ($totalShare[$split->user_id] ?? 0) + (float) $split->share_amount;
            }
        }

        // Step 4: Calculate net balances
        $memberIds = $group->members()->pluck('users.id')->toArray();
        $balances = [];
        foreach ($memberIds as $memberId) {
            $paid = $totalPaid[$memberId] ?? 0;
            $share = $totalShare[$memberId] ?? 0;
            $balances[$memberId] = round($paid - $share, 2);
        }

        // Step 5: Greedy min-transfers algorithm
        $creditors = [];
        $debtors = [];
        foreach ($balances as $userId => $balance) {
            if ($balance > 0.01) {
                $creditors[] = ['user_id' => $userId, 'amount' => $balance];
            } elseif ($balance < -0.01) {
                $debtors[] = ['user_id' => $userId, 'amount' => abs($balance)];
            }
        }

        // Sort descending by amount
        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);

        $transactions = [];
        $i = 0;
        $j = 0;

        while ($i < count($creditors) && $j < count($debtors)) {
            $transfer = min($creditors[$i]['amount'], $debtors[$j]['amount']);
            $transactions[] = [
                'from' => $debtors[$j]['user_id'],
                'to' => $creditors[$i]['user_id'],
                'amount' => round($transfer, 2),
            ];
            $creditors[$i]['amount'] -= $transfer;
            $debtors[$j]['amount'] -= $transfer;
            if ($creditors[$i]['amount'] < 0.01) $i++;
            if ($debtors[$j]['amount'] < 0.01) $j++;
        }

        return $transactions;
    }

    /**
     * Add a completed settlement as a personal expense for the payer.
     */
    private function addSettlementToPersonalExpense(Settlement $settlement, Group $group): void
    {
        $otherCategory = Category::where('name', 'Other')->first();

        $toUser = User::find($settlement->to_user);
        $description = "Settlement: {$group->name} - paid to {$toUser->name}";

        Expense::create([
            'user_id' => $settlement->from_user,
            'paid_by' => $settlement->from_user,
            'amount' => $settlement->amount,
            'category_id' => $otherCategory?->id,
            'description' => $description,
            'expense_date' => now(),
            'group_id' => null,
            'is_settled' => true,
        ]);
    }

    /**
     * Ensure the authenticated user is a member of the group.
     */
    private function ensureMember(Group $group): void
    {
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }
    }
}
