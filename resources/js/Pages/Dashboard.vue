<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            <p class="mt-1 text-gray-500">Welcome to JodTod!</p>

            <!-- Summary Cards -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- This Month -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">This Month Expenses</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">₹0</p>
                </div>

                <!-- You Owe -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">You Owe</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">₹0</p>
                </div>

                <!-- You Are Owed -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">You Are Owed</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">₹0</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
