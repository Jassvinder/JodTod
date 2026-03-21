<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JoinRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'join_request_rejected',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => "Your request to join \"{$this->group->name}\" was rejected.",
        ];
    }
}
