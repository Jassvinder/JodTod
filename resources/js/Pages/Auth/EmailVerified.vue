<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    already_verified: Boolean,
});
</script>

<template>
    <GuestLayout>
        <Head title="Email Verified" />

        <div class="text-center">
            <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ already_verified ? 'Already Verified' : 'Email Verified Successfully!' }}
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                <template v-if="already_verified">
                    Your email was already verified. You can close this page.
                </template>
                <template v-else>
                    Your email has been verified. You can now go back to the app and continue.
                </template>
            </p>

            <Link
                :href="route('login')"
                class="inline-block py-3 px-6 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors"
            >
                Go to Login
            </Link>
        </div>
    </GuestLayout>
</template>
