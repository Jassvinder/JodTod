<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentUrl = computed(() => usePage().url);
const unreadCount = computed(() => usePage().props.unread_notifications_count || 0);

const navigation = [
    { name: 'Home', route: 'dashboard', icon: 'home' },
    { name: 'Expenses', route: 'expenses.index', icon: 'wallet', routePrefix: 'expenses' },
    { name: 'Groups', route: 'groups.index', icon: 'users', routePrefix: 'groups' },
    { name: 'Alerts', route: 'notifications.index', icon: 'bell', routePrefix: 'notifications' },
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
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-around h-16">
            <Link
                v-for="item in navigation"
                :key="item.name"
                :href="getHref(item)"
                :class="[
                    'flex flex-col items-center justify-center gap-1 px-3 py-2 transition-colors',
                    isActive(item) ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400'
                ]"
            >
                <!-- Home Icon -->
                <svg v-if="item.icon === 'home'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z" />
                </svg>
                <!-- Wallet Icon -->
                <svg v-else-if="item.icon === 'wallet'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <!-- Users Icon -->
                <svg v-else-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <!-- Bell Icon -->
                <div v-else-if="item.icon === 'bell'" class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1.5 -right-2 flex items-center justify-center min-w-[16px] h-4 px-1 text-[10px] font-bold text-white bg-accent-500 rounded-full"
                    >
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </div>
                <!-- User Icon -->
                <svg v-else-if="item.icon === 'user'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-xs">{{ item.name }}</span>
            </Link>
        </div>
    </nav>
</template>
