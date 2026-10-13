<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import FriendCodeBadge from '@/Components/Shared/FriendCodeBadge.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

defineProps({
    requests: { type: Array, default: () => [] },
    sentRequests: { type: Array, default: () => [] },
});

const busyId = ref(null);

function approve(request) {
    busyId.value = request.id;
    router.post(route('contacts.approve', request.id), {}, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
    });
}

function decline(request) {
    busyId.value = request.id;
    router.delete(route('contacts.reject', request.id), {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
    });
}

async function cancel(request) {
    const confirmed = await confirmAction({
        title: 'Cancel Request?',
        text: `Your contact request to ${request.contact_user.name} will be withdrawn.`,
        confirmText: 'Cancel Request',
        cancelText: 'Keep',
        danger: true,
    });
    if (!confirmed) return;
    busyId.value = request.id;
    router.delete(route('contacts.destroy', request.id), {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
    });
}

function getInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}
</script>

<template>
    <Head title="Contact Requests" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-3">
                <Link :href="route('contacts.index')" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300" aria-label="Back to contacts">
                    <Icon name="chevron-left" class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Contact Requests</h2>
            </div>
            <p class="mt-1 text-gray-500 dark:text-gray-400 sm:ml-10">Approve requests from friends, or cancel ones you sent</p>

            <div class="mt-6 grid gap-6 lg:grid-cols-2 lg:items-start">
                <!-- Received -->
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <Icon name="inbox" class="h-4 w-4" />
                        Received ({{ requests.length }})
                    </h3>

                    <div v-if="requests.length === 0" class="rounded-xl border border-gray-200 bg-white px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No pending requests. When someone adds you, it shows up here.</p>
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="request in requests"
                            :key="request.id"
                            class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <img v-if="request.user.avatar_url" :src="request.user.avatar_url" :alt="request.user.name" class="h-10 w-10 flex-shrink-0 rounded-full object-cover" />
                            <div v-else class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(request.user.name) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ request.user.name }}</p>
                                <FriendCodeBadge :code="request.user.friend_code" />
                            </div>
                            <button
                                type="button"
                                :disabled="busyId === request.id"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-600 disabled:opacity-60 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-red-900/20"
                                @click="decline(request)"
                            >
                                Decline
                            </button>
                            <button
                                type="button"
                                :disabled="busyId === request.id"
                                class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                                @click="approve(request)"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Sent -->
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <Icon name="arrow-out" class="h-4 w-4" />
                        Sent ({{ sentRequests.length }})
                    </h3>

                    <div v-if="sentRequests.length === 0" class="rounded-xl border border-gray-200 bg-white px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No requests waiting for approval.</p>
                        <Link :href="route('contacts.index')" class="mt-2 inline-block text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Find friends</Link>
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="request in sentRequests"
                            :key="request.id"
                            class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <img v-if="request.contact_user.avatar_url" :src="request.contact_user.avatar_url" :alt="request.contact_user.name" class="h-10 w-10 flex-shrink-0 rounded-full object-cover" />
                            <div v-else class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                <span class="text-sm font-bold text-gray-500 dark:text-gray-300">{{ getInitial(request.contact_user.name) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ request.contact_user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Waiting for approval</p>
                            </div>
                            <button
                                type="button"
                                :disabled="busyId === request.id"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-600 disabled:opacity-60 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-red-900/20"
                                @click="cancel(request)"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
