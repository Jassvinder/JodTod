<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Income;
use App\Models\Settlement;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with all summary data.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $data = [
            'personalSummary' => $this->getPersonalSummary($user),
            'incomeSummary' => $this->getIncomeSummary($user),
            'monthlyTrend' => $this->getMonthlyTrend($user),
            'groupsSummary' => $this->getGroupsSummary($user),
            'recentActivity' => $this->getRecentActivity($user),
            'pendingSettlements' => $this->getPendingSettlements($user),
            'todoStats' => $this->getTodoStats($user),
        ];

        if ($this->wantsJson()) {
            return $this->success($data);
        }

        return Inertia::render('Dashboard', $data);
    }

    /**
     * Get personal expense statistics for the authenticated user.
     */
    private function getPersonalSummary(User $user): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // This month total (personal expenses only - group_id IS NULL)
        $thisMonthTotal = Expense::where('user_id', $user->id)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Last month total
        $lastMonthTotal = Expense::where('user_id', $user->id)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        // Top 5 categories by amount this month
        $categoryBreakdown = Expense::where('user_id', $user->id)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                'categories.icon',
                DB::raw('ROUND(SUM(expenses.amount), 2) as total')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.icon')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();

        return [
            'this_month_total' => round((float) $thisMonthTotal, 2),
            'last_month_total' => round((float) $lastMonthTotal, 2),
            'category_breakdown' => $categoryBreakdown,
        ];
    }

    /**
     * Get income summary for the authenticated user.
     */
    private function getIncomeSummary(User $user): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $thisMonthIncome = Income::forUser($user->id)
            ->whereBetween('income_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $lastMonthIncome = Income::forUser($user->id)
            ->whereBetween('income_date', [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        $thisMonthExpense = Expense::where('user_id', $user->id)
            ->whereNull('group_id')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        return [
            'this_month_income' => round((float) $thisMonthIncome, 2),
            'last_month_income' => round((float) $lastMonthIncome, 2),
            'this_month_savings' => round((float) $thisMonthIncome - (float) $thisMonthExpense, 2),
        ];
    }

    /**
     * Get monthly income vs expense trend (last 6 months).
     */
    private function getMonthlyTrend(User $user): array
    {
        $now = Carbon::now();
        $trend = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $mStart = $month->copy()->startOfMonth();
            $mEnd = $month->copy()->endOfMonth();

            $income = Income::forUser($user->id)
                ->whereBetween('income_date', [$mStart, $mEnd])
                ->sum('amount');

            $expense = Expense::where('user_id', $user->id)
                ->whereNull('group_id')
                ->whereBetween('expense_date', [$mStart, $mEnd])
                ->sum('amount');

            $trend[] = [
                'month' => $month->format('M'),
                'income' => round((float) $income, 2),
                'expense' => round((float) $expense, 2),
            ];
        }

        return $trend;
    }

    /**
     * Get todo stats for dashboard widget.
     */
    private function getTodoStats(User $user): array
    {
        return [
            'pending' => Todo::forUser($user->id)->pending()->count(),
            'overdue' => Todo::forUser($user->id)->pending()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];
    }

    /**
     * Get group balance overview for the authenticated user.
     */
    private function getGroupsSummary(User $user): array
    {
        $groups = $user->groups()->withCount('members')->get();
        $groupIds = $groups->pluck('id');

        // Bulk fetch: total paid by user per group (unsettled)
        $paidPerGroup = Expense::whereIn('group_id', $groupIds)
            ->where('paid_by', $user->id)
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->groupBy('group_id')
            ->selectRaw('group_id, SUM(amount) as total_paid')
            ->pluck('total_paid', 'group_id');

        // Bulk fetch: total share user owes per group (unsettled)
        $sharePerGroup = DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->whereIn('expenses.group_id', $groupIds)
            ->where('expense_splits.user_id', $user->id)
            ->where('expenses.is_settled', false)
            ->whereNull('expenses.deleted_at')
            ->where('expense_splits.is_settled', false)
            ->groupBy('expenses.group_id')
            ->selectRaw('expenses.group_id, SUM(expense_splits.share_amount) as total_share')
            ->pluck('total_share', 'expenses.group_id');

        $groupBalances = [];
        $totalYouOwe = 0;
        $totalOwedToYou = 0;

        foreach ($groups as $group) {
            $paid = (float) ($paidPerGroup[$group->id] ?? 0);
            $share = (float) ($sharePerGroup[$group->id] ?? 0);
            $balance = round($paid - $share, 2);

            if ($balance < 0) {
                $totalYouOwe += abs($balance);
            } else {
                $totalOwedToYou += $balance;
            }

            $groupBalances[] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'your_balance' => $balance,
                'members_count' => $group->members_count,
            ];
        }

        return [
            'groups' => $groupBalances,
            'total_you_owe' => round($totalYouOwe, 2),
            'total_owed_to_you' => round($totalOwedToYou, 2),
        ];
    }

    /**
     * Get the last 10 activities combining personal expenses, group expenses, and settlements.
     */
    private function getRecentActivity(User $user): array
    {
        $activities = collect();

        // Personal expenses (last 10)
        $personalExpenses = Expense::where('user_id', $user->id)
            ->whereNull('group_id')
            ->with('category')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($expense) {
                return [
                    'type' => 'personal_expense',
                    'description' => $expense->description,
                    'amount' => round((float) $expense->amount, 2),
                    'date' => $expense->created_at->toISOString(),
                    'category' => $expense->category?->name,
                    'category_icon' => $expense->category?->icon,
                    'group_name' => null,
                ];
            });

        $activities = $activities->merge($personalExpenses);

        // Group expenses where user is the payer or is in the splits (last 10)
        $groupExpenseIds = ExpenseSplit::where('user_id', $user->id)
            ->pluck('expense_id');

        $groupExpenses = Expense::whereNotNull('group_id')
            ->where(function ($query) use ($user, $groupExpenseIds) {
                $query->where('paid_by', $user->id)
                    ->orWhereIn('id', $groupExpenseIds);
            })
            ->with(['category', 'group'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($expense) {
                return [
                    'type' => 'group_expense',
                    'description' => $expense->description,
                    'amount' => round((float) $expense->amount, 2),
                    'date' => $expense->created_at->toISOString(),
                    'category' => $expense->category?->name,
                    'category_icon' => $expense->category?->icon,
                    'group_name' => $expense->group?->name,
                ];
            });

        $activities = $activities->merge($groupExpenses);

        // Settlements involving user (last 5)
        $settlements = Settlement::where(function ($query) use ($user) {
                $query->where('from_user', $user->id)
                    ->orWhere('to_user', $user->id);
            })
            ->with(['group', 'fromUser', 'toUser'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($settlement) use ($user) {
                $isFromUser = $settlement->from_user === $user->id;
                $otherUser = $isFromUser ? $settlement->toUser : $settlement->fromUser;

                $description = $isFromUser
                    ? "You paid {$otherUser->name}"
                    : "{$otherUser->name} paid you";

                return [
                    'type' => 'settlement',
                    'description' => $description,
                    'amount' => round((float) $settlement->amount, 2),
                    'date' => $settlement->created_at->toISOString(),
                    'category' => null,
                    'category_icon' => null,
                    'group_name' => $settlement->group?->name,
                    'status' => $settlement->status,
                ];
            });

        $activities = $activities->merge($settlements);

        // Sort by date desc and take 10
        return $activities
            ->sortByDesc('date')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Get pending settlements where the user needs to pay someone.
     */
    private function getPendingSettlements(User $user): array
    {
        $settlements = Settlement::where('from_user', $user->id)
            ->where('status', 'pending')
            ->with(['group', 'toUser'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($settlement) {
                return [
                    'id' => $settlement->id,
                    'group_name' => $settlement->group?->name,
                    'to_user_name' => $settlement->toUser->name,
                    'to_user_avatar' => $settlement->toUser->avatar,
                    'amount' => round((float) $settlement->amount, 2),
                ];
            })
            ->toArray();

        return [
            'items' => $settlements,
            'count' => count($settlements),
        ];
    }
}
