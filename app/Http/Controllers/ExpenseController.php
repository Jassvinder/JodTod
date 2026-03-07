<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index(Request $request)
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

        $expenses = $query->paginate(20)->withQueryString();

        // Summary data
        $userId = Auth::id();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlyTotal = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
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

        $lastMonthTotal = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        $dailyTrend = Expense::personal()
            ->forUser($userId)
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->selectRaw('expense_date as date, SUM(amount) as total')
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date->toDateString(),
                'total' => (float) $item->total,
            ]);

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'categories' => Category::orderBy('name')->get(),
            'summary' => [
                'monthly_total' => (float) $monthlyTotal,
                'last_month_total' => (float) $lastMonthTotal,
                'category_breakdown' => $categoryBreakdown,
                'daily_trend' => $dailyTrend,
            ],
            'filters' => $request->only(['category', 'date_from', 'date_to', 'period', 'search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Expenses/Create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        Expense::create([
            ...$validated,
            'user_id' => Auth::id(),
            'paid_by' => Auth::id(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $expense->delete(); // soft delete

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    private function authorizeExpense(Expense $expense): void
    {
        if ($expense->user_id !== Auth::id() || $expense->group_id !== null) {
            abort(403);
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
