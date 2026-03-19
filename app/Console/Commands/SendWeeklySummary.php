<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\WeeklySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendWeeklySummary extends Command
{
    protected $signature = 'summary:weekly';

    protected $description = 'Send weekly expense summary email to all users';

    public function handle(): int
    {
        $weekAgo = now()->subWeek();

        $users = User::where('notification_email', true)
            ->whereNull('banned_at')
            ->cursor();

        $count = 0;

        foreach ($users as $user) {
            $expenses = $user->expenses()
                ->where('created_at', '>=', $weekAgo);

            $totalExpenses = round((float) $expenses->sum('amount'), 2);
            $expenseCount = $expenses->count();

            // Skip users with no activity
            if ($expenseCount === 0) continue;

            $topCategory = DB::table('expenses')
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->where('expenses.user_id', $user->id)
                ->where('expenses.created_at', '>=', $weekAgo)
                ->groupBy('categories.name')
                ->orderByRaw('SUM(expenses.amount) DESC')
                ->value('categories.name') ?? 'N/A';

            $totalIncome = round((float) DB::table('incomes')
                ->where('user_id', $user->id)
                ->where('created_at', '>=', $weekAgo)
                ->sum('amount'), 2);

            $pendingTasks = DB::table('todos')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                })
                ->where('is_completed', false)
                ->count();

            $user->notify(new WeeklySummary([
                'total_expenses' => $totalExpenses,
                'expense_count' => $expenseCount,
                'top_category' => $topCategory,
                'total_income' => $totalIncome,
                'pending_tasks' => $pendingTasks,
            ]));

            $count++;
        }

        if ($count > 0) {
            $this->info("Sent weekly summary to {$count} user(s).");
        }

        return self::SUCCESS;
    }
}
