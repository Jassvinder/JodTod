// Team Builder labels and colors, same as the mobile app (constants/teamBuilder.ts).
// Literal class names so Tailwind generates them.

export const PROSPECT_STATUSES = ['new', 'plan_shown', 'follow_up', 'joined', 'not_interested'];

// Statuses that still need follow-ups (backend Prospect::OPEN_STATUSES).
export const OPEN_PROSPECT_STATUSES = ['new', 'plan_shown', 'follow_up'];

export const PROSPECT_STATUS_META = {
    new: { label: 'New', badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', dot: 'bg-blue-500' },
    plan_shown: { label: 'Plan Shown', badge: 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300', dot: 'bg-violet-500' },
    follow_up: { label: 'Follow-up', badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', dot: 'bg-amber-500' },
    joined: { label: 'Joined', badge: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300', dot: 'bg-green-500' },
    not_interested: { label: 'Not Interested', badge: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300', dot: 'bg-gray-400' },
};

export const ACTIVITY_TYPES = ['call', 'plan_shown', 'follow_up', 'meeting', 'note'];

export const ACTIVITY_TYPE_META = {
    call: { label: 'Call', icon: 'phone' },
    plan_shown: { label: 'Plan Shown', icon: 'chart' },
    follow_up: { label: 'Follow-up', icon: 'refresh' },
    meeting: { label: 'Meeting', icon: 'users' },
    note: { label: 'Note', icon: 'pencil' },
};

// Same rules as the backend's TeamStatsService.
export const MEMBER_FLAG_META = {
    push: {
        label: 'Push needed',
        text: 'No call, plan, follow-up or meeting logged in the last 7 days.',
        icon: 'lightning',
        badge: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    },
    training: {
        label: 'Training needed',
        text: 'Showed 5 or more plans in the last 30 days, but nobody joined.',
        icon: 'academic',
        badge: 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
    },
};

// "28 Oct, 4:30 pm"
export function formatDateTime(value) {
    if (!value) return '';
    return new Date(value).toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true });
}

// ISO date-time -> "YYYY-MM-DDTHH:MM" in local time for <input type="datetime-local">.
export function toLocalInput(value) {
    if (!value) return '';
    const date = new Date(value);
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

export function isOverdue(prospect) {
    return !!prospect.next_follow_up_at
        && OPEN_PROSPECT_STATUSES.includes(prospect.status)
        && new Date(prospect.next_follow_up_at).getTime() < Date.now();
}

// Page for a Team Builder notification (header dropdown and Notifications page), or null.
export function teamBuilderNotificationLink(data) {
    const teamId = data?.team_id;
    switch (data?.type) {
        case 'team_member_joined':
        case 'team_weekly_summary':
        case 'team_announcement':
            return teamId ? route('team-builder.teams.show', teamId) : route('team-builder.dashboard');
        case 'team_target_set':
            return route('team-builder.dashboard');
        default:
            return null;
    }
}
