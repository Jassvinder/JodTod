<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    notifications: Object,
});

const getNotificationIcon = (type) => {
    const icons = {
        group_expense_added: { bg: 'bg-blue-100 dark:bg-blue-900/30', color: 'text-blue-600 dark:text-blue-400', path: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        added_to_group: { bg: 'bg-green-100 dark:bg-green-900/30', color: 'text-green-600 dark:text-green-400', path: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
        settlement_requested: { bg: 'bg-amber-100 dark:bg-amber-900/30', color: 'text-amber-600 dark:text-amber-400', path: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        settlement_completed: { bg: 'bg-emerald-100 dark:bg-emerald-900/30', color: 'text-emerald-600 dark:text-emerald-400', path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        todo_reminder: { bg: 'bg-purple-100 dark:bg-purple-900/30', color: 'text-purple-600 dark:text-purple-400', path: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' },
        todo_assigned: { bg: 'bg-indigo-100 dark:bg-indigo-900/30', color: 'text-indigo-600 dark:text-indigo-400', path: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
    };
    return icons[type] || { bg: 'bg-gray-100 dark:bg-gray-700', color: 'text-gray-600 dark:text-gray-400', path: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' };
};

const getNotificationLink = (notification) => {
    const data = notification.data;
    if (data.type === 'todo_reminder' || data.type === 'todo_assigned') {
        return route('todos.index');
    }
    if (data.group_id) {
        if (data.type === 'group_expense_added') {
            return route('groups.expenses.index', data.group_id);
        }
        if (data.type === 'settlement_requested' || data.type === 'settlement_completed') {
            return route('groups.settlements.index', data.group_id);
        }
        return route('groups.show', data.group_id);
    }
    return null;
};

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

const markAsRead = (id) => {
    router.put(route('notifications.read', id), {}, { preserveScroll: true });
};

const markAllAsRead = () => {
    router.put(route('notifications.read-all'), {}, { preserveScroll: true });
};

const handleNotificationClick = (notification) => {
    if (!notification.read_at) {
        markAsRead(notification.id);
    }
    const link = getNotificationLink(notification);
    if (link) {
        router.visit(link);
    }
};

const hasUnread = computed(() => {
    return props.notifications?.data?.some(n => !n.read_at);
});
</script>

<template>
    <Head title="Notifications" />

    <AppLayout>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Notifications</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Stay updated with your groups and expenses</p>
                </div>

                <button
                    v-if="hasUnread"
                    @click="markAllAsRead"
                    class="text-sm font-medium text-primary-600 hover:text-primary-700"
                >
                    Mark all as read
                </button>
            </div>

            <!-- Notification List -->
            <div v-if="notifications.data.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                <div
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    @click="handleNotificationClick(notification)"
                    class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                    :class="{ 'bg-primary-50/50 dark:bg-primary-900/20': !notification.read_at }"
                >
                    <!-- Icon -->
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                        :class="getNotificationIcon(notification.data?.type).bg"
                    >
                        <svg
                            class="w-5 h-5"
                            :class="getNotificationIcon(notification.data?.type).color"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                :d="getNotificationIcon(notification.data?.type).path"
                            />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 dark:text-gray-100" :class="{ 'font-semibold': !notification.read_at }">
                            {{ notification.data?.message }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formatTime(notification.created_at) }}</p>
                    </div>

                    <!-- Unread dot -->
                    <div v-if="!notification.read_at" class="flex-shrink-0 mt-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="mt-3 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up! We'll notify you when something happens.</p>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.links && notifications.last_page > 1" class="mt-6 flex justify-center gap-1">
                <template v-for="link in notifications.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-2 text-sm rounded-lg transition-colors"
                        :class="link.active ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="px-3 py-2 text-sm text-gray-400 dark:text-gray-600"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
