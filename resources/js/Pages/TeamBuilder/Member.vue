<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import ContactLinks from '@/Components/TeamBuilder/ContactLinks.vue';
import ProspectRow from '@/Components/TeamBuilder/ProspectRow.vue';
import TargetProgress from '@/Components/TeamBuilder/TargetProgress.vue';
import TargetModal from '@/Components/TeamBuilder/TargetModal.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { MEMBER_FLAG_META, formatDateTime } from '@/Utils/teamBuilder.js';

// Upline view of one team member: flag, this week's work, target and their prospects (names, statuses, dates only).
const props = defineProps({
    team: { type: Object, required: true },
    member: { type: Object, required: true },
});

const flag = computed(() => (props.member.flag ? MEMBER_FLAG_META[props.member.flag] : null));
const hasTarget = computed(() => !!props.member.target && (props.member.target.plans_per_week > 0 || props.member.target.follow_ups_per_week > 0));
const showTarget = ref(false);

const stats = computed(() => [
    { label: 'Plans this week', value: props.member.plans_week, color: 'text-gray-900 dark:text-gray-100' },
    { label: 'Follow-ups this week', value: props.member.follow_ups_week, color: 'text-gray-900 dark:text-gray-100' },
    { label: 'Joined this week', value: props.member.joined_week, color: 'text-green-600' },
    { label: 'Overdue follow-ups', value: props.member.overdue_follow_ups, color: props.member.overdue_follow_ups > 0 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' },
]);
</script>

<template>
    <Head :title="`${member.name} - ${team.name}`" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-4">
            <Link :href="route('team-builder.teams.show', team.id)" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <Icon name="chevron-left" class="h-4 w-4" />
                {{ team.name }}
            </Link>

            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <InitialsAvatar :name="member.name" :src="member.avatar_url" class="h-12 w-12 text-base" tone="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" />
                    <div class="min-w-0 flex-1">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ member.name }}</h2>
                        <p v-if="!member.is_direct && member.sponsor_name" class="text-sm text-gray-500">Under {{ member.sponsor_name }}</p>
                        <p class="text-xs text-gray-400">{{ member.last_active_at ? `Last active ${formatDateTime(member.last_active_at)}` : 'No activity logged yet' }}</p>
                    </div>
                    <ContactLinks :phone="member.phone" />
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 sm:grid-cols-4 dark:border-gray-700">
                    <div v-for="stat in stats" :key="stat.label" class="text-center">
                        <p class="text-2xl font-extrabold" :class="stat.color">{{ stat.value }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
                    </div>
                </div>
            </div>

            <div v-if="flag" class="flex items-start gap-2 rounded-xl p-4" :class="flag.badge">
                <Icon :name="flag.icon" class="h-5 w-5 flex-shrink-0" />
                <div>
                    <p class="font-bold">{{ flag.label }}</p>
                    <p class="text-sm">{{ flag.text }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 text-sm font-semibold text-gray-500 dark:text-gray-400">WEEKLY TARGET</h3>
                <TargetProgress v-if="hasTarget" :target="member.target" :plans="member.plans_week" :follow-ups="member.follow_ups_week" />
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">No target yet.</p>
                <button type="button" class="mt-3 text-sm font-bold text-amber-600 hover:text-amber-700" @click="showTarget = true">
                    {{ member.target ? 'Change target' : `Set a target for ${member.name.split(' ')[0]}` }}
                </button>
            </div>

            <section>
                <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">PROSPECTS ({{ member.prospects.length }})</h3>
                <p v-if="member.prospects.length === 0" class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                    {{ member.name }} has not added any prospects to this team yet.
                </p>
                <div class="space-y-2">
                    <ProspectRow v-for="prospect in member.prospects" :key="prospect.id" :prospect="prospect" />
                </div>
            </section>
        </div>

        <TargetModal
            :show="showTarget"
            :title="`Weekly target for ${member.name}`"
            :initial="member.target"
            :action="route('team-builder.teams.members.target', [team.id, member.user_id])"
            @close="showTarget = false"
        />
    </AppLayout>
</template>
