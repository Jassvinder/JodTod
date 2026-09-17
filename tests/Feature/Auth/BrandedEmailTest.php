<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\QueuedVerifyEmail;
use Tests\TestCase;

class BrandedEmailTest extends TestCase
{
    public function test_verification_email_uses_and_renders_the_shared_branded_template(): void
    {
        $user = new User([
            'name' => 'Taylor',
            'email' => 'taylor@example.test',
        ]);
        $user->id = 1;

        $message = (new QueuedVerifyEmail)->toMail($user);
        $html = view($message->view, $message->viewData)->render();

        $this->assertSame('emails.notifications.default', $message->view);
        $this->assertStringContainsString('Verify Email Address', $html);
        $this->assertStringContainsString('Click the button below to verify your email address.', $html);
    }
}
