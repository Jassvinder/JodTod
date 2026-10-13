<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class RememberItem extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RememberEntry::class);
    }

    /**
     * Load the entry count and first/last dates needed by toSummary().
     */
    public function scopeWithEntryStats(Builder $query): Builder
    {
        return $query->withCount('entries')
            ->withMin('entries', 'done_on')
            ->withMax('entries', 'done_on');
    }

    /**
     * Item summary: last done date and the average number of days between entries.
     * Expects the withEntryStats() aggregates to be loaded.
     */
    public function toSummary(): array
    {
        $count = (int) $this->entries_count;
        $first = $this->entries_min_done_on ? Carbon::parse($this->entries_min_done_on) : null;
        $last = $this->entries_max_done_on ? Carbon::parse($this->entries_max_done_on) : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'entries_count' => $count,
            'last_done_on' => $last?->toDateString(),
            'avg_gap_days' => $count > 1 ? (int) round($first->diffInDays($last) / ($count - 1)) : null,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Summary plus the full date history, newest first. Reloads the stats so they reflect recent changes.
     */
    public function toDetail(): array
    {
        $item = static::whereKey($this->id)->withEntryStats()->firstOrFail();

        return [
            ...$item->toSummary(),
            'entries' => $item->entries()
                ->orderByDesc('done_on')
                ->orderByDesc('id')
                ->get(['id', 'done_on', 'note']),
        ];
    }
}
