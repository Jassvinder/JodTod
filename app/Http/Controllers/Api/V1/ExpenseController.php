<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Expense::personal()
            ->forUser(Auth::id())
            ->with('category');

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

        // Filter by preset period
        if ($request->filled('period')) {
            $query = $this->applyPeriodFilter($query, $request->period);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortField = $request->input('sort', 'expense_date');
        $sortDir = $request->input('direction', 'desc');
        $allowedSorts = ['expense_date', 'amount', 'category_id'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $paginator = $query->paginate(20);

        // Summary data
        $userId = Auth::id();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlyTotal = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $lastMonthTotal = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        $categoryBreakdown = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($item) => [
                'category' => $item->category->name,
                'icon' => $item->category->icon,
                'total' => (float) $item->total,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'summary' => [
                'monthly_total' => (float) $monthlyTotal,
                'last_month_total' => (float) $lastMonthTotal,
                'category_breakdown' => $categoryBreakdown,
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date|before_or_equal:now',
            'image_1' => 'nullable|image|max:5120',
            'image_2' => 'nullable|image|max:5120',
        ]);

        $data = collect($validated)->except(['image_1', 'image_2'])->toArray();
        $data['user_id'] = Auth::id();
        $data['paid_by'] = Auth::id();

        foreach (['image_1', 'image_2'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('expenses', 'public');
            }
        }

        $expense = Expense::create($data);
        $expense->load('category');

        return $this->created($expense, 'Expense added successfully.');
    }

    public function show(Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);

        $expense->load('category');

        return $this->success($expense);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date|before_or_equal:now',
            'image_1' => 'nullable|image|max:5120',
            'image_2' => 'nullable|image|max:5120',
            'remove_image_1' => 'nullable|boolean',
            'remove_image_2' => 'nullable|boolean',
        ]);

        $data = collect($validated)->except(['image_1', 'image_2', 'remove_image_1', 'remove_image_2'])->toArray();

        foreach (['image_1', 'image_2'] as $field) {
            $removeKey = "remove_{$field}";
            if ($request->boolean($removeKey) && $expense->{$field}) {
                Storage::disk('public')->delete($expense->{$field});
                $data[$field] = null;
            } elseif ($request->hasFile($field)) {
                if ($expense->{$field}) {
                    Storage::disk('public')->delete($expense->{$field});
                }
                $data[$field] = $request->file($field)->store('expenses', 'public');
            }
        }

        $expense->update($data);
        $expense->load('category');

        return $this->success($expense, 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);

        $expense->delete();

        return $this->success(null, 'Expense deleted successfully.');
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $suggestions = Expense::where('user_id', Auth::id())
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->where('description', 'like', '%' . $query . '%')
            ->select('description')
            ->distinct()
            ->orderBy('description')
            ->limit(10)
            ->pluck('description');

        return $this->success($suggestions);
    }

    private function authorizeExpense(Expense $expense): void
    {
        if ($expense->user_id !== Auth::id() || $expense->group_id !== null) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }

    private function applyPeriodFilter($query, string $period)
    {
        return match ($period) {
            'today' => $query->where('expense_date', today()),
            'week' => $query->where('expense_date', '>=', now()->startOfWeek()),
            'month' => $query->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()]),
            default => $query,
        };
    }
}
