<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/">
                    <h1 class="text-3xl font-bold text-primary-600">JodTod</h1>
                </Link>
                <p class="text-gray-500 mt-1">Kharcha split karo, dosti mazboot rakho</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
