<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    /**
     * Send OTP to the given phone number.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
        ]);

        $phone = $request->phone;

        // Only send OTP if a registered user exists with this verified phone
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'message' => 'No account found with this phone number. Please register first and verify your phone number in profile settings.',
            ], 422);
        }

        if (!$user->phone_verified_at) {
            return response()->json([
                'message' => 'This phone number is not verified yet. Please verify it in your profile settings first.',
            ], 422);
        }

        $throttleKey = 'otp-send:' . $phone;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Too many OTP requests. Please try again in {$seconds} seconds.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        // Invalidate previous unused OTPs for this phone
        OtpCode::where('phone', $phone)->where('used', false)->update(['used' => true]);

        // Generate 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // TODO: Integrate SMS provider (MSG91, Twilio, etc.)
        // For now, log the OTP for development
        Log::info("OTP for +91{$phone}: {$code}");

        return response()->json([
            'message' => 'OTP sent successfully.',
            // Remove this in production - only for development
            'otp_debug' => app()->environment('local') ? $code : null,
        ]);
    }

    /**
     * Verify OTP and log the user in.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $phone = $request->phone;
        $throttleKey = 'otp-verify:' . $phone;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $otpRecord = OtpCode::where('phone', $phone)
            ->where('code', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otpRecord || !$otpRecord->isValid()) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json([
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Find user by phone
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with this phone number.',
            ], 422);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // Redirect admin users to admin dashboard
        $redirect = $user->isAppAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);

        return response()->json([
            'message' => 'Login successful.',
            'redirect' => $redirect,
        ]);
    }
}
