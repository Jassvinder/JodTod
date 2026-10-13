<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import FriendCodeBadge from '@/Components/Shared/FriendCodeBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const friendCode = computed(() => usePage().props.auth?.user?.friend_code);
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Profile Settings</h2>

            <div v-if="friendCode" class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Your Friend Code</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Friends can find you by this code in
                        <Link :href="route('contacts.index')" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Contacts</Link>.
                        Click it to copy.
                    </p>
                </div>
                <FriendCodeBadge :code="friendCode" copyable size="lg" class="self-start sm:self-auto" />
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <UpdatePasswordForm />
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <DeleteUserForm />
            </div>
        </div>
    </AppLayout>
</template>
