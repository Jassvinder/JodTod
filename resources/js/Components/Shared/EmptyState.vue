<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';

// Empty list placeholder that explains the feature and offers the first action.
// Actions are links when an href is given, otherwise they emit `action` / `secondary`.
const props = defineProps({
    icon: { type: String, required: true },
    title: { type: String, required: true },
    text: { type: String, default: '' },
    tone: { type: String, default: 'primary' }, // primary | success | amber | gray
    actionLabel: { type: String, default: '' },
    actionHref: { type: String, default: '' },
    actionIcon: { type: String, default: 'plus' },
    secondaryLabel: { type: String, default: '' },
    secondaryHref: { type: String, default: '' },
    compact: { type: Boolean, default: false },
});

defineEmits(['action', 'secondary']);

// Literal class names so Tailwind generates them.
const TONES = {
    primary: {
        circle: 'bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-300',
        button: 'bg-primary-600 hover:bg-primary-700 focus-visible:outline-primary-600',
        link: 'text-primary-600 hover:text-primary-700 dark:text-primary-400',
    },
    success: {
        circle: 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300',
        button: 'bg-green-600 hover:bg-green-700 focus-visible:outline-green-600',
        link: 'text-green-600 hover:text-green-700 dark:text-green-400',
    },
    amber: {
        circle: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
        button: 'bg-amber-600 hover:bg-amber-700 focus-visible:outline-amber-600',
        link: 'text-amber-700 hover:text-amber-800 dark:text-amber-400',
    },
    gray: {
        circle: 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300',
        button: 'bg-primary-600 hover:bg-primary-700 focus-visible:outline-primary-600',
        link: 'text-primary-600 hover:text-primary-700 dark:text-primary-400',
    },
};

const tone = computed(() => TONES[props.tone] || TONES.primary);
const buttonClass = computed(() => [
    'inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
    tone.value.button,
]);
const secondaryClass = computed(() => ['text-sm font-medium transition-colors', tone.value.link]);
</script>

<template>
    <div class="flex flex-col items-center text-center" :class="compact ? 'px-4 py-8' : 'px-6 py-12 sm:py-16'">
        <div
            class="flex items-center justify-center rounded-full"
            :class="[tone.circle, compact ? 'h-14 w-14' : 'h-20 w-20']"
        >
            <Icon :name="icon" :class="compact ? 'h-7 w-7' : 'h-10 w-10'" stroke-width="1.6" />
        </div>
        <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100" :class="compact ? 'text-base' : 'text-lg'">{{ title }}</h3>
        <p v-if="text" class="mt-1.5 max-w-md text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ text }}</p>

        <div v-if="actionLabel || secondaryLabel" class="mt-5 flex flex-col items-center gap-3 sm:flex-row sm:gap-4">
            <template v-if="actionLabel">
                <Link v-if="actionHref" :href="actionHref" :class="buttonClass">
                    <Icon :name="actionIcon" class="h-4 w-4" />
                    {{ actionLabel }}
                </Link>
                <button v-else type="button" :class="buttonClass" @click="$emit('action')">
                    <Icon :name="actionIcon" class="h-4 w-4" />
                    {{ actionLabel }}
                </button>
            </template>
            <template v-if="secondaryLabel">
                <Link v-if="secondaryHref" :href="secondaryHref" :class="secondaryClass">{{ secondaryLabel }}</Link>
                <button v-else type="button" :class="secondaryClass" @click="$emit('secondary')">{{ secondaryLabel }}</button>
            </template>
        </div>
    </div>
</template>
