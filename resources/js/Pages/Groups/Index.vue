<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    groups: Array,
});
</script>

<template>
    <Head title="Groups" />

    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Groups</h1>
                <Link
                    :href="route('groups.create')"
                    class="px-4 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors"
                >
                    + Create Group
                </Link>
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
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ group.name }}</h3>
                            <p v-if="group.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">{{ group.description }}</p>
                        </div>
                        <div class="ml-4 flex items-center gap-3 shrink-0">
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
            <div v-else class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No groups yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a group and add members from your contacts.</p>
                <Link
                    :href="route('groups.create')"
                    class="mt-4 inline-flex px-5 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors"
                >
                    Create Your First Group
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
