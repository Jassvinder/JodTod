<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ExpenseForm from '@/Components/Expenses/ExpenseForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    categories: Array,
});

const form = useForm({
    amount: '',
    category_id: '',
    description: '',
    expense_date: new Date().toISOString().slice(0, 16),
});

function submit() {
    form.post(route('expenses.store'));
}
</script>

<template>
    <Head title="Add Expense" />

    <AppLayout>
        <div class="max-w-lg mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Expense</h2>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Track a new personal expense</p>

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <ExpenseForm
                    :form="form"
                    :categories="categories"
                    :processing="form.processing"
                    submit-label="Add Expense"
                    @submit="submit"
                />
            </div>
        </div>
    </AppLayout>
</template>
