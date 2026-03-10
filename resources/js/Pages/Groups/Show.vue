<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    group: Object,
    isAdmin: Boolean,
});

const authUser = computed(() => usePage().props.auth.user);
const showInviteModal = ref(false);
const showDeleteModal = ref(false);
const showRemoveModal = ref(false);
const showAddMemberModal = ref(false);
const memberToRemove = ref(null);
const copied = ref(false);

// Add member search state
const phoneSearch = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const searchPerformed = ref(false);
let searchTimeout = null;

const inviteLink = computed(() => {
    return window.location.origin + route('groups.join.link', props.group.invite_code, false);
});

const copyInviteCode = () => {
    navigator.clipboard.writeText(props.group.invite_code);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const copyInviteLink = () => {
    navigator.clipboard.writeText(inviteLink.value);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const refreshCode = () => {
    router.post(route('groups.invite', props.group.id), {}, {
        preserveScroll: true,
    });
};

const deleteGroup = () => {
    router.delete(route('groups.destroy', props.group.id));
};

const confirmRemoveMember = (member) => {
    memberToRemove.value = member;
    showRemoveModal.value = true;
};

const removeMember = () => {
    router.delete(route('groups.members.remove', [props.group.id, memberToRemove.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            showRemoveModal.value = false;
            memberToRemove.value = null;
        },
    });
};

// Add member functionality
watch(phoneSearch, (val) => {
    if (searchTimeout) clearTimeout(searchTimeout);

    const digits = val.replace(/\D/g, '');
    if (digits.length < 3) {
        searchResults.value = [];
        isSearching.value = false;
        searchPerformed.value = false;
        return;
    }

    isSearching.value = true;
    searchPerformed.value = false;

    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get(route('groups.search-users', props.group.id), {
                params: { phone: digits },
            });
            searchResults.value = response.data;
        } catch (error) {
            searchResults.value = [];
        } finally {
            isSearching.value = false;
            searchPerformed.value = true;
        }
    }, 300);
});

const addMember = (user) => {
    router.post(route('groups.add-member', props.group.id), {
        user_id: user.id,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeAddMemberModal();
        },
    });
};

const closeAddMemberModal = () => {
    showAddMemberModal.value = false;
    phoneSearch.value = '';
    searchResults.value = [];
    isSearching.value = false;
    searchPerformed.value = false;
};

const getUserInitials = (name) => {
    return name ? name.charAt(0).toUpperCase() : '?';
};
</script>

