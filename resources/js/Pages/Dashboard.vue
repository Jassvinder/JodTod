<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth?.user);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ user?.name }}!</h2>
            <p class="mt-1 text-gray-500">Your expense overview</p>

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
                    <p class="mt-2 text-3xl font-bold text-accent-600">₹0</p>
                </div>

                <!-- You Are Owed -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">You Are Owed</p>
                    <p class="mt-2 text-3xl font-bold text-success-600">₹0</p>
                </div>
            </div>

            <!-- Recent Activity Placeholder -->
            <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <div class="mt-4 text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>No recent activity</p>
                    <p class="text-sm mt-1">Add your first expense!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
