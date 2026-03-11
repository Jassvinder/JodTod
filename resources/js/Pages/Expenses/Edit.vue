<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ExpenseForm from '@/Components/Expenses/ExpenseForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
    categories: Array,
});

const form = useForm({
    amount: props.expense.amount,
    category_id: props.expense.category_id,
    description: props.expense.description || '',
    expense_date: props.expense.expense_date ? props.expense.expense_date.slice(0, 16) : '',
});

function submit() {
    form.put(route('expenses.update', props.expense.id));
}
</script>

<template>
    <Head title="Edit Expense" />

    <AppLayout>
        <div class="max-w-lg mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Expense</h2>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Update expense details</p>

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <ExpenseForm
                    :form="form"
                    :categories="categories"
                    :processing="form.processing"
                    submit-label="Update Expense"
                    @submit="submit"
                />
            </div>
        </div>
    </AppLayout>
</template>
