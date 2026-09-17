<?php

namespace App\Notifications;

use App\Models\Group;
use App\Notifications\Channels\ExpoPushChannel;
use App\Notifications\Concerns\HasBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SettlementRequested extends Notification implements ShouldQueue
{
    use HasBrandedMail, Queueable;

    public function __construct(
        private Group $group,
        private float $amount,
        private string $toUserName,
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
        return $this->brandedMailMessage(
            "Settlement requested in {$this->group->name}",
            "Hello {$notifiable->name}!",
            [
                "You need to pay ₹{$this->amount} to {$this->toUserName} in {$this->group->name}.",
                'Review the settlement details before completing your payment.',
            ],
            'View Settlements',
            url("/groups/{$this->group->id}/settlements"),
            'Click the button below to view the settlement details.',
            'Best regards, JodTod'
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'settlement_requested',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'amount' => $this->amount,
            'to_user_name' => $this->toUserName,
            'message' => "You need to pay ₹{$this->amount} to {$this->toUserName} in {$this->group->name}.",
        ];
    }
}
