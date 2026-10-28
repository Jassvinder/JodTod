<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamAnnouncement extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'message',
        'link',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Announcements a member can read: posted in their team by themselves or anyone above them.
     */
    public function scopeVisibleTo(Builder $query, TeamMember $member): Builder
    {
        return $query->where('team_id', $member->team_id)->whereIn('user_id', $member->pathUserIds());
    }

    public function toPayload(int $viewerId): array
    {
        return [
            'id' => $this->id,
            'team_id' => $this->team_id,
            'team_name' => $this->team?->name,
            'message' => $this->message,
            'link' => $this->link,
            'author_name' => $this->author?->name,
            'author_avatar_url' => $this->author?->avatar_url,
            'is_mine' => $this->user_id === $viewerId,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
