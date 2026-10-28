<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    // Shared by the API and web controllers.
    public const RULES = [
        'type' => 'required|in:gave,got',
        'amount' => 'required|numeric|min:0.01|max:9999999999.99',
        'entry_date' => 'required|date|before_or_equal:today',
        'note' => 'nullable|string|max:255',
    ];

    protected $fillable = [
        'ledger_person_id',
        'type',
        'amount',
        'entry_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'entry_date' => 'date:Y-m-d',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LedgerPerson::class, 'ledger_person_id');
    }
}
