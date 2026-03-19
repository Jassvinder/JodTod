<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
});

function submit() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <Head :title="`Edit User: ${user.name}`" />

    <AdminLayout>
        <div class="max-w-2xl mx-auto">
            <!-- Back Link -->
            <Link
                :href="route('admin.users.show', user.id)"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors mb-6"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </Link>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit User</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update {{ user.name }}'s information.</p>

                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <TextInput v-model="form.name" type="text" class="w-full" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <TextInput :model-value="user.email" type="email" class="w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" disabled />
                        <p class="mt-1 text-xs text-gray-400">Email can only be changed by the user (requires verification).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <TextInput :model-value="user.phone || '-'" type="text" class="w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" disabled />
                        <p class="mt-1 text-xs text-gray-400">Phone can only be changed by the user (requires verification).</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <Link
                            :href="route('admin.users.show', user.id)"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Cancel
                        </Link>
                        <PrimaryButton type="submit" :disabled="form.processing">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