<template>
    <Head :title="group.name" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <Link :href="route('groups.index')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ group.name }}</h1>
                        <p v-if="group.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ group.description }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="isAdmin"
                        @click="showAddMemberModal = true"
                        class="px-3 py-2 text-sm font-medium text-green-600 dark:text-green-400 border border-green-300 dark:border-green-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors"
                    >
                        Add Member
                    </button>
                    <Link
                        :href="route('groups.settlements.index', group.id)"
                        class="px-3 py-2 text-sm font-medium text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                    >
                        Settle Up
                    </Link>
                    <button
                        @click="showInviteModal = true"
                        class="px-3 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 transition-colors"
                    >
                        Invite
                    </button>
                    <Link
                        v-if="isAdmin"
                        :href="route('groups.edit', group.id)"
                        class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Edit
                    </Link>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Members ({{ group.members.length }})
                    </h2>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    <li
                        v-for="member in group.members"
                        :key="member.id"
                        class="flex items-center justify-between px-5 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                v-if="member.avatar"
                                :src="`/storage/${member.avatar}`"
                                :alt="member.name"
                                class="w-9 h-9 rounded-full object-cover"
                            />
                            <div v-else class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm">
                                {{ member.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ member.name }}
                                    <span v-if="member.id === authUser.id" class="text-gray-400 dark:text-gray-500">(You)</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ member.email }}
                                    <span v-if="member.phone">&middot; +91 {{ member.phone }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                v-if="member.pivot?.role === 'admin'"
                                class="px-2 py-0.5 text-xs font-medium bg-primary-100 text-primary-700 rounded-full"
                            >
                                Admin
                            </span>
                            <button
                                v-if="isAdmin && member.id !== authUser.id && member.pivot?.role !== 'admin'"
                                @click="confirmRemoveMember(member)"
                                class="text-xs text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium"
                            >
                                Remove
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Expenses Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Expenses</h2>
                    <Link
                        :href="route('groups.expenses.index', group.id)"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                    >
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Mini Balance Summary -->
                <div v-if="group.balances && group.balances.length" class="px-5 pb-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Balances</p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="balance in group.balances"
                            :key="balance.user_id"
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                            :class="balance.amount >= 0 ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'"
                        >
                            {{ balance.name }}: {{ balance.amount >= 0 ? '+' : '' }}&#8377;{{ Math.abs(balance.amount).toFixed(2) }}
                        </span>
                    </div>
                </div>

                <div v-else class="px-5 pb-4 text-sm text-gray-400 dark:text-gray-500">
                    No expenses yet.
                    <Link :href="route('groups.expenses.create', group.id)" class="text-primary-600 hover:text-primary-700 font-medium">
                        Add first expense
                    </Link>
                </div>
            </div>

            <!-- Actions -->
            <div v-if="isAdmin" class="mt-6 flex items-center gap-3">
                <button
                    @click="showDeleteModal = true"
                    class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                >
                    Delete Group
                </button>
            </div>
        </div>

        <!-- Invite Modal -->
        <Modal :show="showInviteModal" @close="showInviteModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invite Members</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Invite Code</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-lg tracking-widest font-mono text-center dark:text-gray-100">
                                {{ group.invite_code }}
                            </code>
                            <button @click="copyInviteCode" class="px-3 py-2.5 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50">
                                {{ copied ? 'Copied!' : 'Copy' }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Invite Link</p>
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                :value="inviteLink"
                                readonly
                                class="flex-1 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            />
                            <button @click="copyInviteLink" class="px-3 py-2.5 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 shrink-0">
                                Copy
                            </button>
                        </div>
                    </div>

                    <button
                        v-if="isAdmin"
                        @click="refreshCode"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Regenerate invite code
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showInviteModal = false">Close</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Add Member Modal -->
        <Modal :show="showAddMemberModal" @close="closeAddMemberModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Member</h2>

                <!-- Phone search input -->
                <div class="relative">
                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                        <span class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 text-sm text-gray-500 dark:text-gray-400 border-r border-gray-300 dark:border-gray-600">+91</span>
                        <input
                            v-model="phoneSearch"
                            type="text"
                            placeholder="Search by phone number"
                            class="flex-1 px-3 py-2.5 text-sm border-0 focus:ring-0 focus:outline-none dark:bg-gray-800 dark:text-gray-100"
                            maxlength="10"
                        />
                    </div>
                </div>

                <!-- Search status messages -->
                <div class="mt-4">
                    <p v-if="phoneSearch.replace(/\D/g, '').length < 3 && phoneSearch.length > 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        Type at least 3 digits to search
                    </p>

                    <p v-else-if="isSearching" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        Searching...
                    </p>

                    <p v-else-if="searchPerformed && searchResults.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        No registered user found with this phone number
                    </p>

                    <!-- Search results -->
                    <ul v-else-if="searchResults.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg max-h-60 overflow-y-auto">
                        <li
                            v-for="user in searchResults"
                            :key="user.id"
                            class="flex items-center justify-between px-4 py-3"
                        >
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="user.avatar"
                                    :src="`/storage/${user.avatar}`"
                                    :alt="user.name"
                                    class="w-9 h-9 rounded-full object-cover"
                                />
                                <div v-else class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm">
                                    {{ getUserInitials(user.name) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">+91 {{ user.phone }}</p>
                                </div>
                            </div>
                            <button
                                @click="addMember(user)"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
                            >
                                Add
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeAddMemberModal">Close</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete Group</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete "{{ group.name }}"? This will remove all members and group data. This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showDeleteModal = false">Cancel</SecondaryButton>
                    <DangerButton @click="deleteGroup">Delete Group</DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Remove Member Modal -->
        <Modal :show="showRemoveModal" @close="showRemoveModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Remove Member</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to remove <strong>{{ memberToRemove?.name }}</strong> from this group?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showRemoveModal = false">Cancel</SecondaryButton>
                    <DangerButton @click="removeMember">Remove</DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
