<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    incomes: Object,
    summary: Object,
    sourceSuggestions: Array,
    filters: Object,
});

const showAddForm = ref(false);
const editingIncome = ref(null);

const form = useForm({
    amount: '',
    source: '',
    description: '',
    income_date: new Date().toISOString().split('T')[0],
});

const editForm = useForm({
    amount: '',
    source: '',
    description: '',
    income_date: '',
});

// Source autocomplete
const sourceQuery = ref('');
const showSourceSuggestions = ref(false);
const filteredSources = computed(() => {
    if (!sourceQuery.value) return props.sourceSuggestions || [];
    return (props.sourceSuggestions || []).filter(s =>
        s.toLowerCase().includes(sourceQuery.value.toLowerCase())
    );
});

function onSourceInput(val) {
    sourceQuery.value = val;
    form.source = val;
    showSourceSuggestions.value = true;
}

function selectSource(source) {
    form.source = source;
    sourceQuery.value = source;
    showSourceSuggestions.value = false;
}

function addIncome() {
    form.post(route('incomes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.income_date = new Date().toISOString().split('T')[0];
            showAddForm.value = false;
        },
    });
}

function startEdit(income) {
    editingIncome.value = income.id;
    editForm.amount = income.amount;
    editForm.source = income.source;
    editForm.description = income.description || '';
    editForm.income_date = income.income_date ? income.income_date.split('T')[0] : '';
}

function saveEdit(income) {
    editForm.put(route('incomes.update', income.id), {
        preserveScroll: true,
        onSuccess: () => { editingIncome.value = null; },
    });
}

async function deleteIncome(income) {
    const confirmed = await confirmAction({
        title: 'Delete Income?',
        text: 'This income entry will be permanently removed.',
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('incomes.destroy', income.id), {
            preserveScroll: true,
        });
    }
}

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

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

const savingsColor = computed(() => {
    const s = props.summary.this_month_savings;
    if (s > 0) return 'text-green-600 dark:text-green-400';
    if (s < 0) return 'text-red-600 dark:text-red-400';
    return 'text-gray-600 dark:text-gray-400';
});

const incomeChange = computed(() => {
    const current = props.summary.this_month_income;
    const last = props.summary.last_month_income;
    if (last === 0) return current > 0 ? 100 : 0;
    return Math.round(((current - last) / last) * 100);
});
</script>

