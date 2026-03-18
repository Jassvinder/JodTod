<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, defineAsyncComponent } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

// Lazy-load chart components — chart.js is heavy, load only when charts section renders
const CategoryPieChart = defineAsyncComponent(() => import('@/Components/Expenses/CategoryPieChart.vue'));
const DailyBarChart = defineAsyncComponent(() => import('@/Components/Expenses/DailyBarChart.vue'));

const props = defineProps({
    expenses: Object,
    categories: Array,
    summary: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const period = ref(props.filters.period || '');
const sort = ref(props.filters.sort || 'expense_date');
const direction = ref(props.filters.direction || 'desc');

let searchTimeout = null;

function applyFilters() {
    router.get(route('expenses.index'), {
        search: search.value || undefined,
        category: category.value || undefined,
        period: period.value || undefined,
        sort: sort.value !== 'expense_date' ? sort.value : undefined,
        direction: direction.value !== 'desc' ? direction.value : undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

watch([category, period, sort, direction], applyFilters);

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

function clearFilters() {
    search.value = '';
    category.value = '';
    period.value = '';
    sort.value = 'expense_date';
    direction.value = 'desc';
    router.get(route('expenses.index'), {}, { preserveState: true, replace: true });
}

function toggleSort(field) {
    if (sort.value === field) {
        direction.value = direction.value === 'desc' ? 'asc' : 'desc';
    } else {
        sort.value = field;
        direction.value = 'desc';
    }
}

async function confirmDelete(expense) {
    const confirmed = await confirmAction({
        title: 'Delete Expense',
        text: `Are you sure you want to delete this expense of ${formatCurrency(expense.amount)}? This action cannot be undone.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('expenses.destroy', expense.id));
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount);
}

function formatDate(date) {
    const d = new Date(date);
    const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
    const timeStr = d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
    return `${dateStr}, ${timeStr}`;
}

const hasActiveFilters = () => search.value || category.value || period.value;
</script>

<template>
    <Head title="Expenses" />

    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Expenses</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Track your personal expenses</p>
                </div>
                <Link
                    :href="route('expenses.create')"
                    class="inline-flex items-center px-4 py-2.5 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Expense
                </Link>
            </div>

            <!-- Summary Cards -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(summary.monthly_total) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last Month</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(summary.last_month_total) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Difference</p>
                    <p
                        class="mt-1 text-2xl font-bold"
                        :class="summary.monthly_total > summary.last_month_total ? 'text-accent-600' : 'text-success-600'"
                    >
                        {{ summary.monthly_total > summary.last_month_total ? '+' : '' }}{{ formatCurrency(summary.monthly_total - summary.last_month_total) }}
                    </p>
                </div>
            </div>

            <!-- Charts -->
            <div v-if="summary.category_breakdown.length || summary.daily_trend.length" class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Category Pie Chart -->
                <div v-if="summary.category_breakdown.length" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">This Month by Category</h3>
                    <CategoryPieChart :data="summary.category_breakdown" />
                </div>

                <!-- Daily Trend Bar Chart -->
                <div v-if="summary.daily_trend.length" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Daily Spending Trend</h3>
                    <DailyBarChart :data="summary.daily_trend" />
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search -->
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search description..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                    </div>

                    <!-- Category filter -->
                    <select
                        v-model="category"
                        class="w-full py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>

                    <!-- Period filter -->
                    <select
                        v-model="period"
                        class="w-full py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>

                    <!-- Clear filters -->
                    <button
                        v-if="hasActiveFilters()"
                        @click="clearFilters"
                        class="py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Expenses List -->
            <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Table Header (desktop) -->
                <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <button @click="toggleSort('expense_date')" class="col-span-2 flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">
                        Date
                        <span v-if="sort === 'expense_date'" class="text-primary-600">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </button>
                    <div class="col-span-3">Description</div>
                    <button @click="toggleSort('category_id')" class="col-span-2 flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">
                        Category
                        <span v-if="sort === 'category_id'" class="text-primary-600">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </button>
                    <button @click="toggleSort('amount')" class="col-span-2 flex items-center gap-1 justify-end hover:text-gray-700">
                        Amount
                        <span v-if="sort === 'amount'" class="text-primary-600">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </button>
                    <div class="col-span-3 text-right">Actions</div>
                </div>

                <!-- Expense rows -->
                <div v-if="expenses.data.length === 0" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>No expenses found</p>
                    <Link :href="route('expenses.create')" class="inline-block mt-2 text-primary-600 hover:text-primary-700 font-medium text-sm">Add your first expense</Link>
                </div>

                <div
                    v-for="expense in expenses.data"
                    :key="expense.id"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 px-5 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors items-center"
                >
                    <!-- Mobile: stacked layout -->
                    <div class="sm:col-span-2 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(expense.expense_date) }}</div>
                    <div class="sm:col-span-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ expense.description || '-' }}</div>
                    <div class="sm:col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ expense.category?.name }}
                        </span>
                    </div>
                    <div class="sm:col-span-2 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:text-right">{{ formatCurrency(expense.amount) }}</div>
                    <div class="sm:col-span-3 flex items-center gap-2 sm:justify-end">
                        <Link
                            :href="route('expenses.edit', expense.id)"
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-primary-600 hover:bg-primary-50 transition-colors"
                        >
                            Edit
                        </Link>
                        <button
                            @click="confirmDelete(expense)"
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-accent-600 hover:bg-accent-50 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="expenses.data.length && expenses.last_page > 1" class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ expenses.from }} to {{ expenses.to }} of {{ expenses.total }} expenses
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in expenses.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 rounded-md text-sm',
                                link.active ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                                !link.url ? 'opacity-50 pointer-events-none' : '',
                            ]"
                            v-html="link.label"
                            preserve-state
                        />
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
