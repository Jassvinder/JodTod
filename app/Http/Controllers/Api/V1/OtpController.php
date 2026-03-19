<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
        ]);

        $phone = $request->phone;

        $user = User::where('phone', $phone)->first();
        if (! $user) {
            return $this->error('No account found with this phone number. Please register first and verify your phone number in profile settings.', 422);
        }

        if (! $user->phone_verified_at) {
            return $this->error('This phone number is not verified yet. Please verify it in your profile settings first.', 422);
        }

        $throttleKey = 'otp-send:' . $phone;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return $this->error("Too many OTP requests. Please try again in {$seconds} seconds.", 429);
        }

        RateLimiter::hit($throttleKey, 60);

        // Invalidate previous unused OTPs
        OtpCode::where('phone', $phone)->where('used', false)->update(['used' => true]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // TODO: Integrate SMS provider (MSG91, Twilio, etc.)
        Log::info("OTP for +91{$phone}: {$code}");

        $data = app()->environment('local') ? ['otp_debug' => $code] : null;

        return $this->success($data, 'OTP sent successfully.');
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'otp' => ['required', 'string', 'size:6'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $phone = $request->phone;
        $throttleKey = 'otp-verify:' . $phone;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return $this->error("Too many attempts. Please try again in {$seconds} seconds.", 429);
        }

        $otpRecord = OtpCode::where('phone', $phone)
            ->where('code', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (! $otpRecord || ! $otpRecord->isValid()) {
            RateLimiter::hit($throttleKey, 60);
            return $this->error('Invalid or expired OTP.', 422);
        }

        $otpRecord->update(['used' => true]);

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            return $this->error('No account found with this phone number.', 422);
        }

        RateLimiter::clear($throttleKey);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $user,
        ], 'Login successful.');
    }
}
