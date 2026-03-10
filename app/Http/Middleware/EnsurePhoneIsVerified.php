<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    /**
     * Ensure the user has verified their phone number.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || is_null($request->user()->phone_verified_at)) {
            if ($request->inertia()) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Please verify your phone number to access group features.');
            }

            return redirect()->route('profile.edit')
                ->with('error', 'Please verify your phone number to access group features.');
        }

        return $next($request);
    }
}
