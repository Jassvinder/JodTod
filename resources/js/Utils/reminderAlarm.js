import axios from 'axios';
import Swal from 'sweetalert2';

let pollInterval = null;
const POLL_INTERVAL_MS = 60_000; // 60 seconds
const LOCK_KEY = 'jodtod_reminder_lock';
const LOCK_DURATION_MS = 30_000; // 30 second lock window for multi-tab

/**
 * Play a pleasant notification chime using Web Audio API.
 * No external files needed — works everywhere.
 */
function playAlarmSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();

        const playTone = (freq, startTime, duration, volume = 0.25) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = freq;
            osc.type = 'sine';
            gain.gain.setValueAtTime(volume, ctx.currentTime + startTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + startTime + duration);
            osc.start(ctx.currentTime + startTime);
            osc.stop(ctx.currentTime + startTime + duration);
        };

        // Two-chime pattern (plays twice for attention)
        const chime = (offset) => {
            playTone(659.25, offset, 0.15);       // E5
            playTone(783.99, offset + 0.15, 0.15); // G5
            playTone(987.77, offset + 0.3, 0.3);   // B5
        };

        chime(0);
        chime(0.7);
    } catch {
        // Audio not supported — fail silently
    }
}

/**
 * Show browser notification (works when tab is in background).
 */
function showBrowserNotification(reminders) {
    if (!('Notification' in window) || Notification.permission !== 'granted') return;

    const title = reminders.length === 1
        ? `Reminder: ${reminders[0].title}`
        : `You have ${reminders.length} task reminders`;

    const body = reminders.length === 1
        ? `Priority: ${reminders[0].priority.charAt(0).toUpperCase() + reminders[0].priority.slice(1)}`
        : reminders.map(r => `• ${r.title}`).join('\n');

    const notification = new Notification(title, {
        body,
        icon: '/icons/icon-192x192.png',
        tag: 'todo-reminder',
        renotify: true,
    });

    notification.onclick = () => {
        window.focus();
        window.location.href = '/todos';
        notification.close();
    };
}

/**
 * Show SweetAlert popup with reminder details.
 */
function showReminderPopup(reminders) {
    const isDark = document.documentElement.classList.contains('dark');

    const priorityBadge = (p) => {
        const colors = { high: '#dc2626', medium: '#d97706', low: '#16a34a' };
        return `<span style="display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;color:white;background:${colors[p] || colors.medium}">${p.toUpperCase()}</span>`;
    };

    let html;
    if (reminders.length === 1) {
        const r = reminders[0];
        const dueLine = r.due_date
            ? `<p style="margin-top:6px;font-size:13px;color:${isDark ? '#9ca3af' : '#6b7280'}">Due: ${new Date(r.due_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })}</p>`
            : '';
        html = `<div style="text-align:center">
            <p style="font-size:16px;font-weight:600;margin-bottom:6px;color:${isDark ? '#f3f4f6' : '#1f2937'}">${r.title}</p>
            ${priorityBadge(r.priority)}
            ${dueLine}
        </div>`;
    } else {
        const items = reminders.map(r => {
            const due = r.due_date ? ` — ${new Date(r.due_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })}` : '';
            return `<div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid ${isDark ? '#374151' : '#f3f4f6'}">
                <span style="flex:1;font-size:14px;text-align:left;color:${isDark ? '#e5e7eb' : '#374151'}">${r.title}${due}</span>
                ${priorityBadge(r.priority)}
            </div>`;
        }).join('');
        html = `<div>${items}</div>`;
    }

    Swal.fire({
        title: reminders.length === 1 ? 'Task Reminder' : `${reminders.length} Task Reminders`,
        html,
        icon: 'info',
        iconColor: '#8b5cf6',
        confirmButtonText: 'View Tasks',
        showCancelButton: true,
        cancelButtonText: 'Dismiss',
        confirmButtonColor: '#6366f1',
        cancelButtonColor: isDark ? '#4b5563' : '#9ca3af',
        background: isDark ? '#1f2937' : '#ffffff',
        color: isDark ? '#f3f4f6' : '#1f2937',
        customClass: {
            popup: 'rounded-xl shadow-xl',
            confirmButton: 'rounded-lg px-5 py-2.5 text-sm font-semibold',
            cancelButton: 'rounded-lg px-5 py-2.5 text-sm font-semibold',
        },
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/todos';
        }
    });
}

/**
 * Acquire cross-tab lock using localStorage.
 * Returns true if this tab should handle the alarm.
 */
function acquireLock() {
    const now = Date.now();
    const lastLock = parseInt(localStorage.getItem(LOCK_KEY) || '0', 10);

    if (now - lastLock < LOCK_DURATION_MS) {
        return false; // Another tab handled it recently
    }

    localStorage.setItem(LOCK_KEY, now.toString());
    return true;
}

/**
 * Check for due reminders and fire alarm.
 */
async function checkReminders() {
    // Don't check if we're offline
    if (!navigator.onLine) return;

    // Multi-tab lock
    if (!acquireLock()) return;

    try {
        const { data: reminders } = await axios.get('/todos/check-reminders');

        if (!reminders || reminders.length === 0) return;

        // Fire alarm
        playAlarmSound();
        showReminderPopup(reminders);
        showBrowserNotification(reminders);
    } catch {
        // Silently fail — will retry next interval
    }
}

/**
 * Request browser notification permission (called once on first user interaction).
 */
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

/**
 * Start the reminder alarm polling.
 * Call this in AppLayout onMounted.
 */
export function startReminderAlarm() {
    // Ask for browser notification permission
    requestNotificationPermission();

    // Initial check after 5 seconds (give page time to load)
    setTimeout(checkReminders, 5000);

    // Poll every 60 seconds
    pollInterval = setInterval(checkReminders, POLL_INTERVAL_MS);
}

/**
 * Stop the reminder alarm polling.
 * Call this in AppLayout onUnmounted.
 */
export function stopReminderAlarm() {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}
