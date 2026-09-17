<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasBrandedMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use HasBrandedMail, Queueable;

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $name = trim((string) ($notifiable->name ?: $notifiable->email ?: 'there'));

        return $this->brandedMailMessage(
            'Verify your email address',
            "Welcome to JodTod, {$name}!",
            [
                'Thank you for creating your account.',
                'Please verify your email address to activate your account and keep it secure.',
                'If you did not create a JodTod account, no action is required.',
            ],
            'Verify Email Address',
            $verificationUrl,
            'Click the button below to verify your email address.',
            'Best regards, JodTod'
        );
    }
}
