<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    monthlyTrends: Array,
    categoryBreakdown: Array,
    topGroups: Array,
    settlementStats: Object,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(amount);
}

// Find max expense for bar chart scaling
const maxExpense = Math.max(...props.monthlyTrends.map(m => m.expenses), 1);
const maxCategoryTotal = Math.max(...props.categoryBreakdown.map(c => c.total), 1);
</script>

<template>
    <Head title="Reports" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Reports</h1>

            <!-- Settlement Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ settlementStats.total }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Settlements</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ settlementStats.completed }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ settlementStats.pending }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ formatCurrency(settlementStats.total_amount) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Amount Settled</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Expense Trends -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Expense Trends</h2>
                    <div class="space-y-3">
                        <div v-for="month in monthlyTrends" :key="month.month" class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 dark:text-gray-400 w-16 flex-shrink-0">{{ month.month.split(' ')[0] }}</span>
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-6 relative overflow-hidden">
                                <div
                                    class="h-full bg-primary-500 rounded-full flex items-center justify-end pr-2 transition-all"
                                    :style="{ width: Math.max((month.expenses / maxExpense) * 100, 2) + '%' }"
                                >
                                    <span v-if="month.expenses > 0" class="text-[10px] text-white font-medium whitespace-nowrap">{{ formatCurrency(month.expenses) }}</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500 w-8 text-right" :title="month.users + ' new users'">+{{ month.users }}</span>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Category Breakdown</h2>
                    <div v-if="categoryBreakdown.length === 0" class="text-center py-8 text-gray-400">No data yet</div>
                    <div v-else class="space-y-3">
                        <div v-for="cat in categoryBreakdown" :key="cat.name" class="flex items-center gap-3">
                            <span class="text-xs text-gray-700 dark:text-gray-300 w-24 truncate flex-shrink-0">{{ cat.name }}</span>
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-5 relative overflow-hidden">
                                <div
                                    class="h-full bg-accent-500 rounded-full transition-all"
                                    :style="{ width: Math.max((cat.total / maxCategoryTotal) * 100, 2) + '%' }"
                                />
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 w-20 text-right">{{ formatCurrency(cat.total) }}</span>
                            <span class="text-[10px] text-gray-400 w-6 text-right">({{ cat.count }})</span>
                        </div>
                    </div>
                </div>

                <!-- Top Groups -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Groups by Expenses</h2>
                    <div v-if="topGroups.length === 0" class="text-center py-8 text-gray-400">No groups yet</div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                                    <th class="pb-3 text-gray-500 dark:text-gray-400 font-medium">#</th>
                                    <th class="pb-3 text-gray-500 dark:text-gray-400 font-medium">Group</th>
                                    <th class="pb-3 text-gray-500 dark:text-gray-400 font-medium text-center">Members</th>
                                    <th class="pb-3 text-gray-500 dark:text-gray-400 font-medium text-right">Total Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(group, i) in topGroups" :key="group.name" class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="py-3 text-gray-400">{{ i + 1 }}</td>
                                    <td class="py-3 font-medium text-gray-900 dark:text-gray-100">{{ group.name }}</td>
                                    <td class="py-3 text-center text-gray-500 dark:text-gray-400">{{ group.members }}</td>
                                    <td class="py-3 text-right font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(group.total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
