<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';

// Dashboard summary card. Only the header links to the details page,
// so the amount below stays easy to select and copy.
const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, required: true },
    tone: { type: String, default: 'primary' }, // primary | green | red
    href: { type: String, default: '' },
});

const TONES = {
    primary: { header: 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300', hover: 'hover:bg-primary-100 dark:hover:bg-primary-900/50' },
    green: { header: 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300', hover: 'hover:bg-green-100 dark:hover:bg-green-900/50' },
    red: { header: 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300', hover: 'hover:bg-red-100 dark:hover:bg-red-900/50' },
};

const tone = computed(() => TONES[props.tone] || TONES.primary);
</script>

<template>
    <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <component
            :is="href ? Link : 'div'"
            :href="href || undefined"
            class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold transition-colors"
            :class="[tone.header, href ? tone.hover : '']"
        >
            <Icon :name="icon" class="h-4 w-4 flex-shrink-0" />
            <span class="flex-1 truncate">{{ title }}</span>
            <Icon v-if="href" name="chevron-right" class="h-4 w-4 flex-shrink-0 opacity-70" />
        </component>
        <div class="flex-1 px-4 py-3">
            <slot />
        </div>
    </div>
</template>
