<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    group: Object,
});

const joinGroup = () => {
    router.post(route('groups.join'), {
        invite_code: props.group.invite_code,
    });
};
</script>

<template>
    <Head :title="`Join ${group.name}`" />

    <AppLayout>
        <div class="max-w-md mx-auto mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>

                <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Join "{{ group.name }}"</h2>
                <p v-if="group.description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ group.description }}</p>
                <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">{{ group.members_count }} member{{ group.members_count !== 1 ? 's' : '' }}</p>

                <button
                    @click="joinGroup"
                    class="mt-6 w-full px-5 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
                >
                    Join Group
                </button>
            </div>
        </div>
    </AppLayout>
</template>
