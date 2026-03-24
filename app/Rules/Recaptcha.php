<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    private float $minScore;

    public function __construct(float $minScore = 0.5)
    {
        $this->minScore = $minScore;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret_key');

        if (empty($secret)) {
            // Skip validation if not configured (local dev)
            return;
        }

        if (empty($value)) {
            $fail('Please complete the CAPTCHA verification.');
            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                Log::warning('reCAPTCHA failed', ['errors' => $data['error-codes'] ?? []]);
                $fail('CAPTCHA verification failed. Please try again.');
                return;
            }

            if (($data['score'] ?? 0) < $this->minScore) {
                Log::warning('reCAPTCHA low score', ['score' => $data['score'] ?? 0]);
                $fail('Suspicious activity detected. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA exception: ' . $e->getMessage());
            // Don't block registration on reCAPTCHA service failure
        }
    }
}
