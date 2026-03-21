<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    personalSummary: {
        type: Object,
        default: () => ({
            this_month_total: 0,
            last_month_total: 0,
            category_breakdown: [],
        }),
    },
    incomeSummary: {
        type: Object,
        default: () => ({
            this_month_income: 0,
            last_month_income: 0,
            this_month_savings: 0,
        }),
    },
    monthlyTrend: {
        type: Array,
        default: () => [],
    },
    groupsSummary: {
        type: Object,
        default: () => ({
            total_you_owe: 0,
            total_owed_to_you: 0,
            groups: [],
        }),
    },
    recentActivity: {
        type: Array,
        default: () => [],
    },
    pendingSettlements: {
        type: Object,
        default: () => ({
            count: 0,
            items: [],
        }),
    },
    todoStats: {
        type: Object,
        default: () => ({ pending: 0, overdue: 0 }),
    },
});

const user = computed(() => usePage().props.auth?.user);

// Month-over-month comparison
const monthChange = computed(() => {
    const current = props.personalSummary.this_month_total;
    const last = props.personalSummary.last_month_total;
    if (last === 0) return current > 0 ? 100 : 0;
    return Math.round(((current - last) / last) * 100);
});

const monthChangeUp = computed(() => monthChange.value > 0);

// Group expense link: if 1 group -> direct add expense, if multiple -> group listing
const groupExpenseLink = computed(() => {
    const groups = props.groupsSummary.groups;
    if (groups.length === 1) {
        return route('groups.expenses.create', groups[0].group_id);
    }
    return route('groups.index');
});

// Category breakdown: top 5 with percentage bars
const topCategories = computed(() => {
    const cats = props.personalSummary.category_breakdown || [];
    if (cats.length === 0) return [];
    const total = cats.reduce((sum, c) => sum + c.total, 0);
    return cats.slice(0, 5).map(c => ({
        ...c,
        percentage: total > 0 ? Math.round((c.total / total) * 100) : 0,
    }));
});

// Format currency in INR
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

