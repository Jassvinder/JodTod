<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    group: Object,
    expenses: Object,
    balances: Array,
    categories: Array,
    filters: Object,
});

const authUser = computed(() => usePage().props.auth.user);

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const expandedExpense = ref(null);

let searchTimeout = null;

function applyFilters() {
    router.get(route('groups.expenses.index', props.group.id), {
        search: search.value || undefined,
        category: category.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

watch([category, dateFrom, dateTo], applyFilters);

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

function clearFilters() {
    search.value = '';
    category.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get(route('groups.expenses.index', props.group.id), {}, { preserveState: true, replace: true });
}

const hasActiveFilters = () => search.value || category.value || dateFrom.value || dateTo.value;

async function confirmDelete(expense) {
    const confirmed = await confirmAction({
        title: 'Delete Expense',
        text: `Are you sure you want to delete this expense of ${formatCurrency(expense.amount)}? This action cannot be undone.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('groups.expenses.destroy', [props.group.id, expense.id]));
    }
}

function toggleExpand(expenseId) {
    expandedExpense.value = expandedExpense.value === expenseId ? null : expenseId;
}

function canDelete(expense) {
    const isAdmin = props.group.members.find(
        m => m.id === authUser.value.id && m.pivot?.role === 'admin'
    );
    return isAdmin || expense.paid_by === authUser.value.id;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
}

function formatDate(date) {
    const d = new Date(date);
    const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
    const timeStr = d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
    return `${dateStr}, ${timeStr}`;
}

function getMemberName(userId) {
    const member = props.group.members.find(m => m.id === userId);
    return member ? member.name : 'Unknown';
}

function splitTypeLabel(type) {
    const labels = { equal: 'Equal', custom: 'Custom', percentage: 'Percentage' };
    return labels[type] || type;
}

function splitTypeBadgeClass(type) {
    const classes = {
        equal: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        custom: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        percentage: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
    };
    return classes[type] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
}
</script>

<template>
    <Head :title="`${group.name} - Expenses`" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <Link :href="route('groups.show', group.id)" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ group.name }}</h1>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Group Expenses</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                <Link
                    :href="route('groups.settlements.index', group.id)"
                    class="inline-flex items-center px-4 py-2.5 rounded-lg text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 font-semibold hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                >
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Settle Up
                </Link>
                <Link
                    :href="route('groups.expenses.create', group.id)"
                    class="inline-flex items-center px-4 py-2.5 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Expense
                </Link>
                </div>
            </div>

            <!-- Balance Summary -->
            <div v-if="balances && balances.length" class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Member Balances</h3>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1">
                    <div
                        v-for="balance in balances"
                        :key="balance.user_id"
                        class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3 min-w-[140px]"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ balance.name }}</p>
                        <p
                            class="mt-0.5 text-lg font-bold"
                            :class="balance.balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                        >
                            {{ balance.balance >= 0 ? '+' : '' }}{{ formatCurrency(balance.balance) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <!-- Search -->
                    <div class="relative lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search description..."
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Category</label>
                        <select
                            v-model="category"
                            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From</label>
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                    </div>

                    <!-- Date To -->
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To</label>
                            <input
                                v-model="dateTo"
                                type="date"
                                class="w-full py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                        </div>
                        <button
                            v-if="hasActiveFilters()"
                            @click="clearFilters"
                            class="py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0"
                            title="Clear filters"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Expenses List -->
            <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Empty state -->
                <div v-if="expenses.data.length === 0" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No expenses yet. Add your first group expense!</p>
                    <Link
                        :href="route('groups.expenses.create', group.id)"
                        class="inline-block mt-3 text-primary-600 hover:text-primary-700 font-medium text-sm"
                    >
                        Add Expense
                    </Link>
                </div>

                <!-- Expense rows -->
                <div v-for="expense in expenses.data" :key="expense.id" class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <!-- Main row -->
                    <div
                        class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                        @click="toggleExpand(expense.id)"
                    >
                        <!-- Category icon / initial -->
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm shrink-0">
                            {{ expense.category?.name?.charAt(0) || '?' }}
                        </div>

                        <!-- Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ expense.description || 'No description' }}</p>
                                <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium', splitTypeBadgeClass(expense.split_type)]">
                                    {{ splitTypeLabel(expense.split_type) }}
                                </span>
                            </div>
                            <div class="mt-0.5 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                                <span>{{ expense.category?.name }}</span>
                                <span>&middot;</span>
                                <span>Paid by {{ getMemberName(expense.paid_by) }}</span>
                                <span>&middot;</span>
                                <span>{{ formatDate(expense.expense_date) }}</span>
                            </div>
                        </div>

                        <!-- Amount & Actions -->
                        <div class="flex items-center gap-3 shrink-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(expense.amount) }}</p>
                            <svg
                                class="w-4 h-4 text-gray-400 transition-transform"
                                :class="{ 'rotate-180': expandedExpense === expense.id }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Expanded split details -->
                    <div
                        v-if="expandedExpense === expense.id"
                        class="px-5 pb-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700"
                    >
                        <div class="pt-3">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Split Details</p>
                            <div class="space-y-2">
                                <div
                                    v-for="split in expense.splits"
                                    :key="split.id"
                                    class="flex items-center justify-between py-1.5"
                                >
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-xs">
                                            {{ getMemberName(split.user_id).charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ getMemberName(split.user_id) }}</span>
                                        <span v-if="split.user_id === expense.paid_by" class="text-xs text-gray-400 dark:text-gray-500">(Payer)</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(split.share_amount) }}</span>
                                        <span v-if="expense.split_type === 'percentage'" class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ split.percentage }}%)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2 justify-end">
                                <Link
                                    :href="route('groups.expenses.edit', [group.id, expense.id])"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-primary-600 hover:bg-primary-50 transition-colors"
                                >
                                    Edit
                                </Link>
                                <button
                                    v-if="canDelete(expense)"
                                    @click.stop="confirmDelete(expense)"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
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
                                link.active ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700',
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
