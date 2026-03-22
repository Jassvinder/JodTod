<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class GroupExpenseAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private Expense $expense,
        private string $payerName,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', ExpoPushChannel::class];

        if ($notifiable->notification_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/groups/{$this->group->id}/expenses");

        return (new MailMessage)
            ->subject("New expense in {$this->group->name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->payerName} added an expense of ₹{$this->expense->amount} in **{$this->group->name}**.")
            ->line("**{$this->expense->description}**")
            ->action('View Group Expenses', $url)
            ->line('You can manage your notification preferences in your profile settings.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_expense_added',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'expense_id' => $this->expense->id,
            'expense_description' => $this->expense->description,
            'expense_amount' => $this->expense->amount,
            'payer_name' => $this->payerName,
            'message' => "{$this->payerName} added ₹{$this->expense->amount} for \"{$this->expense->description}\" in {$this->group->name}.",
        ];
    }
}
