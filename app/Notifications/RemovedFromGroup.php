<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class RemovedFromGroup extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private string $removedByName,
        private bool $deactivated = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->deactivated ? 'deactivated from' : 'removed from';

        return [
            'type' => 'removed_from_group',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'removed_by' => $this->removedByName,
            'deactivated' => $this->deactivated,
            'message' => "{$this->removedByName} {$action} \"{$this->group->name}\".",
        ];
    }
}
