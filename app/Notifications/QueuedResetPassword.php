<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasBrandedMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedResetPassword extends ResetPassword implements ShouldQueue
{
    use HasBrandedMail, Queueable;

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        $name = trim((string) ($notifiable->name ?: $notifiable->email ?: 'there'));

        return $this->brandedMailMessage(
            'Reset your password',
            "Hello {$name},",
            [
                'We received a request to reset the password for your JodTod account.',
                'For your security, this password reset link will expire soon.',
                'If you did not request a password reset, you can safely ignore this email.',
            ],
            'Reset Password',
            $resetUrl,
            'Click the button below to securely reset your password.',
            'Best regards, JodTod'
        );
    }
}
