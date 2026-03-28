<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    /**
     * Send push notification to a specific user.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->get();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $deviceToken) {
            if ($deviceToken->platform === 'fcm') {
                self::sendViaFcm($deviceToken->token, $title, $body, $data);
            } else {
                self::sendViaExpo($deviceToken->token, $title, $body, $data);
            }
        }
    }

    /**
     * Send push notification to multiple users.
     */
    public static function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)->get();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $deviceToken) {
            if ($deviceToken->platform === 'fcm') {
                self::sendViaFcm($deviceToken->token, $title, $body, $data);
            } else {
                self::sendViaExpo($deviceToken->token, $title, $body, $data);
            }
        }
    }

    /**
     * Send via FCM V1 API directly (for native FCM tokens).
     */
    private static function sendViaFcm(string $token, string $title, string $body, array $data = []): void
    {
        Log::info('FCM: sending to token ' . substr($token, 0, 20) . '... title: ' . $title);

        $accessToken = self::getFcmAccessToken();
        if (!$accessToken) {
            Log::error('FCM: no access token');
            return;
        }

        $serviceAccount = self::getServiceAccount();
        if (!$serviceAccount) {
            Log::error('FCM: no service account');
            return;
        }

        $projectId = $serviceAccount['project_id'];

        try {
            $response = Http::withToken($accessToken)->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => array_map('strval', $data),
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'channel_id' => 'default',
                            ],
                        ],
                    ],
                ]
            );

            if ($response->failed()) {
                Log::warning('FCM send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->status() === 404 || str_contains($response->body(), 'UNREGISTERED')) {
                    DeviceToken::where('token', $token)->delete();
                }
            }
        } catch (\Exception $e) {
            Log::error('FCM exception: ' . $e->getMessage());
        }
    }

    /**
     * Send via Expo Push API (fallback for Expo tokens).
     */
    private static function sendViaExpo(string $token, string $title, string $body, array $data = []): void
    {
        if (!str_starts_with($token, 'ExponentPushToken[') && !str_starts_with($token, 'ExpoPushToken[')) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://exp.host/--/api/v2/push/send', [[
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'priority' => 'high',
                'channelId' => 'default',
            ]]);

            if ($response->failed()) {
                Log::warning('Expo Push failed', ['body' => $response->body()]);
            }

            if ($response->successful()) {
                $results = $response->json('data') ?? [];
                foreach ($results as $result) {
                    if (($result['status'] ?? '') === 'error') {
                        if (($result['details']['error'] ?? '') === 'DeviceNotRegistered') {
                            DeviceToken::where('token', $token)->delete();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Expo Push exception: ' . $e->getMessage());
        }
    }

    /**
     * Get FCM V1 access token using service account JWT.
     */
    private static function getFcmAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', 3500, function () {
            $sa = self::getServiceAccount();
            if (!$sa) {
                return null;
            }

            try {
                $now = time();
                $header = self::base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
                $payload = self::base64url(json_encode([
                    'iss' => $sa['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'iat' => $now,
                    'exp' => $now + 3600,
                ]));

                $privateKey = openssl_pkey_get_private($sa['private_key']);
                openssl_sign("{$header}.{$payload}", $signature, $privateKey, OPENSSL_ALGO_SHA256);
                $jwt = "{$header}.{$payload}." . self::base64url($signature);

                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('FCM OAuth failed', ['body' => $response->body()]);
                return null;
            } catch (\Exception $e) {
                Log::error('FCM OAuth exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    private static function base64url(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function getServiceAccount(): ?array
    {
        $path = config('services.fcm.service_account_path');
        if (!$path || !file_exists($path)) {
            Log::error('FCM service account not found: ' . ($path ?? 'null'));
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }
}
