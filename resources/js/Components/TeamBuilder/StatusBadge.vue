<script setup>
import { PROSPECT_STATUS_META } from '@/Utils/teamBuilder.js';

defineProps({ status: { type: String, required: true } });
</script>

<template>
    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold" :class="PROSPECT_STATUS_META[status]?.badge">
        {{ PROSPECT_STATUS_META[status]?.label ?? status }}
    </span>
</template>
