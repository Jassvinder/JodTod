<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    form: Object,
    categories: Array,
    submitLabel: { type: String, default: 'Save Expense' },
    processing: { type: Boolean, default: false },
});

defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-5">
        <!-- Amount -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
            <div class="mt-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm">&#8377;</span>
                <input
                    id="amount"
                    v-model="form.amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="99999999.99"
                    placeholder="0.00"
                    class="block w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    required
                />
            </div>
            <p v-if="form.errors.amount" class="mt-1 text-sm text-accent-600">{{ form.errors.amount }}</p>
        </div>

        <!-- Category -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
            <select
                id="category_id"
                v-model="form.category_id"
                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                required
            >
                <option value="" disabled>Select category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>
            <p v-if="form.errors.category_id" class="mt-1 text-sm text-accent-600">{{ form.errors.category_id }}</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-gray-400 dark:text-gray-500">(optional)</span></label>
            <input
                id="description"
                v-model="form.description"
                type="text"
                maxlength="255"
                placeholder="e.g. Lunch at cafe"
                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            />
            <p v-if="form.errors.description" class="mt-1 text-sm text-accent-600">{{ form.errors.description }}</p>
        </div>

        <!-- Date & Time -->
        <div>
            <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date & Time</label>
            <input
                id="expense_date"
                v-model="form.expense_date"
                type="datetime-local"
                :max="new Date().toISOString().slice(0, 16)"
                class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                required
            />
            <p v-if="form.errors.expense_date" class="mt-1 text-sm text-accent-600">{{ form.errors.expense_date }}</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-2">
            <button
                type="submit"
                :disabled="processing"
                class="flex-1 py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50"
            >
                {{ processing ? 'Saving...' : submitLabel }}
            </button>
            <Link
                :href="route('expenses.index')"
                class="py-3 px-6 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
                Cancel
            </Link>
        </div>
    </form>
</template>
