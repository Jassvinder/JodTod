<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     *
     * No auto-login — just verify and show success page.
     * User could be verifying from someone else's device or a shared computer.
     */
    public function __invoke(Request $request, int $id, string $hash): Response
    {
        $user = User::findOrFail($id);

        // Validate hash matches the user's email
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        $alreadyVerified = $user->hasVerifiedEmail();

        if (! $alreadyVerified && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return Inertia::render('Auth/EmailVerified', [
            'already_verified' => $alreadyVerified,
        ]);
    }
}
