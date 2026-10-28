<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import StatusBadge from '@/Components/TeamBuilder/StatusBadge.vue';
import ContactLinks from '@/Components/TeamBuilder/ContactLinks.vue';
import { OPEN_PROSPECT_STATUSES, formatDateTime, isOverdue } from '@/Utils/teamBuilder.js';

// One prospect row: Prospects list, Follow-ups and the upline's member page (no href, no phone there).
const props = defineProps({
    prospect: { type: Object, required: true },
    href: { type: String, default: '' },
    active: { type: Boolean, default: false },
});

const showFollowUp = computed(() => !!props.prospect.next_follow_up_at && OPEN_PROSPECT_STATUSES.includes(props.prospect.status));
const overdue = computed(() => isOverdue(props.prospect));
</script>

<template>
    <div
        class="flex items-center gap-1 rounded-xl border bg-white transition-colors dark:bg-gray-800"
        :class="[
            active ? 'border-amber-400 ring-1 ring-amber-400' : overdue ? 'border-red-200 dark:border-red-900' : 'border-gray-200 dark:border-gray-700',
            href && !active ? 'hover:bg-gray-50 dark:hover:bg-gray-700/50' : '',
        ]"
    >
        <!-- The link covers the text only, so the call / WhatsApp links are not nested inside it. -->
        <component :is="href ? Link : 'div'" :href="href || undefined" preserve-scroll class="flex min-w-0 flex-1 items-center gap-3 p-3">
            <InitialsAvatar :name="prospect.name" class="h-10 w-10 text-sm" tone="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" />
            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ prospect.name }}</p>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <StatusBadge :status="prospect.status" />
                    <span v-if="prospect.team_name" class="truncate text-xs text-gray-400">{{ prospect.team_name }}</span>
                </div>
                <p v-if="showFollowUp" class="mt-1 flex items-center gap-1 text-xs" :class="overdue ? 'font-semibold text-red-600' : 'text-gray-500 dark:text-gray-400'">
                    <Icon name="clock" class="h-3.5 w-3.5" />
                    {{ overdue ? 'Overdue: ' : '' }}{{ formatDateTime(prospect.next_follow_up_at) }}
                </p>
            </div>
        </component>
        <ContactLinks :phone="prospect.phone" class="pr-2" />
    </div>
</template>
