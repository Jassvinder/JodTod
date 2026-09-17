<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GroupJoinRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private User $requestingUser,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_join_request',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'requesting_user_id' => $this->requestingUser->id,
            'requesting_user_name' => $this->requestingUser->name,
            'message' => "{$this->requestingUser->name} wants to join \"{$this->group->name}\".",
        ];
    }
}
