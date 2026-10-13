<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import HelpButton from '@/Components/Onboarding/HelpButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

defineProps({
    groups: Array,
});

// Join a group with an invite code
const showJoin = ref(false);
const joinInput = ref(null);
const joinForm = useForm({ invite_code: '' });

function openJoin() {
    joinForm.reset();
    joinForm.clearErrors();
    showJoin.value = true;
    nextTick(() => joinInput.value?.focus());
}

function submitJoin() {
    joinForm
        .transform((data) => ({ invite_code: data.invite_code.trim().toUpperCase() }))
        .post(route('groups.join'), {
            preserveScroll: true,
            onSuccess: () => { showJoin.value = false; },
        });
}
</script>

<template>
    <Head title="Groups" />

    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Groups</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Split shared expenses and settle up</p>
                </div>
                <div class="flex flex-shrink-0 items-center gap-2">
                    <HelpButton page="groups" />
                    <button
                        type="button"
                        data-tip="groups.join"
                        title="Join with an invite code"
                        class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        @click="openJoin"
                    >
                        <Icon name="user-plus" class="w-4 h-4" />
                        <span class="hidden sm:inline">Join Group</span>
                    </button>
                    <Link
                        :href="route('groups.create')"
                        data-tip="groups.create"
                        title="Create Group"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors"
                    >
                        <Icon name="plus" class="w-4 h-4" />
                        <span class="hidden sm:inline">Create Group</span>
                    </Link>
                </div>
            </div>

            <!-- Groups List -->
            <div v-if="groups.length > 0" class="space-y-3">
                <Link
                    v-for="group in groups"
                    :key="group.id"
                    :href="route('groups.show', group.id)"
                    class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-sm transition-all"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <img
                                v-if="group.photo_url"
                                :src="group.photo_url"
                                :alt="group.name"
                                class="w-11 h-11 rounded-full object-cover shrink-0"
                            />
                            <div v-else class="w-11 h-11 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-base shrink-0">
                                {{ group.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ group.name }}</h3>
                                <p v-if="group.description" class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ group.description }}</p>
                            </div>
                        </div>
                        <div class="ml-4 flex items-center gap-3 shrink-0">
                            <span v-if="group.is_all_settled" class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                All Settled
                            </span>
                            <span v-if="group.pivot?.role === 'admin'" class="px-2 py-0.5 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                                Admin
                            </span>
                            <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                {{ group.members_count }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <EmptyState
                    icon="users"
                    title="Split bills with friends"
                    text="Create a group for a trip, flat or event. Add friends, record shared expenses, and JodTod shows who owes whom."
                    action-label="Create Group"
                    :action-href="route('groups.create')"
                    secondary-label="Have an invite code? Join a group"
                    @secondary="openJoin"
                />
            </div>
        </div>

        <!-- Join with invite code -->
        <Modal :show="showJoin" max-width="md" @close="showJoin = false">
            <form class="p-6" @submit.prevent="submitJoin">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Join a Group</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the 8-character invite code a group member shared with you. The group admin approves new members.</p>
                <label for="invite-code" class="sr-only">Invite code</label>
                <input
                    id="invite-code"
                    ref="joinInput"
                    v-model="joinForm.invite_code"
                    type="text"
                    maxlength="8"
                    autocomplete="off"
                    placeholder="e.g. AB12CD34"
                    class="mt-4 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-center font-mono text-lg uppercase tracking-[0.3em] focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                />
                <p v-if="joinForm.errors.invite_code" class="mt-2 text-sm text-red-600">{{ joinForm.errors.invite_code }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showJoin = false">Cancel</button>
                    <button
                        type="submit"
                        :disabled="joinForm.processing || joinForm.invite_code.trim().length !== 8"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60"
                    >
                        Join
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
