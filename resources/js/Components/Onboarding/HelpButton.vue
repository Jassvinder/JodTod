<script setup>
import { onMounted } from 'vue';
import { maybeStartTour, startTour } from '@/Utils/onboarding.js';

// "?" button for a page header: shows the page's tips on the first visit and replays them on click.
const props = defineProps({
    page: { type: String, required: true },
});

onMounted(() => maybeStartTour(props.page));
</script>

<template>
    <button
        type="button"
        class="inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border border-gray-300 text-sm font-semibold text-gray-500 transition-colors hover:border-primary-400 hover:bg-primary-50 hover:text-primary-600 dark:border-gray-600 dark:text-gray-400 dark:hover:border-primary-500 dark:hover:bg-primary-900/30 dark:hover:text-primary-300"
        title="Show tips for this page"
        aria-label="Show tips for this page"
        @click="startTour(page)"
    >
        ?
    </button>
</template>
