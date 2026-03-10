<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class PhoneVerificationController extends Controller
{
    /**
     * Send OTP to verify a phone number from profile settings.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^[6-9]\d{9}$/',
                Rule::unique(User::class)->ignore($request->user()->id),
            ],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'phone.unique' => 'This phone number is already registered with another account.',
        ]);

        $phone = $request->phone;
        $throttleKey = 'phone-verify-send:' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Too many requests. Please try again in {$seconds} seconds.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        // Invalidate previous unused OTPs for this phone
        OtpCode::where('phone', $phone)->where('used', false)->update(['used' => true]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // TODO: Integrate SMS provider (MSG91, Twilio, etc.)
        Log::info("Phone verification OTP for +91{$phone}: {$code}");

        return response()->json([
            'message' => 'OTP sent successfully.',
            'otp_debug' => app()->environment('local') ? $code : null,
        ]);
    }

    /**
     * Verify OTP and save the phone number to user profile.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^[6-9]\d{9}$/',
                Rule::unique(User::class)->ignore($request->user()->id),
            ],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $phone = $request->phone;
        $throttleKey = 'phone-verify-check:' . $request->user()->id;

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

        $otpRecord->update(['used' => true]);

        // Save verified phone to user
        $request->user()->update([
            'phone' => $phone,
            'phone_verified_at' => now(),
        ]);

        RateLimiter::clear($throttleKey);

        return response()->json([
            'message' => 'Phone number verified successfully.',
            'phone' => $phone,
        ]);
    }

    /**
     * Remove phone number from user profile.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->user()->update([
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        return response()->json([
            'message' => 'Phone number removed.',
        ]);
    }
}
