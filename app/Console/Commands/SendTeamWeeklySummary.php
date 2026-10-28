<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use App\Notifications\TeamWeeklySummary;
use App\Services\TeamBuilder\TeamStatsService;
use Illuminate\Console\Command;

class SendTeamWeeklySummary extends Command
{
    protected $signature = 'team-builder:weekly-summary';

    protected $description = 'Send each Team Builder upline a short update about the people below them';

    public function handle(TeamStatsService $stats): int
    {
        $count = 0;

        // Only members with someone below them have anything to report.
        $uplines = TeamMember::with(['team', 'user'])
            ->whereIn('user_id', TeamMember::whereNotNull('sponsor_id')->select('sponsor_id'))
            ->cursor();

        foreach ($uplines as $member) {
            if (!$member->user || !$member->team || $member->user->banned_at) {
                continue;
            }

            $list = $stats->downlineSummaries($member);
            if ($list->isEmpty()) {
                continue;
            }

            $member->user->notify(new TeamWeeklySummary($member->team_id, $member->team->name, [
                'total' => $list->count(),
                'push' => $list->where('flag', 'push')->count(),
                'training' => $list->where('flag', 'training')->count(),
                'overdue' => $list->sum('overdue_follow_ups'),
            ]));
            $count++;
        }

        $this->info("Sent team summary to {$count} upline(s).");

        return self::SUCCESS;
    }
}
