<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LedgerPerson extends Model
{
    protected $table = 'ledger_people';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    /**
     * Load the totals needed by toSummary().
     */
    public function scopeWithLedgerTotals(Builder $query): Builder
    {
        return $query->withCount('entries')
            ->withSum(['entries as total_gave' => fn ($q) => $q->where('type', 'gave')], 'amount')
            ->withSum(['entries as total_got' => fn ($q) => $q->where('type', 'got')], 'amount')
            ->withMax('entries', 'entry_date');
    }

    /**
     * Person summary. balance > 0: they owe me (I will get). balance < 0: I owe them (I will give).
     * Expects the withLedgerTotals() aggregates to be loaded.
     */
    public function toSummary(): array
    {
        $gave = round((float) $this->total_gave, 2);
        $got = round((float) $this->total_got, 2);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'note' => $this->note,
            'entries_count' => (int) $this->entries_count,
            'total_gave' => $gave,
            'total_got' => $got,
            'balance' => round($gave - $got, 2),
            'last_entry_on' => $this->entries_max_entry_date
                ? substr((string) $this->entries_max_entry_date, 0, 10)
                : null,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Summary plus every entry, newest first, each with the running balance after it.
     */
    public function toDetail(): array
    {
        $person = static::whereKey($this->id)->withLedgerTotals()->firstOrFail();

        $entries = $person->entries()
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get(['id', 'type', 'amount', 'entry_date', 'note']);

        $running = 0.0;
        $rows = $entries->map(function (LedgerEntry $entry) use (&$running) {
            $running = round($running + ($entry->type === 'gave' ? $entry->amount : -$entry->amount), 2);

            return [
                'id' => $entry->id,
                'type' => $entry->type,
                'amount' => $entry->amount,
                'entry_date' => $entry->entry_date->toDateString(),
                'note' => $entry->note,
                'balance_after' => $running,
            ];
        })->reverse()->values();

        return [
            ...$person->toSummary(),
            'entries' => $rows,
        ];
    }
}
