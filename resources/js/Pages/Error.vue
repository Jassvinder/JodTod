<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: Number,
});

const page = usePage();

const title = computed(() => {
    const titles = {
        403: 'Forbidden',
        404: 'Page Not Found',
        500: 'Server Error',
        503: 'Service Unavailable',
    };
    return titles[props.status] || 'Error';
});

const description = computed(() => {
    const descriptions = {
        403: 'Sorry, you don\'t have permission to access this page.',
        404: 'Sorry, the page you are looking for could not be found.',
        500: 'Whoops, something went wrong on our end.',
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
    };
    return descriptions[props.status] || 'Something went wrong.';
});

const isLoggedIn = computed(() => !!page.props?.auth?.user);
</script>

<template>
    <Head :title="`${status} - ${title}`" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
        <div class="text-center max-w-md">
            <p class="text-6xl font-bold text-primary-600">{{ status }}</p>
            <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ description }}</p>

            <div class="mt-8 flex items-center justify-center gap-4">
                <button
                    @click="$inertia.visit(isLoggedIn ? '/dashboard' : '/')"
                    class="px-5 py-2.5 rounded-lg bg-primary-600 text-white font-medium hover:bg-primary-700 transition-colors"
                >
                    {{ isLoggedIn ? 'Go to Dashboard' : 'Go Home' }}
                </button>

                <button
                    @click="history.back()"
                    class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                    Go Back
                </button>
            </div>
        </div>
    </div>
</template>
