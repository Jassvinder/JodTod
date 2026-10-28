<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's weekly Team Builder target. The latest one saved wins, whether the user or an upline set it.
 */
class TeamBuilderTarget extends Model
{
    // Same rules whether the user or an upline sets the target. 0 means no target.
    public const RULES = [
        'plans_per_week' => 'required|integer|min:0|max:500',
        'follow_ups_per_week' => 'required|integer|min:0|max:500',
    ];

    protected $fillable = [
        'user_id',
        'set_by',
        'plans_per_week',
        'follow_ups_per_week',
    ];

    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public static function toPayload(?self $target, int $forUserId): ?array
    {
        if (!$target) {
            return null;
        }

        return [
            'plans_per_week' => $target->plans_per_week,
            'follow_ups_per_week' => $target->follow_ups_per_week,
            // Null when the user set it themselves.
            'set_by_name' => $target->set_by && $target->set_by !== $forUserId ? $target->setBy?->name : null,
        ];
    }
}
