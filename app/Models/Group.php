<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'invite_code',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            if (empty($group->invite_code)) {
                $group->invite_code = self::generateInviteCode();
            }
        });
    }

    public static function generateInviteCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('invite_code', $code)->exists());

        return $code;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->orderByPivot('joined_at');
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function isAdmin(User $user): bool
    {
        return $this->groupMembers()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->groupMembers()
            ->where('user_id', $user->id)
            ->exists();
    }
}
