<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';
import ContactLinks from '@/Components/TeamBuilder/ContactLinks.vue';
import { copyText } from '@/Utils/clipboard.js';
import { whatsAppLink } from '@/Utils/phone.js';

// Team summary with my sponsor, my invite code and team size. Used on Teams and the team page.
const props = defineProps({
    team: { type: Object, required: true },
    href: { type: String, default: '' },
});

const copied = ref(false);

const inviteMessage = computed(() =>
    `Join my team "${props.team.name}"${props.team.company_name ? ` (${props.team.company_name})` : ''} on JodTod Team Builder.\n\n`
    + '1. Install JodTod and choose Team Builder\n'
    + '2. Open Team > Join Team\n'
    + `3. Enter my invite code: ${props.team.invite_code}`);

async function copyCode() {
    await copyText(props.team.invite_code);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 1500);
}
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <component :is="href ? Link : 'div'" :href="href || undefined" class="flex items-start gap-2" :class="{ 'group': href }">
            <div class="min-w-0 flex-1">
                <p class="truncate text-lg font-bold text-gray-900 group-hover:text-amber-700 dark:text-gray-100">{{ team.name }}</p>
                <p v-if="team.company_name" class="text-sm text-gray-500 dark:text-gray-400">{{ team.company_name }}</p>
            </div>
            <span v-if="team.is_owner" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Leader</span>
            <Icon v-if="href" name="chevron-right" class="h-5 w-5 text-gray-400" />
        </component>

        <p class="mt-3 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
            <Icon name="users" class="h-4 w-4 text-gray-400" />
            <span class="font-bold text-gray-900 dark:text-gray-100">{{ team.downline_count }}</span> {{ team.downline_count === 1 ? 'person' : 'people' }} in my team
        </p>

        <div v-if="team.sponsor" class="mt-2 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <span class="flex-1">Sponsor: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ team.sponsor.name }}</span></span>
            <ContactLinks :phone="team.sponsor.phone" />
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3 dark:border-gray-700">
            <span class="flex-1 text-sm text-gray-600 dark:text-gray-300">
                My invite code: <span class="font-mono font-bold tracking-wider text-gray-900 dark:text-gray-100">{{ team.invite_code }}</span>
            </span>
            <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="copyCode">
                <Icon :name="copied ? 'check' : 'copy'" class="h-3.5 w-3.5" />
                {{ copied ? 'Copied' : 'Copy' }}
            </button>
            <a :href="whatsAppLink(null, inviteMessage)" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-lg bg-amber-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-amber-700">
                <Icon name="share" class="h-3.5 w-3.5" />
                Invite on WhatsApp
            </a>
        </div>
    </div>
</template>
