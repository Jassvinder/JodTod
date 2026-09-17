<?php

namespace App\Notifications;

use App\Models\Group;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AddedToGroup extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private string $addedByName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'added_to_group',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'added_by' => $this->addedByName,
            'message' => "{$this->addedByName} added you to \"{$this->group->name}\".",
        ];
    }
}
