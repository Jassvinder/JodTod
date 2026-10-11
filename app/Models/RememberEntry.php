<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RememberEntry extends Model
{
    protected $fillable = [
        'remember_item_id',
        'done_on',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'done_on' => 'date:Y-m-d',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RememberItem::class, 'remember_item_id');
    }
}
