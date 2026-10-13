<script setup>
import { Link } from '@inertiajs/vue3';
import BrandLogo from '@/Components/Shared/BrandLogo.vue';
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/">
                    <BrandLogo class="h-10 mx-auto" />
                </a>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Track expenses, split bills, get things done</p>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
