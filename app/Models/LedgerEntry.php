<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
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
