<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklySummary extends Notification implements ShouldQueue
{
    use Queueable;

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

        return (new MailMessage)
            ->subject('Your Weekly Expense Summary — JodTod')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Here's your expense summary for the past week:")
            ->line("**Total Expenses:** ₹{$s['total_expenses']}")
            ->line("**Number of Transactions:** {$s['expense_count']}")
            ->line("**Top Category:** {$s['top_category']}")
            ->line("**Total Income:** ₹{$s['total_income']}")
            ->line("**Pending Tasks:** {$s['pending_tasks']}")
            ->action('View Dashboard', url('/dashboard'))
            ->line('You can manage your notification preferences in your profile settings.');
    }
}
