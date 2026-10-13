// Date helpers for date-only values (YYYY-MM-DD). They are parsed as local dates to avoid timezone shifts.

function parseDateOnly(value) {
    const [year, month, day] = String(value).slice(0, 10).split('-').map(Number);
    return new Date(year, month - 1, day);
}

// Today's date in the user's timezone as YYYY-MM-DD (for <input type="date"> values and max).
export function todayISO() {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${month}-${day}`;
}

// "12 Oct 2026"
export function formatShortDate(value) {
    if (!value) return '';
    return parseDateOnly(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

// "Today", "Yesterday", "5 days ago"
export function formatDaysAgo(value) {
    if (!value) return '';
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const days = Math.round((today - parseDateOnly(value)) / 86400000);
    if (days <= 0) return 'Today';
    if (days === 1) return 'Yesterday';
    return `${days} days ago`;
}
