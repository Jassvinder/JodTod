<script setup>
import Icon from '@/Components/Shared/Icon.vue';
import { telLink, whatsAppLink } from '@/Utils/phone.js';

// Call and WhatsApp links for a phone number; renders nothing without one.
defineProps({ phone: { type: String, default: null } });
</script>

<template>
    <div v-if="phone" class="flex flex-shrink-0 items-center gap-1">
        <a :href="telLink(phone)" class="rounded-lg p-2 text-blue-600 transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Call" @click.stop>
            <Icon name="phone" class="h-4 w-4" />
        </a>
        <a :href="whatsAppLink(phone)" target="_blank" rel="noopener" class="rounded-lg p-2 text-green-600 transition-colors hover:bg-green-50 dark:hover:bg-green-900/20" title="WhatsApp" @click.stop>
            <Icon name="chat" class="h-4 w-4" />
        </a>
    </div>
</template>