// Relative date formatting
function relativeDate(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const target = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const diffDays = Math.round((today - target) / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays > 1 && diffDays <= 6) return `${diffDays} days ago`;
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header with Quick Actions -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome, {{ user?.name }}!</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Your expense overview</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('groups.create')"
                        class="inline-flex items-center px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">New Group</span>
                    </Link>
                    <Link
                        v-if="groupsSummary.groups.length > 0"
                        :href="groupExpenseLink"
                        class="inline-flex items-center px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-lg border border-blue-300 dark:border-blue-600 text-xs sm:text-sm font-semibold text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                    >
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">Group Expense</span>
                    </Link>
                    <Link
                        :href="route('expenses.create')"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2.5 rounded-lg bg-primary-600 text-white text-xs sm:text-sm font-semibold hover:bg-primary-700 transition-colors"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Add Expense</span>
                    </Link>
                </div>
            </div>

            <!-- Row 1: Summary Cards -->
            <div class="mt-6 grid grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- This Month Expenses -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Expenses</p>
                        <span class="p-1.5 bg-primary-50 dark:bg-primary-900/30 rounded-lg">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(personalSummary.this_month_total) }}</p>
                    <div v-if="personalSummary.last_month_total > 0 || personalSummary.this_month_total > 0" class="mt-1.5 flex items-center">
                        <span
                            :class="monthChangeUp ? 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-900/30' : 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/30'"
                            class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium"
                        >
                            {{ monthChangeUp ? '+' : '' }}{{ monthChange }}%
                        </span>
                    </div>
                </div>

                <!-- This Month Income -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Income</p>
                        <span class="p-1.5 bg-green-50 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(incomeSummary.this_month_income) }}</p>
                    <Link :href="route('incomes.index')" class="mt-1.5 inline-block text-[10px] text-green-600 hover:text-green-700 font-medium">
                        Manage &rarr;
                    </Link>
                </div>

                <!-- Savings/Loss -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ incomeSummary.this_month_savings >= 0 ? 'Savings' : 'Loss' }}</p>
                        <span class="p-1.5 rounded-lg" :class="incomeSummary.this_month_savings >= 0 ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30'">
                            <svg class="w-4 h-4" :class="incomeSummary.this_month_savings >= 0 ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold" :class="incomeSummary.this_month_savings >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                        {{ incomeSummary.this_month_savings >= 0 ? '+' : '' }}{{ formatCurrency(incomeSummary.this_month_savings) }}
                    </p>
                </div>

                <!-- To Pay -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">To Pay</p>
                        <span class="p-1.5 bg-red-50 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(groupsSummary.total_you_owe) }}</p>
                    <Link
                        v-if="groupsSummary.groups.length > 0"
                        :href="route('groups.index')"
                        class="mt-1.5 inline-block text-[10px] text-red-600 hover:text-red-700 font-medium"
                    >
                        Groups &rarr;
                    </Link>
                </div>

                <!-- To Receive -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">To Receive</p>
                        <span class="p-1.5 bg-green-50 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h14" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(groupsSummary.total_owed_to_you) }}</p>
                    <Link
                        v-if="groupsSummary.groups.length > 0"
                        :href="route('groups.index')"
                        class="mt-1.5 inline-block text-[10px] text-green-600 hover:text-green-700 font-medium"
                    >
                        Groups &rarr;
                    </Link>
                </div>
            </div>

            <!-- Monthly Trend Chart (Income vs Expense) -->
            <div v-if="monthlyTrend && monthlyTrend.length > 0" class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Income vs Expenses (6 Months)</h3>
                <div class="flex items-end justify-between gap-2 h-36">
                    <div
                        v-for="month in monthlyTrend"
                        :key="month.month"
                        class="flex-1 flex flex-col items-center gap-1"
                    >
                        <div class="w-full flex gap-0.5 items-end justify-center" style="height: 100px;">
                            <div
                                class="w-3 sm:w-5 bg-green-400 dark:bg-green-500 rounded-t transition-all"
                                :style="{
                                    height: Math.max(4, (month.income / Math.max(...monthlyTrend.map(m => Math.max(m.income, m.expense, 1)))) * 100) + 'px'
                                }"
                                :title="'Income: ' + formatCurrency(month.income)"
                            ></div>
                            <div
                                class="w-3 sm:w-5 bg-red-400 dark:bg-red-500 rounded-t transition-all"
                                :style="{
                                    height: Math.max(4, (month.expense / Math.max(...monthlyTrend.map(m => Math.max(m.income, m.expense, 1)))) * 100) + 'px'
                                }"
                                :title="'Expense: ' + formatCurrency(month.expense)"
                            ></div>
                        </div>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ month.month }}</span>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-400 rounded"></span> Income</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-400 rounded"></span> Expense</span>
                </div>
            </div>

            <!-- Row 2: Two columns on desktop -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Tasks Widget -->
                    <div v-if="todoStats.pending > 0 || todoStats.overdue > 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">My Tasks</h3>
                            <Link :href="route('todos.index')" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</Link>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-primary-600">{{ todoStats.pending }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">pending</span>
                            </div>
                            <div v-if="todoStats.overdue > 0" class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-red-600">{{ todoStats.overdue }}</span>
                                <span class="text-sm text-red-500">overdue</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Settlements -->
                    <div
                        v-if="pendingSettlements.count > 0"
                        class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-6"
                    >
                        <div class="flex items-center gap-3 mb-4">
                            <span class="p-2 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Pending Payments</h3>
                                <p class="text-xs text-amber-600 dark:text-amber-400">You have {{ pendingSettlements.count }} pending {{ pendingSettlements.count === 1 ? 'payment' : 'payments' }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="item in pendingSettlements.items"
                                :key="item.id"
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-3 border border-amber-100 dark:border-amber-800/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-700 dark:text-amber-300 text-sm font-semibold">
                                        {{ item.to_user_name?.charAt(0)?.toUpperCase() || '?' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Pay {{ formatCurrency(item.amount) }} to {{ item.to_user_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ item.group_name }}</p>
                                    </div>
                                </div>
                                <Link
                                    :href="route('groups.settlements.index', item.group_id || 0)"
                                    class="text-xs font-semibold text-amber-700 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 whitespace-nowrap"
                                >
                                    Pay Now &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Groups Overview -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Groups</h3>
                            <Link
                                v-if="groupsSummary.groups.length > 0"
                                :href="route('groups.index')"
                                class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                            >
                                View All
                            </Link>
                        </div>

                        <div v-if="groupsSummary.groups.length === 0" class="text-center py-6">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No groups yet</p>
                            <Link
                                :href="route('groups.create')"
                                class="inline-block mt-2 text-sm text-primary-600 hover:text-primary-700 font-medium"
                            >
                                Create your first group &rarr;
                            </Link>
                        </div>

                        <div v-else class="space-y-3">
                            <Link
                                v-for="group in groupsSummary.groups"
                                :key="group.group_id"
                                :href="route('groups.show', group.group_id)"
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors -mx-1 px-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold text-sm">
                                        {{ group.group_name?.charAt(0)?.toUpperCase() || 'G' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ group.group_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ group.members_count }} {{ group.members_count === 1 ? 'member' : 'members' }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-sm font-semibold"
                                    :class="group.your_balance >= 0 ? 'text-green-600' : 'text-red-600'"
                                >
                                    {{ group.your_balance >= 0 ? '+' : '' }}{{ formatCurrency(group.your_balance) }}
                                </span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Category Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Category Breakdown</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">This month's spending by category</p>

                    <div v-if="topCategories.length === 0" class="text-center py-6">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No expenses this month</p>
                        <Link
                            :href="route('expenses.create')"
                            class="inline-block mt-2 text-sm text-primary-600 hover:text-primary-700 font-medium"
                        >
                            Add your first expense &rarr;
                        </Link>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="cat in topCategories" :key="cat.name">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ cat.name }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(cat.total) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                <div
                                    class="bg-primary-500 h-2.5 rounded-full transition-all duration-500"
                                    :style="{ width: cat.percentage + '%' }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ cat.percentage }}% of total</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Recent Activity (full width) -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Recent Activity</h3>
                    <Link
                        v-if="recentActivity.length > 0"
                        :href="route('expenses.index')"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                    >
                        View All
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-if="recentActivity.length === 0" class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add your first expense to get started!</p>
                    <Link
                        :href="route('expenses.create')"
                        class="inline-flex items-center mt-3 px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Expense
                    </Link>
                </div>

                <!-- Activity List -->
                <div v-else class="space-y-1">
                    <div
                        v-for="(item, index) in recentActivity"
                        :key="index"
                        class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <!-- Icon -->
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/30': item.type === 'personal_expense',
                                'bg-blue-50 dark:bg-blue-900/30': item.type === 'group_expense',
                                'bg-green-50 dark:bg-green-900/30': item.type === 'settlement',
                            }"
                        >
                            <!-- Personal expense: wallet icon -->
                            <svg v-if="item.type === 'personal_expense'" class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <!-- Group expense: users icon -->
                            <svg v-else-if="item.type === 'group_expense'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Settlement: arrow-right icon -->
                            <svg v-else class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                <template v-if="item.type === 'settlement'">
                                    {{ item.from_name }} &rarr; {{ item.to_name }}
                                </template>
                                <template v-else>
                                    {{ item.description }}
                                </template>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ relativeDate(item.date) }}</span>
                                <span v-if="item.group_name" class="ml-1">&middot; {{ item.group_name }}</span>
                            </p>
                        </div>

                        <!-- Amount -->
                        <div class="flex-shrink-0 text-right">
                            <p
                                class="text-sm font-semibold"
                                :class="{
                                    'text-gray-900 dark:text-gray-100': item.type === 'personal_expense',
                                    'text-blue-600 dark:text-blue-400': item.type === 'group_expense',
                                    'text-green-600 dark:text-green-400': item.type === 'settlement',
                                }"
                            >
                                {{ formatCurrency(item.amount) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                <template v-if="item.type === 'personal_expense'">Personal</template>
                                <template v-else-if="item.type === 'group_expense'">Group</template>
                                <template v-else>Settlement</template>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
