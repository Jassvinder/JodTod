<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Income::forUser($userId);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('income_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('income_date', '<=', $request->date_to);
        }

        // Filter by source
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('source', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $incomes = $query->orderBy('income_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Monthly summary
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $thisMonthIncome = Income::forUser($userId)
            ->whereBetween('income_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $thisMonthExpense = Expense::where('user_id', $userId)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $lastMonthIncome = Income::forUser($userId)
            ->whereBetween('income_date', [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $mStart = $month->copy()->startOfMonth();
            $mEnd = $month->copy()->endOfMonth();

            $income = Income::forUser($userId)
                ->whereBetween('income_date', [$mStart, $mEnd])
                ->sum('amount');

            $expense = Expense::where('user_id', $userId)
                ->whereNull('group_id')
                ->whereBetween('expense_date', [$mStart, $mEnd])
                ->sum('amount');

            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'short_month' => $month->format('M'),
                'income' => round((float) $income, 2),
                'expense' => round((float) $expense, 2),
                'savings' => round((float) $income - (float) $expense, 2),
            ];
        }

        // Income sources breakdown (this month)
        $sourceBreakdown = Income::forUser($userId)
            ->whereBetween('income_date', [$monthStart, $monthEnd])
            ->select('source', DB::raw('ROUND(SUM(amount), 2) as total'))
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // Autocomplete sources
        $sourceSuggestions = Income::forUser($userId)
            ->select('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source')
            ->toArray();

        return Inertia::render('Incomes/Index', [
            'incomes' => $incomes,
            'summary' => [
                'this_month_income' => round((float) $thisMonthIncome, 2),
                'this_month_expense' => round((float) $thisMonthExpense, 2),
                'this_month_savings' => round((float) $thisMonthIncome - (float) $thisMonthExpense, 2),
                'last_month_income' => round((float) $lastMonthIncome, 2),
                'source_breakdown' => $sourceBreakdown,
                'monthly_trend' => $monthlyTrend,
            ],
            'sourceSuggestions' => $sourceSuggestions,
            'filters' => $request->only(['date_from', 'date_to', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'source' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'income_date' => 'required|date',
        ]);

        Income::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Income added successfully.');
    }

    public function update(Request $request, Income $income)
    {
        $this->authorizeIncome($income);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'source' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'income_date' => 'required|date',
        ]);

        $income->update($validated);

        return redirect()->back()->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        $this->authorizeIncome($income);

        $income->delete();

        return redirect()->back()->with('success', 'Income deleted successfully.');
    }

    private function authorizeIncome(Income $income): void
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
