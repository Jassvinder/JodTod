import { reactive } from 'vue';

// Onboarding copy (kept in sync with the mobile app's constants/tips.ts) and per-user "seen" state.

export const WELCOME_SLIDES = [
    { icon: 'wallet', tone: 'primary', title: 'Track every rupee', text: 'Add expenses and income, see where your money goes, and how much you save each month.' },
    { icon: 'users', tone: 'success', title: 'Split bills with friends', text: 'Create a group for a trip or flat, add shared expenses, and JodTod tells everyone who owes whom.' },
    { icon: 'tasks', tone: 'amber', title: 'Plan tasks and reminders', text: 'Add tasks with reminders, plan events months ahead, and assign tasks to friends.' },
    { icon: 'bookmark', tone: 'pink', title: 'Remember the little things', text: 'Log when you last changed the gas cylinder or remote batteries, and see how often it happens.' },
];

// Each tip points at an element with a matching data-tip attribute. Tips whose element is not
// visible (for example the sidebar on a phone) are skipped.
export const PAGE_TIPS = {
    dashboard: [
        { anchor: 'dashboard.summary', title: 'Your month at a glance', text: 'Spending, income, savings and group balances. Click a card title to open its details.' },
        { anchor: 'dashboard.actions', title: 'Quick actions', text: 'Add an expense, a group expense or a new group straight from here.' },
        { anchor: 'header.notifications', title: 'Notifications', text: 'Contact requests, task reminders and group updates show up here.' },
        { anchor: 'sidebar.nav', title: 'Everything else', text: 'Contacts, Remember, Help & Tips and every other section are in this menu.' },
    ],
    expenses: [
        { anchor: 'expenses.add', title: 'Add an expense', text: 'Record what you spent and pick a category, so the dashboard can show where your money goes.' },
        { anchor: 'expenses.filters', title: 'Find expenses', text: 'Search by description, or filter by category and time period.' },
    ],
    incomes: [
        { anchor: 'incomes.add', title: 'Add income', text: 'Add salary or any other income. Savings on the dashboard is income minus expenses.' },
    ],
    groups: [
        { anchor: 'groups.create', title: 'Create a group', text: 'Make a group for a trip, flat or event, add friends from your contacts, and split bills. JodTod works out who owes whom.' },
        { anchor: 'groups.join', title: 'Join a group', text: 'Got an invite code from a friend? Enter it here to join their group.' },
    ],
    todos: [
        { anchor: 'todos.add', title: 'Add a task', text: 'Set a due date and a reminder. You can also assign the task to one of your contacts.' },
        { anchor: 'todos.filters', title: 'Filter tasks', text: 'Show pending or completed tasks, or narrow them down by priority and category.' },
    ],
    contacts: [
        { anchor: 'contacts.code', title: 'Your friend code', text: 'Share this code with friends so they can find you quickly. Click it to copy.' },
        { anchor: 'contacts.search', title: 'Add a contact', text: 'Find friends by name or friend code and send a request. Once they approve, you can add them to groups and assign them tasks.' },
        { anchor: 'contacts.requests', title: 'Requests', text: 'Requests you received and the ones you sent are here.' },
    ],
    remember: [
        { anchor: 'remember.add', title: 'Remember something', text: 'Save things like when you changed the gas cylinder. Add a date each time it happens, and JodTod shows how often it usually does.' },
    ],
};

export const HELP_TOPICS = [
    { icon: 'home', title: 'Dashboard', text: 'This month\'s spending, income, savings, group balances, pending payments and recent activity in one place.', route: 'dashboard' },
    { icon: 'wallet', title: 'Expenses', text: 'Record personal expenses with a category and date. Search and filter them any time.', route: 'expenses.index' },
    { icon: 'income', title: 'Income', text: 'Record salary and other income. Savings = income minus expenses.', route: 'incomes.index' },
    { icon: 'users', title: 'Groups', text: 'Create or join a group, add shared expenses, and settle up. JodTod calculates who owes whom.', route: 'groups.index' },
    { icon: 'tasks', title: 'Tasks', text: 'Tasks with due dates, priorities, categories and reminders. Assign tasks to your contacts.', route: 'todos.index' },
    { icon: 'user-plus', title: 'Contacts', text: 'Friends you split bills and share tasks with. Find them by name or friend code; they join your list after approving.', route: 'contacts.index' },
    { icon: 'bookmark', title: 'Remember', text: 'Log when recurring things happened, like a gas cylinder change, and see the usual gap between them.', route: 'remember.index' },
    { icon: 'bell', title: 'Notifications', text: 'Contact requests, task reminders, and group expense and settlement updates.', route: 'notifications.index' },
];

const STORAGE_PREFIX = 'jodtod_tips_v1_';

// Shared UI state: which tour is running and whether the welcome slides are open.
export const onboarding = reactive({
    userId: null,
    introOpen: false,
    tourPage: null,
    // A page that asked for its tips while the welcome slides were open; started after they close.
    pendingPage: null,
});

function storageKey() {
    return STORAGE_PREFIX + (onboarding.userId ?? 'guest');
}

function readState() {
    try {
        const parsed = JSON.parse(localStorage.getItem(storageKey()) || '{}');
        return { introSeen: !!parsed.introSeen, seenPages: Array.isArray(parsed.seenPages) ? parsed.seenPages : [] };
    } catch {
        return { introSeen: false, seenPages: [] };
    }
}

function writeState(state) {
    try {
        localStorage.setItem(storageKey(), JSON.stringify(state));
    } catch {
        // Storage can be unavailable (private mode); onboarding then simply shows again next time.
    }
}

export function initOnboarding(userId) {
    onboarding.userId = userId;
    if (!readState().introSeen) {
        onboarding.introOpen = true;
    }
}

export function openIntro() {
    onboarding.tourPage = null;
    onboarding.introOpen = true;
}

export function closeIntro() {
    writeState({ ...readState(), introSeen: true });
    onboarding.introOpen = false;
    if (onboarding.pendingPage) {
        const page = onboarding.pendingPage;
        onboarding.pendingPage = null;
        startTour(page);
    }
}

export function hasSeenPage(page) {
    return readState().seenPages.includes(page);
}

export function startTour(page) {
    if (!PAGE_TIPS[page]) return;
    if (onboarding.introOpen) {
        onboarding.pendingPage = page;
        return;
    }
    onboarding.tourPage = page;
}

// Called by a page's help button on mount: shows the page's tips once.
export function maybeStartTour(page) {
    if (!hasSeenPage(page)) {
        startTour(page);
    }
}

export function finishTour() {
    const page = onboarding.tourPage;
    onboarding.tourPage = null;
    if (!page) return;
    const state = readState();
    if (!state.seenPages.includes(page)) {
        writeState({ ...state, seenPages: [...state.seenPages, page] });
    }
}

// "Show All Tips Again": every page shows its tips on the next visit.
export function resetTips() {
    writeState({ ...readState(), seenPages: [] });
}
