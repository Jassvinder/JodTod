<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    requests: Array,
});

function approve(request) {
    router.post(route('contacts.approve', request.id), {}, { preserveScroll: true });
}

function reject(request) {
    router.delete(route('contacts.reject', request.id), { preserveScroll: true });
}

function getAvatarUrl(user) {
    return user.avatar_url ? `${user.avatar_url}` : null;
}

function getInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}
</script>

<template>
    <Head title="Contact Requests" />

    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-3">
                <Link :href="route('contacts.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Contact Requests</h2>
            </div>
            <p class="mt-1 text-gray-500 dark:text-gray-400 ml-8">Approve or decline requests to add you as a contact</p>

            <div class="mt-6">
                <div v-if="requests.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No pending requests</p>
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="request in requests"
                        :key="request.id"
                        class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700"
                    >
                        <div v-if="getAvatarUrl(request.user)" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            <img :src="getAvatarUrl(request.user)" :alt="request.user.name" class="w-full h-full object-cover" />
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(request.user.name) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate flex items-center gap-1.5">
                                {{ request.user.name }}
                                <span class="text-[11px] font-semibold text-primary-600 dark:text-primary-400 bg-primary-100 dark:bg-primary-900/30 rounded px-1.5 py-0.5">{{ request.user.friend_code }}</span>
                            </p>
                        </div>
                        <button
                            @click="reject(request)"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            title="Decline"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <button
                            @click="approve(request)"
                            class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-medium hover:bg-primary-700 transition-colors"
                        >
                            Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
