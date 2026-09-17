<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklySummary extends Notification implements ShouldQueue
{
    use HasBrandedMail, Queueable;

    public function __construct(
        private array $summary,
    ) {}

    public function via(object $notifiable): array
    {
        return $notifiable->notification_email ? ['mail'] : [];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->summary;

        return $this->brandedMailMessage(
            'Your Weekly Expense Summary — JodTod',
            "Hello {$notifiable->name}!",
            [
                'Here is your expense summary for the past week.',
                "Total expenses: ₹{$s['total_expenses']}",
                "Number of transactions: {$s['expense_count']}",
                "Top category: {$s['top_category']}",
                "Total income: ₹{$s['total_income']}",
                "Pending tasks: {$s['pending_tasks']}",
            ],
            'View Dashboard',
            url('/dashboard'),
            'Click the button below to view your full dashboard.',
            'Best regards, JodTod'
        );
    }
}
