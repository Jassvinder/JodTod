<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import FriendCodeBadge from '@/Components/Shared/FriendCodeBadge.vue';
import HelpButton from '@/Components/Onboarding/HelpButton.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import axios from 'axios';

const props = defineProps({
    contacts: Array,
    requestCount: {
        type: Number,
        default: 0,
    },
});

const myFriendCode = computed(() => usePage().props.auth?.user?.friend_code);

// Search users to add (by name or friend code)
const searchInput = ref(null);
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const searchPerformed = ref(false);
const sendingId = ref(null);

let searchTimeout = null;
let searchRequest = 0;
watch(searchQuery, (val) => {
    clearTimeout(searchTimeout);
    const query = val.trim();
    if (query.length < 3) {
        searchRequest++;
        searchResults.value = [];
        searchPerformed.value = false;
        isSearching.value = false;
        return;
    }
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        const requestId = ++searchRequest;
        try {
            const { data } = await axios.get(route('contacts.search'), { params: { q: query } });
            // Ignore responses from older searches that finish late.
            if (requestId === searchRequest) searchResults.value = data;
        } catch {
            if (requestId === searchRequest) searchResults.value = [];
        }
        if (requestId === searchRequest) {
            isSearching.value = false;
            searchPerformed.value = true;
        }
    }, 400);
});

function focusSearch() {
    searchInput.value?.scrollIntoView({ block: 'center', behavior: 'smooth' });
    searchInput.value?.focus({ preventScroll: true });
}

function sendRequest(user) {
    sendingId.value = user.id;
    router.post(route('contacts.store'), { contact_user_id: user.id }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Keep the user in the results so it is clear the request went out.
            searchResults.value = searchResults.value.map((u) => (u.id === user.id ? { ...u, request_status: 'sent' } : u));
        },
        onFinish: () => { sendingId.value = null; },
    });
}

async function removeContact(contact) {
    const confirmed = await confirmAction({
        title: 'Remove Contact?',
        text: `${contact.user.name} will be removed from your contacts.`,
        confirmText: 'Remove',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('contacts.destroy', contact.id), {
            preserveScroll: true,
        });
    }
}

function getInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}
</script>

<template>
    <Head title="Contacts" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Contacts</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Friends you split group expenses and share tasks with</p>
                </div>
                <div class="flex flex-shrink-0 items-center gap-2">
                    <HelpButton page="contacts" />
                    <Link
                        :href="route('contacts.requests')"
                        data-tip="contacts.requests"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        <Icon name="inbox" class="h-4 w-4" />
                        Requests
                        <span
                            v-if="requestCount"
                            class="inline-flex min-w-[20px] items-center justify-center rounded-full bg-accent-500 px-1.5 text-xs font-bold leading-5 text-white"
                        >
                            {{ requestCount }}
                        </span>
                    </Link>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-5 lg:items-start">
                <!-- Left: friend code + find friends -->
                <div class="space-y-6 lg:col-span-2 lg:sticky lg:top-24">
                    <div v-if="myFriendCode" data-tip="contacts.code" class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Your friend code</p>
                        <div class="mt-2">
                            <FriendCodeBadge :code="myFriendCode" copyable size="lg" />
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Share this code so friends can find you quickly. Click it to copy.</p>
                    </div>

                    <div data-tip="contacts.search" class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <label for="contact-search" class="text-sm font-semibold text-gray-900 dark:text-gray-100">Find friends</label>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Search JodTod users by name or friend code and send a request.</p>
                        <div class="relative mt-3">
                            <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="contact-search"
                                ref="searchInput"
                                v-model="searchQuery"
                                type="search"
                                autocomplete="off"
                                placeholder="Name or friend code..."
                                class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                            />
                            <span v-if="isSearching" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin rounded-full border-2 border-primary-500 border-t-transparent"></span>
                        </div>

                        <p v-if="searchQuery.trim().length > 0 && searchQuery.trim().length < 3" class="mt-2 text-xs text-gray-400">Type at least 3 characters to search</p>

                        <div v-if="searchQuery.trim().length >= 3 && !isSearching" class="mt-3">
                            <div v-if="searchResults.length > 0" class="space-y-2">
                                <div
                                    v-for="user in searchResults"
                                    :key="user.id"
                                    class="flex items-center gap-3 rounded-lg border border-gray-100 p-3 dark:border-gray-700"
                                >
                                    <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="h-10 w-10 flex-shrink-0 rounded-full object-cover" />
                                    <div v-else class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                                        <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(user.name) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                                        <FriendCodeBadge :code="user.friend_code" />
                                    </div>
                                    <Link
                                        v-if="user.request_status === 'sent'"
                                        :href="route('contacts.requests')"
                                        class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                        title="Request sent. Open Requests to cancel it."
                                    >
                                        <Icon name="check" class="h-3.5 w-3.5" />
                                        Requested
                                    </Link>
                                    <Link
                                        v-else-if="user.request_status === 'received'"
                                        :href="route('contacts.requests')"
                                        class="rounded-lg border border-primary-300 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-50 dark:border-primary-700 dark:text-primary-300 dark:hover:bg-primary-900/30"
                                        title="This person sent you a request"
                                    >
                                        Respond
                                    </Link>
                                    <button
                                        v-else
                                        type="button"
                                        :disabled="sendingId === user.id"
                                        class="inline-flex items-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                                        @click="sendRequest(user)"
                                    >
                                        <Icon name="user-plus" class="h-3.5 w-3.5" />
                                        Add
                                    </button>
                                </div>
                            </div>
                            <p v-else-if="searchPerformed" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No users found. Check the spelling or ask your friend for their friend code.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right: contact list -->
                <div class="lg:col-span-3">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        My Contacts ({{ contacts.length }})
                    </h3>

                    <div v-if="contacts.length === 0" class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <EmptyState
                            icon="users"
                            title="Add your first contact"
                            text="Contacts are friends you split group expenses and share tasks with. Find them by name or friend code and send a request. They show up here once they approve."
                            action-label="Find Friends"
                            action-icon="search"
                            @action="focusSearch"
                        />
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="contact in contacts"
                            :key="contact.id"
                            class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <img v-if="contact.user.avatar_url" :src="contact.user.avatar_url" :alt="contact.user.name" class="h-10 w-10 flex-shrink-0 rounded-full object-cover" />
                            <div v-else class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(contact.user.name) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <span class="truncate">{{ contact.user.name }}</span>
                                    <FriendCodeBadge :code="contact.user.friend_code" />
                                </p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ contact.user.email }}</p>
                            </div>
                            <button
                                type="button"
                                class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                title="Remove contact"
                                :aria-label="`Remove ${contact.user.name}`"
                                @click="removeContact(contact)"
                            >
                                <Icon name="trash" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
