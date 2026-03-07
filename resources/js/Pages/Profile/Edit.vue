<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <h2 class="text-2xl font-bold text-gray-900">Profile Settings</h2>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <UpdatePasswordForm />
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <DeleteUserForm />
            </div>
        </div>
    </AppLayout>
</template>
