<script setup>
import { ref } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';

// Readable friend code chip. With `copyable`, clicking copies the code and briefly shows "Copied".
const props = defineProps({
    code: { type: String, default: '' },
    copyable: { type: Boolean, default: false },
    size: { type: String, default: 'sm' }, // sm | lg
});

const copied = ref(false);
let resetTimer = null;

async function copy() {
    if (!props.copyable || !props.code) return;
    try {
        await navigator.clipboard.writeText(props.code);
    } catch {
        // Older browsers / insecure origins: fall back to a hidden textarea.
        const area = document.createElement('textarea');
        area.value = props.code;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        document.execCommand('copy');
        document.body.removeChild(area);
    }
    copied.value = true;
    clearTimeout(resetTimer);
    resetTimer = setTimeout(() => { copied.value = false; }, 1800);
}
</script>

<template>
    <component
        :is="copyable ? 'button' : 'span'"
        v-if="code"
        :type="copyable ? 'button' : undefined"
        class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 font-mono font-semibold tracking-wide text-indigo-800 ring-1 ring-inset ring-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-200 dark:ring-indigo-700/60"
        :class="[
            size === 'lg' ? 'px-3 py-1.5 text-base' : 'px-1.5 py-0.5 text-[11px]',
            copyable ? 'cursor-pointer transition-colors hover:bg-indigo-100 dark:hover:bg-indigo-900/60' : '',
        ]"
        :title="copyable ? 'Copy friend code' : undefined"
        @click="copy"
    >
        {{ code }}
        <template v-if="copyable">
            <Icon :name="copied ? 'check' : 'copy'" :class="size === 'lg' ? 'h-4 w-4' : 'h-3 w-3'" />
            <span class="sr-only">{{ copied ? 'Copied' : 'Copy' }}</span>
            <span v-if="copied" class="font-sans text-xs font-medium text-green-700 dark:text-green-300">Copied</span>
        </template>
    </component>
</template>
