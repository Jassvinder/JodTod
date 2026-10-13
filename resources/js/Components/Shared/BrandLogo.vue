<script setup>
// JodTod logo; set the height with a class (e.g. h-9). `iconOnly` shows just the round mark, for tight spots such as a collapsed sidebar.
defineProps({
    iconOnly: { type: Boolean, default: false },
});

// Bump when a logo file changes so browsers fetch the new image.
const VERSION = 2;
</script>

<template>
    <img
        v-if="iconOnly"
        :src="`/images/Logo/logo-icon.webp?v=${VERSION}`"
        alt="JodTod"
        width="128"
        height="123"
        class="w-auto"
    />
    <img
        v-else
        :src="`/images/Logo/logo.webp?v=${VERSION}`"
        alt="JodTod"
        class="w-auto"
    />
</template>
