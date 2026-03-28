<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import axios from 'axios';

const props = defineProps({
    contacts: Array,
});

// Search
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const searchPerformed = ref(false);

let searchTimeout = null;
watch(searchQuery, (val) => {
    clearTimeout(searchTimeout);
    if (val.length < 3) {
        searchResults.value = [];
        searchPerformed.value = false;
        return;
    }
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get(route('contacts.search'), { params: { q: val } });
            searchResults.value = data;
        } catch {}
        isSearching.value = false;
        searchPerformed.value = true;
    }, 400);
});

function addContact(user) {
    router.post(route('contacts.store'), { contact_user_id: user.id }, {
        preserveScroll: true,
        onSuccess: () => {
            searchResults.value = searchResults.value.filter(u => u.id !== user.id);
        },
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

function getAvatarUrl(user) {
    return user.avatar ? `/storage/${user.avatar}` : null;
}

function getInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}
</script>

<template>
    <Head title="Contacts" />

    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Contacts</h2>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Add JodTod users to quickly add them to groups or assign tasks</p>
            </div>

            <!-- Search -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, email, or phone..."
                        class="w-full py-3 pl-10 pr-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                    />
                </div>

                <!-- Search Results -->
                <div v-if="searchQuery.length >= 3" class="mt-3">
                    <div v-if="isSearching" class="text-center py-4">
                        <p class="text-sm text-gray-400">Searching...</p>
                    </div>
                    <div v-else-if="searchResults.length > 0" class="space-y-2">
                        <div
                            v-for="user in searchResults"
                            :key="user.id"
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <div v-if="getAvatarUrl(user)" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                <img :src="getAvatarUrl(user)" :alt="user.name" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(user.name) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ user.name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ user.email || user.phone }}</p>
                            </div>
                            <button
                                @click="addContact(user)"
                                class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-medium hover:bg-primary-700 transition-colors"
                            >
                                Add
                            </button>
                        </div>
                    </div>
                    <div v-else-if="searchPerformed" class="text-center py-4">
                        <p class="text-sm text-gray-400">No users found</p>
                    </div>
                </div>
                <p v-else-if="searchQuery.length > 0" class="mt-2 text-xs text-gray-400">Type at least 3 characters to search</p>
            </div>

            <!-- Contact List -->
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    My Contacts ({{ contacts.length }})
                </h3>

                <div v-if="contacts.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No contacts yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Search above to add JodTod users</p>
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="contact in contacts"
                        :key="contact.id"
                        class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 group"
                    >
                        <div v-if="getAvatarUrl(contact.user)" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            <img :src="getAvatarUrl(contact.user)" :alt="contact.user.name" class="w-full h-full object-cover" />
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ getInitial(contact.user.name) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ contact.user.name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ contact.user.email || contact.user.phone }}</p>
                        </div>
                        <button
                            @click="removeContact(contact)"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 opacity-0 group-hover:opacity-100 transition-all"
                            title="Remove contact"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
