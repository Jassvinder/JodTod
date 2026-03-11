<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    open: Boolean,
});

defineEmits(['close']);

const currentUrl = computed(() => usePage().url);

const unreadCount = computed(() => usePage().props.unread_notifications_count || 0);
const isAdmin = computed(() => usePage().props.auth?.user?.role === 'admin');

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home' },
    { name: 'Expenses', route: 'expenses.index', icon: 'wallet', routePrefix: 'expenses' },
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
            'fixed top-0 left-0 z-50 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200',
            open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <!-- Logo -->
        <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
            <Link :href="route('dashboard')" class="text-xl font-bold text-primary-600 dark:text-primary-400">JodTod</Link>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1">
            <Link
                v-for="item in navigation"
                :key="item.name"
                :href="getHref(item)"
                :class="[
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition-colors',
                    isActive(item)
                        ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium'
                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                ]"
                @click="$emit('close')"
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
                <svg v-else-if="item.icon === 'bell'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- User Icon -->
                <svg v-else-if="item.icon === 'user'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="flex-1">{{ item.name }}</span>
                <!-- Notification badge in sidebar -->
                <span
                    v-if="item.icon === 'bell' && unreadCount > 0"
                    class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-accent-500 rounded-full"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
            </Link>

            <!-- Admin Panel Link (only for admin users) -->
            <template v-if="isAdmin">
                <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>
                <Link
                    :href="route('admin.dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 font-medium"
                    @click="$emit('close')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Admin Panel</span>
                </Link>
            </template>
        </nav>
    </aside>
</template>