<template>
    <Head title="Income" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Income</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Track your earnings and see profit/loss</p>
                </div>
                <button
                    @click="showAddForm = !showAddForm"
                    class="inline-flex items-center px-4 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Income
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- This Month Income -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Income This Month</p>
                        <span class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(summary.this_month_income) }}</p>
                    <div v-if="summary.last_month_income > 0 || summary.this_month_income > 0" class="mt-2 flex items-center text-sm">
                        <span
                            :class="incomeChange >= 0 ? 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/30' : 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-900/30'"
                            class="inline-flex items-center px-1.5 py-0.5 rounded-md text-xs font-medium"
                        >
                            {{ incomeChange >= 0 ? '+' : '' }}{{ incomeChange }}%
                        </span>
                        <span class="ml-1.5 text-gray-500 dark:text-gray-400">vs last month</span>
                    </div>
                </div>

                <!-- This Month Expense -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expenses This Month</p>
                        <span class="p-2 bg-red-50 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(summary.this_month_expense) }}</p>
                </div>

                <!-- Savings (Profit/Loss) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ summary.this_month_savings >= 0 ? 'Savings' : 'Loss' }}
                        </p>
                        <span class="p-2 rounded-lg" :class="summary.this_month_savings >= 0 ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30'">
                            <svg class="w-5 h-5" :class="summary.this_month_savings >= 0 ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold" :class="savingsColor">
                        {{ summary.this_month_savings >= 0 ? '+' : '' }}{{ formatCurrency(summary.this_month_savings) }}
                    </p>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div v-if="summary.monthly_trend && summary.monthly_trend.length > 0" class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">6-Month Trend</h3>
                <div class="flex items-end justify-between gap-2 h-40">
                    <div
                        v-for="month in summary.monthly_trend"
                        :key="month.month"
                        class="flex-1 flex flex-col items-center gap-1"
                    >
                        <div class="w-full flex gap-0.5 items-end justify-center" style="height: 120px;">
                            <!-- Income bar -->
                            <div
                                class="w-3 sm:w-4 bg-green-400 dark:bg-green-500 rounded-t transition-all"
                                :style="{
                                    height: Math.max(4, (month.income / Math.max(...summary.monthly_trend.map(m => Math.max(m.income, m.expense, 1)))) * 100) + 'px'
                                }"
                                :title="'Income: ' + formatCurrency(month.income)"
                            ></div>
                            <!-- Expense bar -->
                            <div
                                class="w-3 sm:w-4 bg-red-400 dark:bg-red-500 rounded-t transition-all"
                                :style="{
                                    height: Math.max(4, (month.expense / Math.max(...summary.monthly_trend.map(m => Math.max(m.income, m.expense, 1)))) * 100) + 'px'
                                }"
                                :title="'Expense: ' + formatCurrency(month.expense)"
                            ></div>
                        </div>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ month.short_month }}</span>
                        <span class="text-[9px] font-medium" :class="month.savings >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ month.savings >= 0 ? '+' : '' }}{{ formatCurrency(month.savings) }}
                        </span>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-400 rounded"></span> Income</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-400 rounded"></span> Expense</span>
                </div>
            </div>

            <!-- Source Breakdown -->
            <div v-if="summary.source_breakdown && summary.source_breakdown.length > 0" class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Income Sources (This Month)</h3>
                <div class="space-y-3">
                    <div v-for="src in summary.source_breakdown" :key="src.source">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ src.source }}</span>
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(src.total) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div
                                class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                :style="{ width: Math.round((src.total / summary.this_month_income) * 100) + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Income Form -->
            <div v-if="showAddForm" class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Income</h3>
                <form @submit.prevent="addIncome" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                            <div class="mt-1 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">&#8377;</span>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    max="99999999.99"
                                    placeholder="0.00"
                                    class="block w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                        </div>
                        <!-- Source -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source</label>
                            <input
                                v-model="form.source"
                                @input="onSourceInput($event.target.value)"
                                @focus="showSourceSuggestions = true"
                                @blur="setTimeout(() => showSourceSuggestions = false, 200)"
                                type="text"
                                maxlength="100"
                                placeholder="e.g. Salary, Freelance, Rent"
                                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                required
                            />
                            <!-- Autocomplete dropdown -->
                            <div
                                v-if="showSourceSuggestions && filteredSources.length > 0"
                                class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-lg max-h-40 overflow-y-auto"
                            >
                                <button
                                    v-for="src in filteredSources"
                                    :key="src"
                                    type="button"
                                    @mousedown.prevent="selectSource(src)"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                >
                                    {{ src }}
                                </button>
                            </div>
                            <p v-if="form.errors.source" class="mt-1 text-sm text-red-600">{{ form.errors.source }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-gray-400">(optional)</span></label>
                            <input
                                v-model="form.description"
                                type="text"
                                maxlength="255"
                                placeholder="e.g. March salary"
                                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            />
                        </div>
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                            <input
                                v-model="form.income_date"
                                type="date"
                                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                required
                            />
                            <p v-if="form.errors.income_date" class="mt-1 text-sm text-red-600">{{ form.errors.income_date }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="py-3 px-6 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Add Income' }}
                        </button>
                        <button
                            type="button"
                            @click="showAddForm = false"
                            class="py-3 px-6 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Income List -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Income History</h3>
                </div>

                <div v-if="incomes.data.length === 0" class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No income recorded yet</p>
                    <button
                        @click="showAddForm = true"
                        class="inline-flex items-center mt-3 px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors"
                    >
                        Add your first income
                    </button>
                </div>

                <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div
                        v-for="income in incomes.data"
                        :key="income.id"
                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                        <!-- Edit mode -->
                        <div v-if="editingIncome === income.id" class="space-y-3">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <input v-model="editForm.amount" type="number" step="0.01" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                                <input v-model="editForm.source" type="text" maxlength="100" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                                <input v-model="editForm.description" type="text" maxlength="255" placeholder="Description" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                                <input v-model="editForm.income_date" type="date" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                            </div>
                            <div class="flex gap-2">
                                <button @click="saveEdit(income)" class="py-1.5 px-3 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700">Save</button>
                                <button @click="editingIncome = null" class="py-1.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-sm">Cancel</button>
                            </div>
                        </div>

                        <!-- View mode -->
                        <div v-else class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ income.source }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(income.income_date) }}
                                    <span v-if="income.description"> &middot; {{ income.description }}</span>
                                </p>
                            </div>
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400 flex-shrink-0">
                                +{{ formatCurrency(income.amount) }}
                            </p>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button @click="startEdit(income)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteIncome(income)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="incomes.links && incomes.last_page > 1" class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-1">
                    <template v-for="link in incomes.links" :key="link.label">
                        <button
                            v-if="link.url"
                            @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm',
                                link.active
                                    ? 'bg-green-600 text-white'
                                    : 'text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                            ]"
                            v-html="link.label"
                        />
                        <span v-else class="px-3 py-2 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
