<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class ReactivatedInGroup extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private string $reactivatedByName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reactivated_in_group',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'reactivated_by' => $this->reactivatedByName,
            'message' => "{$this->reactivatedByName} reactivated you in \"{$this->group->name}\".",
        ];
    }
}
