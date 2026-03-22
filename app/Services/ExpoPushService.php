<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send push notification to a specific user.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        self::send($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to multiple users.
     */
    public static function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        self::send($tokens, $title, $body, $data);
    }

    /**
     * Send push notifications to Expo Push API.
     */
    private static function send(array $tokens, string $title, string $body, array $data = []): void
    {
        $messages = [];

        foreach ($tokens as $token) {
            // Only send to valid Expo push tokens
            if (!str_starts_with($token, 'ExponentPushToken[') && !str_starts_with($token, 'ExpoPushToken[')) {
                continue;
            }

            $messages[] = [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'priority' => 'high',
                'channelId' => 'default',
            ];
        }

        if (empty($messages)) {
            return;
        }

        try {
            // Expo accepts batches of up to 100
            $chunks = array_chunk($messages, 100);

            foreach ($chunks as $chunk) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post(self::EXPO_PUSH_URL, $chunk);

                if ($response->failed()) {
                    Log::warning('Expo Push failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }

                // Handle invalid tokens - remove them
                if ($response->successful()) {
                    $results = $response->json('data') ?? [];
                    foreach ($results as $i => $result) {
                        if (($result['status'] ?? '') === 'error') {
                            $errorType = $result['details']['error'] ?? '';
                            if ($errorType === 'DeviceNotRegistered') {
                                DeviceToken::where('token', $chunk[$i]['to'])->delete();
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Expo Push exception: ' . $e->getMessage());
        }
    }
}
