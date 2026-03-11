<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { computed, ref, onMounted, onUnmounted } from 'vue';

defineEmits(['toggle-sidebar']);

const user = computed(() => usePage().props.auth?.user);
const unreadCount = computed(() => usePage().props.unread_notifications_count || 0);

// Dark mode toggle
const isDark = ref(document.documentElement.classList.contains('dark'));

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    // Update meta theme-color
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', isDark.value ? '#1f2937' : '#6366f1');
};

// Notification dropdown state
const showNotifications = ref(false);
const recentNotifications = ref([]);
const loadingNotifications = ref(false);
const dropdownRef = ref(null);

const fetchRecentNotifications = async () => {
    if (loadingNotifications.value) return;
    loadingNotifications.value = true;

    try {
        const response = await axios.get(route('notifications.recent'));
        recentNotifications.value = response.data.notifications;
    } catch {
        // Silently fail
    } finally {
        loadingNotifications.value = false;
    }
};

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        fetchRecentNotifications();
    }
};

const closeNotifications = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showNotifications.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeNotifications);
});

onUnmounted(() => {
    document.removeEventListener('click', closeNotifications);
});

const formatTime = (dateStr) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
};

const getNotificationIcon = (type) => {
    const icons = {
        group_expense_added: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        added_to_group: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        settlement_requested: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        settlement_completed: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    };
    return icons[type] || 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
};

const handleNotificationClick = (notification) => {
    // Mark as read
    if (!notification.read_at) {
        axios.put(route('notifications.read', notification.id));
    }

    showNotifications.value = false;

    // Navigate to relevant page
    const data = notification.data;
    if (data.group_id) {
        if (data.type === 'group_expense_added') {
            router.visit(route('groups.expenses.index', data.group_id));
        } else if (data.type === 'settlement_requested' || data.type === 'settlement_completed') {
            router.visit(route('groups.settlements.index', data.group_id));
        } else {
            router.visit(route('groups.show', data.group_id));
        }
    }
};

const markAllAsRead = async () => {
    await axios.put(route('notifications.read-all'));
    recentNotifications.value = recentNotifications.value.map(n => ({
        ...n,
        read_at: new Date().toISOString(),
    }));
    router.reload({ only: ['unread_notifications_count'] });
};
</script>

<template>
    <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Left: Menu button (mobile) + Title -->
            <div class="flex items-center gap-3">
                <button
                    @click="$emit('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <Link :href="route('dashboard')" class="text-xl font-bold text-primary-600 dark:text-primary-400">JodTod</Link>
            </div>

            <!-- Right: Dark Mode + Notifications + Profile -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Dark Mode Toggle -->
                <button
                    @click="toggleDarkMode"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <!-- Sun icon (shown in dark mode) -->
                    <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Notification Bell -->
                <div ref="dropdownRef" class="relative">
                    <button
                        @click.stop="toggleNotifications"
                        class="relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Unread badge -->
                        <span
                            v-if="unreadCount > 0"
                            class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white bg-accent-500 rounded-full"
                        >
                            {{ unreadCount > 99 ? '99+' : unreadCount }}
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showNotifications"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                        >
                            <!-- Dropdown Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                                <button
                                    v-if="unreadCount > 0"
                                    @click.stop="markAllAsRead"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-700"
                                >
                                    Mark all read
                                </button>
                            </div>

                            <!-- Notification Items -->
                            <div class="max-h-80 overflow-y-auto">
                                <div v-if="loadingNotifications" class="p-6 text-center">
                                    <div class="inline-block w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                                </div>

                                <template v-else-if="recentNotifications.length > 0">
                                    <div
                                        v-for="notification in recentNotifications"
                                        :key="notification.id"
                                        @click="handleNotificationClick(notification)"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-50 dark:border-gray-700 last:border-0"
                                        :class="{ 'bg-primary-50/40': !notification.read_at }"
                                    >
                                        <svg
                                            class="w-5 h-5 mt-0.5 flex-shrink-0 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="getNotificationIcon(notification.data?.type)"
                                            />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2" :class="{ 'font-medium': !notification.read_at }">
                                                {{ notification.data?.message }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ formatTime(notification.created_at) }}</p>
                                        </div>
                                        <div v-if="!notification.read_at" class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                        </div>
                                    </div>
                                </template>

                                <div v-else class="p-6 text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                                </div>
                            </div>

                            <!-- Dropdown Footer -->
                            <div class="border-t border-gray-100 dark:border-gray-700">
                                <Link
                                    :href="route('notifications.index')"
                                    @click="showNotifications = false"
                                    class="block px-4 py-2.5 text-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    View all notifications
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Profile Dropdown -->
                <Dropdown v-if="user" align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center text-sm font-medium">
                                {{ user.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ user.name }}</span>
                            <svg class="hidden sm:block w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink v-if="user?.role === 'admin'" :href="route('admin.dashboard')">
                            Admin Panel
                        </DropdownLink>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Logout</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </header>
</template>
