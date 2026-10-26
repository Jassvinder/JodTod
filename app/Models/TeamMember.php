<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'sponsor_id',
        'invite_code',
        'path',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TeamMember $member) {
            if (empty($member->invite_code)) {
                $member->invite_code = self::generateInviteCode();
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Path for a member joining under this one.
     */
    public function childPath(int $userId): string
    {
        return $this->path . $userId . '/';
    }

    /**
     * Everyone below this member in the same team, at any depth.
     */
    public function scopeDownlineOf(Builder $query, TeamMember $member): Builder
    {
        return $query->where('team_id', $member->team_id)
            ->where('path', 'like', $member->path . '%')
            ->where('user_id', '!=', $member->user_id);
    }

    public function directDownline(): Builder
    {
        return self::where('team_id', $this->team_id)->where('sponsor_id', $this->user_id);
    }

    /**
     * Levels below the top of the tree (the owner is level 0).
     */
    public function depth(): int
    {
        return substr_count(trim($this->path, '/'), '/');
    }
}
