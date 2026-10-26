<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Team Builder: one network marketing team (any company), with an upline/downline tree of members.
 */
class Team extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'company_name',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function memberFor(int $userId): ?TeamMember
    {
        return $this->members()->where('user_id', $userId)->first();
    }
}
