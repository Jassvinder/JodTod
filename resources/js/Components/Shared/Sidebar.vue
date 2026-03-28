<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    open: Boolean,
    collapsed: Boolean,
});

defineEmits(['close', 'toggle-collapse']);

const currentUrl = computed(() => usePage().url);

const unreadCount = computed(() => usePage().props.unread_notifications_count || 0);
const isAdmin = computed(() => usePage().props.auth?.user?.role === 'admin');

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home' },
    { name: 'Expenses', route: 'expenses.index', icon: 'wallet', routePrefix: 'expenses' },
    { name: 'Income', route: 'incomes.index', icon: 'income', routePrefix: 'incomes' },
    { name: 'My Tasks', route: 'todos.index', icon: 'tasks', routePrefix: 'todos' },
    { name: 'Contacts', route: 'contacts.index', icon: 'contacts', routePrefix: 'contacts' },
    { name: 'Groups', route: 'groups.index', icon: 'users', routePrefix: 'groups' },
    { name: 'Notifications', route: 'notifications.index', icon: 'bell', routePrefix: 'notifications' },
    { name: 'Profile', route: 'profile.edit', icon: 'user' },
];

function getHref(item) {
    try {
        return route(item.route);
    } catch {
        return item.href || '#';
    }
}

function isActive(item) {
    try {
        if (item.routePrefix) {
            return route().current(item.routePrefix + '.*');
        }
        return route().current(item.route);
    } catch {
        return false;
    }
}
</script>

<template>
    <!-- Mobile Overlay -->
    <div
        v-if="open"
        class="fixed inset-0 z-50 bg-black/50 lg:hidden"
        @click="$emit('close')"
    />

    <!-- Sidebar -->
    <aside
        :class="[
            'fixed top-0 left-0 z-50 h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-300 ease-in-out',
            collapsed ? 'w-[68px]' : 'w-64',
            open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <!-- Logo + Collapse Toggle -->
        <div class="flex items-center justify-between h-16 border-b border-gray-200 dark:border-gray-700" :class="collapsed ? 'px-3' : 'px-6'">
            <Link :href="route('dashboard')" :class="collapsed ? 'mx-auto' : ''">
                <img src="/images/Logo/logo.png" alt="JodTod" :class="collapsed ? 'h-7 w-auto' : 'h-9 w-auto'" class="transition-all duration-300" />
            </Link>
            <button
                @click="$emit('toggle-collapse')"
                class="hidden lg:flex items-center justify-center w-7 h-7 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :class="collapsed ? 'absolute -right-3 top-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm z-10 rounded-full w-6 h-6' : ''"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="p-2 space-y-1" :class="collapsed ? 'px-2' : 'p-4'">
            <Link
                v-for="item in navigation"
                :key="item.name"
                :href="getHref(item)"
                :class="[
                    'flex items-center rounded-lg transition-colors relative group',
                    collapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3',
                    isActive(item)
                        ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium'
                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                ]"
                @click="$emit('close')"
                :title="collapsed ? item.name : ''"
            >
                <!-- Home Icon -->
                <svg v-if="item.icon === 'home'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z" />
                </svg>
                <!-- Wallet Icon -->
                <svg v-else-if="item.icon === 'wallet'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <!-- Income Icon -->
                <svg v-else-if="item.icon === 'income'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Tasks Icon -->
                <svg v-else-if="item.icon === 'tasks'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <!-- Contacts Icon -->
                <svg v-else-if="item.icon === 'contacts'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <!-- Users Icon -->
                <svg v-else-if="item.icon === 'users'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <!-- Bell Icon -->
                <svg v-else-if="item.icon === 'bell'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- User Icon -->
                <svg v-else-if="item.icon === 'user'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span v-if="!collapsed" class="flex-1 whitespace-nowrap">{{ item.name }}</span>
                <!-- Notification badge -->
                <span
                    v-if="item.icon === 'bell' && unreadCount > 0"
                    :class="collapsed
                        ? 'absolute -top-1 -right-1 min-w-[16px] h-4 px-1 text-[10px]'
                        : 'ml-auto min-w-[20px] h-5 px-1.5 text-xs'"
                    class="inline-flex items-center justify-center font-bold text-white bg-accent-500 rounded-full"
                >
                    {{ collapsed ? (unreadCount > 9 ? '9+' : unreadCount) : (unreadCount > 99 ? '99+' : unreadCount) }}
                </span>
                <!-- Tooltip for collapsed state -->
                <div
                    v-if="collapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50"
                >
                    {{ item.name }}
                </div>
            </Link>

            <!-- Admin Panel Link -->
            <template v-if="isAdmin">
                <div class="my-3 border-t border-gray-200 dark:border-gray-700"></div>
                <Link
                    :href="route('admin.dashboard')"
                    :class="[
                        'flex items-center rounded-lg transition-colors text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 font-medium relative group',
                        collapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3',
                    ]"
                    @click="$emit('close')"
                    :title="collapsed ? 'Admin Panel' : ''"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span v-if="!collapsed">Admin Panel</span>
                    <div
                        v-if="collapsed"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50"
                    >
                        Admin Panel
                    </div>
                </Link>
            </template>
        </nav>
    </aside>
</template>
