<script setup>
import Icon from '@/Components/Shared/Icon.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import { formatDateTime } from '@/Utils/teamBuilder.js';

// One team announcement: author, message and an optional link (training video, meeting).
defineProps({
    announcement: { type: Object, required: true },
    deletable: { type: Boolean, default: false },
});

defineEmits(['delete']);
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-2">
            <InitialsAvatar :name="announcement.author_name" :src="announcement.author_avatar_url" class="h-8 w-8 text-xs" tone="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" />
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ announcement.is_mine ? 'You' : announcement.author_name }}</p>
                <p class="text-xs text-gray-400">{{ formatDateTime(announcement.created_at) }}<template v-if="announcement.team_name"> &middot; {{ announcement.team_name }}</template></p>
            </div>
            <button v-if="deletable" type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Delete announcement" @click="$emit('delete')">
                <Icon name="trash" class="h-4 w-4" />
            </button>
        </div>
        <p class="mt-2 whitespace-pre-line text-sm text-gray-800 dark:text-gray-200">{{ announcement.message }}</p>
        <a v-if="announcement.link" :href="announcement.link" target="_blank" rel="noopener" class="mt-2 inline-flex max-w-full items-center gap-1 text-sm text-blue-600 hover:underline">
            <Icon name="link" class="h-4 w-4 flex-shrink-0" />
            <span class="truncate">{{ announcement.link }}</span>
        </a>
    </div>
</template>
