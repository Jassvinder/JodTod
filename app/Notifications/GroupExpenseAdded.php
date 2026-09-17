<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\Group;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

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
        return ['database', ExpoPushChannel::class];
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
