<script setup>
import Icon from '@/Components/Shared/Icon.vue';
import { MEMBER_FLAG_META } from '@/Utils/teamBuilder.js';

defineProps({ flag: { type: String, required: true } });
</script>

<template>
    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold" :class="MEMBER_FLAG_META[flag]?.badge" :title="MEMBER_FLAG_META[flag]?.text">
        <Icon :name="MEMBER_FLAG_META[flag]?.icon" class="h-3 w-3" />
        {{ MEMBER_FLAG_META[flag]?.label }}
    </span>
</template>
