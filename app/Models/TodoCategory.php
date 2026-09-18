<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TodoCategory extends Model
{
    private const DEFAULT_CATEGORIES = [
        ['name' => 'Home', 'color' => '#6366f1'],
        ['name' => 'Market', 'color' => '#22c55e'],
    ];

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Seed the shared default categories (Home, Market) for a user the first
     * time they're needed. Safe to call on every listing request.
     */
    public static function ensureDefaultsForUser(int $userId): void
    {
        foreach (self::DEFAULT_CATEGORIES as $category) {
            static::firstOrCreate(
                ['user_id' => $userId, 'name' => $category['name']],
                [...$category, 'user_id' => $userId, 'is_default' => true],
            );
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }
}
