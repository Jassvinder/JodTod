<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentUrl = computed(() => usePage().url);

const navigation = [
    { name: 'Home', route: 'dashboard', icon: 'home' },
    { name: 'Expenses', route: 'expenses.index', icon: 'wallet', routePrefix: 'expenses' },
    { name: 'Income', route: 'incomes.index', icon: 'income', routePrefix: 'incomes' },
    { name: 'Groups', route: 'groups.index', icon: 'users', routePrefix: 'groups' },
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
                <!-- Income Icon -->
                <svg v-else-if="item.icon === 'income'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Users Icon -->
                <svg v-else-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <!-- User Icon -->
                <svg v-else-if="item.icon === 'user'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-xs">{{ item.name }}</span>
            </Link>
        </div>
    </nav>
</template>
