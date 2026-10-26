<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectActivity extends Model
{
    public const TYPES = ['call', 'plan_shown', 'follow_up', 'meeting', 'note'];

    protected $fillable = [
        'prospect_id',
        'type',
        'note',
        'done_at',
    ];

    protected function casts(): array
    {
        return [
            'done_at' => 'datetime',
        ];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }
}
