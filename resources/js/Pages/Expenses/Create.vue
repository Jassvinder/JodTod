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
    expense_date: new Date().toISOString().split('T')[0],
});

function submit() {
    form.post(route('expenses.store'));
}
</script>

<template>
    <Head title="Add Expense" />

    <AppLayout>
        <div class="max-w-lg mx-auto">
            <h2 class="text-2xl font-bold text-gray-900">Add Expense</h2>
            <p class="mt-1 text-gray-500">Track a new personal expense</p>

            <div class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
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
