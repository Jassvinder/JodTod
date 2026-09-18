<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials.');
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $user,
        ], 'Login successful.');
    }

    public function register(Request $request): JsonResponse
    {
        // API registration uses rate limiting (5/min) instead of CAPTCHA
        // CAPTCHA is for web registration only (reCAPTCHA v3 needs browser)
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken($validated['device_name'])->plainTextToken;

        return $this->created([
            'token' => $token,
            'user' => $user,
        ], 'Registration successful. Please verify your email.');
    }

    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
            'device_name' => 'required|string|max:255',
        ]);

        $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if (! $response->ok()) {
            return $this->unauthorized('Invalid Google token.');
        }

        $payload = $response->json();

        $allowedAudiences = array_filter([
            config('services.google.client_id'),
            config('services.google.android_client_id'),
            config('services.google.ios_client_id'),
        ]);

        $issuerIsGoogle = in_array($payload['iss'] ?? null, ['accounts.google.com', 'https://accounts.google.com'], true);
        $audienceIsOurs = in_array($payload['aud'] ?? null, $allowedAudiences, true);
        $emailIsVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $issuerIsGoogle || ! $audienceIsOurs || ! $emailIsVerified || empty($payload['email'])) {
            return $this->unauthorized('Invalid Google token.');
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];

        $user = User::where('google_id', $googleId)->orWhere('email', $email)->first();

        if ($user) {
            $updates = [];

            if (! $user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }

            if (! $user->google_id) {
                $updates['google_id'] = $googleId;
                $updates['avatar'] = $user->avatar ?? ($payload['picture'] ?? null);
            }

            if (! empty($updates)) {
                $user->update($updates);
            }
        } else {
            $user = User::create([
                'name' => $payload['name'] ?? explode('@', $email)[0],
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $payload['picture'] ?? null,
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $user,
        ], 'Login successful.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }

    public function resendVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(null, 'Email already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->success(null, 'Verification email sent.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Password reset link sent to your email.');
        }

        $message = match ($status) {
            Password::INVALID_USER => 'No account found with this email address.',
            Password::RESET_THROTTLED => 'Please wait before requesting another reset link.',
            default => 'Unable to send reset link. Please try again later.',
        };

        return $this->error($message, 422);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, 'Password reset successfully. Please login again.');
        }

        return $this->error('Unable to reset password.', 400);
    }
}
