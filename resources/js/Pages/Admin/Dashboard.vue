<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_users: 0,
            total_groups: 0,
            total_expenses: 0,
            total_amount_tracked: 0,
            new_users_this_month: 0,
            new_groups_this_month: 0,
        }),
    },
    recentUsers: {
        type: Array,
        default: () => [],
    },
});

function formatCurrency(amount) {
    const num = Number(amount);
    if (isNaN(num)) return '₹0';
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: num % 1 !== 0 ? 2 : 0,
    }).format(num);
}

function formatNumber(num) {
    return new Intl.NumberFormat('en-IN').format(num);
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

const quickLinks = [
    { label: 'Users', href: '/admin/users', icon: 'users', color: 'blue' },
    { label: 'Groups', href: '/admin/groups', icon: 'groups', color: 'purple' },
    { label: 'Pages', href: '/admin/pages', icon: 'pages', color: 'amber' },
    { label: 'Blog', href: '/admin/blog', icon: 'blog', color: 'green' },
    { label: 'Categories', href: '/admin/categories', icon: 'categories', color: 'rose' },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h2>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Overview of your application</p>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                        <span class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ formatNumber(stats.total_users) }}</p>
                </div>

                <!-- Total Groups -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Groups</p>
                        <span class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ formatNumber(stats.total_groups) }}</p>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p>
                        <span class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ formatNumber(stats.total_expenses) }}</p>
                </div>

                <!-- Total Amount Tracked -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount Tracked</p>
                        <span class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(stats.total_amount_tracked) }}</p>
                </div>
            </div>

            <!-- Monthly Stats -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- New Users This Month -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-4">
                        <span class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Users This Month</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formatNumber(stats.new_users_this_month) }}</p>
                        </div>
                    </div>
                </div>

                <!-- New Groups This Month -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-4">
                        <span class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Groups This Month</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formatNumber(stats.new_groups_this_month) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Recent Users</h3>
                </div>

                <div v-if="recentUsers.length === 0" class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No users yet</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="recentUser in recentUsers" :key="recentUser.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                            {{ recentUser.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ recentUser.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ recentUser.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(recentUser.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Links</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Users -->
                    <Link href="/admin/users" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all group">
                        <span class="inline-flex p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg mb-3 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Users</p>
                    </Link>

                    <!-- Groups -->
                    <Link href="/admin/groups" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all group">
                        <span class="inline-flex p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg mb-3 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Groups</p>
                    </Link>

                    <!-- Pages -->
                    <Link href="/admin/pages" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center hover:border-amber-300 dark:hover:border-amber-600 hover:shadow-md transition-all group">
                        <span class="inline-flex p-3 bg-amber-50 dark:bg-amber-900/30 rounded-lg mb-3 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/50 transition-colors">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Pages</p>
                    </Link>

                    <!-- Blog -->
                    <Link href="/admin/blog" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all group">
                        <span class="inline-flex p-3 bg-green-50 dark:bg-green-900/30 rounded-lg mb-3 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Blog</p>
                    </Link>

                    <!-- Categories -->
                    <Link href="/admin/categories" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center hover:border-rose-300 dark:hover:border-rose-600 hover:shadow-md transition-all group">
                        <span class="inline-flex p-3 bg-rose-50 dark:bg-rose-900/30 rounded-lg mb-3 group-hover:bg-rose-100 dark:group-hover:bg-rose-900/50 transition-colors">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Categories</p>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
