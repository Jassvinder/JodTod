<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SettlementRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private float $amount,
        private string $toUserName,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notification_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/groups/{$this->group->id}/settlements");

        return (new MailMessage)
            ->subject("Settlement requested in {$this->group->name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You need to pay **₹{$this->amount}** to **{$this->toUserName}** in **{$this->group->name}**.")
            ->action('View Settlements', $url)
            ->line('You can manage your notification preferences in your profile settings.');
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
