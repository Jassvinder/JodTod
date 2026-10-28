<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, default: '' },
    src: { type: String, default: null },
    // Tailwind classes for the circle without a photo.
    tone: { type: String, default: 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' },
});

const initials = computed(() => {
    const parts = (props.name || '').trim().split(/\s+/).filter(Boolean);
    if (parts.length === 0) return '?';
    if (parts.length === 1) return parts[0][0].toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

// Users without a photo get the default placeholder URL; show initials for them instead.
const showPhoto = computed(() => props.src && !props.src.includes('default-avatar'));
</script>

<template>
    <img v-if="showPhoto" :src="src" :alt="name" class="flex-shrink-0 rounded-full object-cover" />
    <div v-else class="flex flex-shrink-0 items-center justify-center rounded-full font-semibold" :class="tone">
        {{ initials }}
    </div>
</template>
