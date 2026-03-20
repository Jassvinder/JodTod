<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            // API request - revoke token and return JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                $request->user()->currentAccessToken()?->delete();

                return response()->json([
                    'message' => 'Your account has been suspended. Please contact support.',
                ], 403);
            }

            // Web request - session logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ]);
        }

        return $next($request);
    }
}
