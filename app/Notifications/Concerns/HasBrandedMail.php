<?php

namespace App\Notifications\Concerns;

use Illuminate\Notifications\Messages\MailMessage;

trait HasBrandedMail
{
    protected function brandedMailMessage(
        string $subject,
        string $greeting,
        array $contentLines,
        ?string $actionText = null,
        ?string $actionUrl = null,
        ?string $actionInstruction = null,
        ?string $footerText = null,
    ): MailMessage {
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.default', [
                'appName' => config('app.name', 'JodTod'),
                'greeting' => $greeting,
                'contentLines' => array_values(array_filter($contentLines, static fn ($line) => $line !== null && $line !== '')),
                'actionText' => $actionText,
                'actionUrl' => $actionUrl,
                'actionInstruction' => $actionInstruction,
                'footerText' => $footerText ?? 'You are receiving this email because of activity on your JodTod account.',
            ]);
    }
}
