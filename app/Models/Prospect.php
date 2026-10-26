<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Team Builder: a person the user is presenting the business plan to.
 * Uplines may see a downline member's prospect names, statuses and follow-up dates
 * (to ask "why no follow-up with Rahul?"), but never the phone number or notes.
 */
class Prospect extends Model
{
    public const STATUSES = ['new', 'plan_shown', 'follow_up', 'joined', 'not_interested'];

    // Prospects that still need follow-ups.
    public const OPEN_STATUSES = ['new', 'plan_shown', 'follow_up'];

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'phone',
        'source',
        'status',
        'next_follow_up_at',
        'joined_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'datetime',
            'joined_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Prospect $prospect) {
            if (!$prospect->isDirty('status')) {
                return;
            }
            $prospect->joined_at = $prospect->status === 'joined' ? ($prospect->joined_at ?? now()) : null;
            // Closed prospects need no more follow-ups.
            if (!in_array($prospect->status, self::OPEN_STATUSES, true)) {
                $prospect->next_follow_up_at = null;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProspectActivity::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'source' => $this->source,
            'status' => $this->status,
            'team_id' => $this->team_id,
            'team_name' => $this->team?->name,
            'next_follow_up_at' => $this->next_follow_up_at?->toIso8601String(),
            'joined_at' => $this->joined_at?->toIso8601String(),
            'note' => $this->note,
            // Present only when the query used withMax('activities', 'done_at').
            'last_activity_at' => $this->activities_max_done_at
                ? Carbon::parse($this->activities_max_done_at)->toIso8601String()
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    public function toDetail(): array
    {
        return [
            ...$this->toSummary(),
            'activities' => $this->activities()
                ->orderByDesc('done_at')
                ->orderByDesc('id')
                ->get(['id', 'type', 'note', 'done_at'])
                ->map(fn (ProspectActivity $activity) => [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'note' => $activity->note,
                    'done_at' => $activity->done_at->toIso8601String(),
                ]),
        ];
    }
}
