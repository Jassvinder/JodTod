<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'icon'];

    /**
     * Create any missing default expense categories from config('site.categories').
     * Existing rows (matched by name) are left untouched.
     */
    public static function ensureDefaults(): void
    {
        foreach (config('site.categories', []) as $category) {
            static::firstOrCreate(
                ['name' => $category['name']],
                ['icon' => $category['icon'] ?? null],
            );
        }
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
