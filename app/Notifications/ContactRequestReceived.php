<?php

namespace App\Notifications;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContactRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Contact $contact,
        private User $requestingUser,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contact_request_received',
            'contact_id' => $this->contact->id,
            'requesting_user_id' => $this->requestingUser->id,
            'requesting_user_name' => $this->requestingUser->name,
            'message' => "{$this->requestingUser->name} wants to add you as a contact.",
        ];
    }
}
