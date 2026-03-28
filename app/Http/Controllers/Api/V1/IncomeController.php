<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request): JsonResponse
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

        // Search by source or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('source', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $paginator = $query->orderBy('income_date', 'desc')->paginate(20);

        // Summary data
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $thisMonthIncome = Income::forUser($userId)
            ->whereBetween('income_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $lastMonthIncome = Income::forUser($userId)
            ->whereBetween('income_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        $thisMonthExpense = Expense::where('user_id', $userId)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $thisMonthSavings = (float) $thisMonthIncome - (float) $thisMonthExpense;

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
                'this_month_income' => round((float) $thisMonthIncome, 2),
                'last_month_income' => round((float) $lastMonthIncome, 2),
                'this_month_expense' => round((float) $thisMonthExpense, 2),
                'this_month_savings' => round($thisMonthSavings, 2),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'source' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'income_date' => 'required|date',
        ]);

        $income = Income::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return $this->created($income, 'Income added successfully.');
    }

    public function update(Request $request, Income $income): JsonResponse
    {
        $this->authorizeIncome($income);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'source' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'income_date' => 'required|date',
        ]);

        $income->update($validated);

        return $this->success($income, 'Income updated successfully.');
    }

    public function destroy(Income $income): JsonResponse
    {
        $this->authorizeIncome($income);

        $income->delete();

        return $this->success(null, 'Income deleted successfully.');
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $suggestions = Income::where('user_id', Auth::id())
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->where('source', 'like', '%' . $query . '%')
            ->select('source')
            ->distinct()
            ->orderBy('source')
            ->limit(10)
            ->pluck('source');

        return $this->success($suggestions);
    }

    private function authorizeIncome(Income $income): void
    {
        if ($income->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }
}
