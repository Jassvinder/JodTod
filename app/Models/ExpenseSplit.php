<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplit extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',
        'share_amount',
        'percentage',
        'is_settled',
    ];

    protected function casts(): array
    {
        return [
            'share_amount' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_settled' => 'boolean',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
