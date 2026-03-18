<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref, computed } from 'vue';

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const flash = computed(() => usePage().props.flash || {});
const user = computed(() => usePage().props.auth?.user);

import { onMounted } from 'vue';
onMounted(() => {
    sidebarCollapsed.value = localStorage.getItem('admin_sidebar_collapsed') === 'true';
});

function toggleCollapse() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('admin_sidebar_collapsed', sidebarCollapsed.value);
}

const navigation = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: 'chart' },
    { name: 'Users', route: 'admin.users', icon: 'users' },
    { name: 'Groups', route: 'admin.groups', icon: 'group' },
    { name: 'Categories', route: 'admin.categories', icon: 'tag' },
    { name: 'Pages', route: 'admin.pages', icon: 'page' },
    { name: 'Blog', route: 'admin.blog', icon: 'document' },
];

function getHref(item) {
    try {
        return route(item.route);
    } catch {
        return '#';
    }
}

function isActive(item) {
    try {
        return route().current(item.route);
    } catch {
        return false;
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile Overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-50 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed top-0 left-0 z-50 h-full bg-slate-800 transform transition-all duration-300 ease-in-out',
                sidebarCollapsed ? 'w-[68px]' : 'w-64',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Logo + Collapse Toggle -->
            <div class="flex items-center justify-between h-16 border-b border-slate-700" :class="sidebarCollapsed ? 'px-3' : 'px-6'">
                <Link :href="route('admin.dashboard')" :class="sidebarCollapsed ? 'mx-auto' : ''">
                    <img src="/images/Logo/logo.png" alt="JodTod" :class="sidebarCollapsed ? 'h-7 w-auto' : 'h-9 w-auto'" class="transition-all duration-300" />
                </Link>
                <span v-if="!sidebarCollapsed" class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-500 text-white rounded-md">Admin</span>
                <button
                    @click="toggleCollapse"
                    class="hidden lg:flex items-center justify-center w-7 h-7 rounded-md text-slate-400 hover:text-white hover:bg-slate-700 transition-colors"
                    :class="sidebarCollapsed ? 'absolute -right-3 top-5 bg-slate-800 border border-slate-600 shadow-sm z-10 rounded-full w-6 h-6' : ''"
                    :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                >
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="getHref(item)"
                    :class="[
                        'flex items-center rounded-lg transition-colors relative group',
                        sidebarCollapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3',
                        isActive(item)
                            ? 'bg-slate-700 text-white font-medium'
                            : 'text-slate-300 hover:bg-slate-700 hover:text-white'
                    ]"
                    @click="sidebarOpen = false"
                    :title="sidebarCollapsed ? item.name : ''"
                >
                    <!-- Chart Icon (Dashboard) -->
                    <svg v-if="item.icon === 'chart'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <!-- Users Icon -->
                    <svg v-else-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <!-- Group Icon -->
                    <svg v-else-if="item.icon === 'group'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <!-- Tag Icon (Categories) -->
                    <svg v-else-if="item.icon === 'tag'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <!-- Page Icon (Pages) -->
                    <svg v-else-if="item.icon === 'page'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <!-- Document Icon (Blog) -->
                    <svg v-else-if="item.icon === 'document'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span v-if="!sidebarCollapsed">{{ item.name }}</span>
                    <!-- Tooltip -->
                    <div
                        v-if="sidebarCollapsed"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50"
                    >
                        {{ item.name }}
                    </div>
                </Link>

                <!-- Divider -->
                <div class="my-3 border-t border-slate-700"></div>

                <!-- Back to App -->
                <Link
                    :href="route('dashboard')"
                    :class="[
                        'flex items-center rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors relative group',
                        sidebarCollapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3',
                    ]"
                    @click="sidebarOpen = false"
                    :title="sidebarCollapsed ? 'Back to App' : ''"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    <span v-if="!sidebarCollapsed">Back to App</span>
                    <div
                        v-if="sidebarCollapsed"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50"
                    >
                        Back to App
                    </div>
                </Link>
            </nav>

            <!-- Admin User Info at Bottom -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-slate-700" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                    <img v-if="user?.avatar" :src="`/storage/${user.avatar}`" :alt="user?.name" class="w-8 h-8 rounded-full object-cover flex-shrink-0" />
                    <div v-else class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-medium flex-shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div v-if="!sidebarCollapsed" class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ user?.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-[68px]' : 'lg:pl-64'">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Left: Menu button (mobile) + Admin Panel label -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Admin Panel</h1>
                    </div>

                    <!-- Right: Profile Dropdown -->
                    <div class="flex items-center gap-3">
                        <Dropdown v-if="user" align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <img v-if="user.avatar" :src="`/storage/${user.avatar}`" :alt="user.name" class="w-8 h-8 rounded-full object-cover" />
                                    <div v-else class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-medium">
                                        {{ user.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ user.name }}</span>
                                    <svg class="hidden sm:block w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Logout</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="flash.success" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                <div class="px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
                    {{ flash.success }}
                </div>
            </div>
            <div v-if="flash.error" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                <div class="px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
                    {{ flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
                <slot />
            </main>
        </div>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="fixed bottom-0 left-0 right-0 z-40 bg-slate-800 border-t border-slate-700 lg:hidden">
            <div class="flex items-center justify-around h-16">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="getHref(item)"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 transition-colors',
                        isActive(item) ? 'text-white' : 'text-slate-400 hover:text-white'
                    ]"
                >
                    <!-- Chart Icon -->
                    <svg v-if="item.icon === 'chart'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <!-- Users Icon -->
                    <svg v-else-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <!-- Group Icon -->
                    <svg v-else-if="item.icon === 'group'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <!-- Tag Icon -->
                    <svg v-else-if="item.icon === 'tag'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <!-- Page Icon -->
                    <svg v-else-if="item.icon === 'page'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <!-- Document Icon (Blog) -->
                    <svg v-else-if="item.icon === 'document'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span class="text-xs">{{ item.name }}</span>
                </Link>
                <!-- Back to App (mobile bottom nav) -->
                <Link
                    :href="route('dashboard')"
                    class="flex flex-col items-center justify-center gap-1 px-3 py-2 text-slate-400 hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    <span class="text-xs">App</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
