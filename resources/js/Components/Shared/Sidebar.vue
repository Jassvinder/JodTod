<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    open: Boolean,
});

defineEmits(['close']);

const currentUrl = computed(() => usePage().url);

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home' },
    { name: 'Expenses', route: 'expenses.index', icon: 'wallet', routePrefix: 'expenses' },
    { name: 'Groups', route: 'groups', icon: 'users', href: '/groups' },
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
            'fixed top-0 left-0 z-50 h-full w-64 bg-white border-r border-gray-200 transform transition-transform duration-200',
            open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <!-- Logo -->
        <div class="flex items-center h-16 px-6 border-b border-gray-200">
            <Link :href="route('dashboard')" class="text-xl font-bold text-primary-600">JodTod</Link>
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
                        ? 'bg-primary-50 text-primary-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
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
                <!-- User Icon -->
                <svg v-else-if="item.icon === 'user'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ item.name }}</span>
            </Link>
        </nav>
    </aside>
</template>
