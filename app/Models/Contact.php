<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'contact_user_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contactUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }

    /**
     * Make two users approved contacts of each other (both rows), replacing any pending request.
     */
    public static function connect(int $userId, int $otherUserId): void
    {
        static::updateOrCreate(['user_id' => $userId, 'contact_user_id' => $otherUserId], ['status' => 'approved']);
        static::updateOrCreate(['user_id' => $otherUserId, 'contact_user_id' => $userId], ['status' => 'approved']);
    }

    /**
     * Find users to add by name or exact friend code. Approved contacts and the user are hidden;
     * users with a pending request stay visible with its direction in `request_status`
     * (`sent`, `received`, or null).
     */
    public static function searchUsersFor(int $userId, string $query, int $limit = 20): Collection
    {
        $approvedContactIds = static::where('user_id', $userId)
            ->where('status', 'approved')
            ->pluck('contact_user_id')
            ->push($userId)
            ->all();

        $sentRequestIds = static::where('user_id', $userId)
            ->where('status', 'pending')
            ->pluck('contact_user_id')
            ->all();

        $receivedRequestIds = static::where('contact_user_id', $userId)
            ->where('status', 'pending')
            ->pluck('user_id')
            ->all();

        return User::registered()->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('friend_code', strtoupper($query));
            })
            ->whereNotIn('id', $approvedContactIds)
            ->select('id', 'name', 'friend_code', 'avatar')
            ->limit($limit)
            ->get()
            ->each(function (User $user) use ($sentRequestIds, $receivedRequestIds) {
                $user->request_status = match (true) {
                    in_array($user->id, $sentRequestIds) => 'sent',
                    in_array($user->id, $receivedRequestIds) => 'received',
                    default => null,
                };
            });
    }
}
