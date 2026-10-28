<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import TeamBuilderNav from '@/Components/TeamBuilder/TeamBuilderNav.vue';
import TargetProgress from '@/Components/TeamBuilder/TargetProgress.vue';
import TargetModal from '@/Components/TeamBuilder/TargetModal.vue';
import AnnouncementCard from '@/Components/TeamBuilder/AnnouncementCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { PROSPECT_STATUSES, PROSPECT_STATUS_META } from '@/Utils/teamBuilder.js';

const props = defineProps({
    dashboard: { type: Object, required: true },
});

const week = computed(() => props.dashboard.week);
const target = computed(() => props.dashboard.target);
const hasTarget = computed(() => !!target.value && (target.value.plans_per_week > 0 || target.value.follow_ups_per_week > 0));
const showTarget = ref(false);

const stats = computed(() => [
    { label: 'Plans shown', value: week.value.plans_shown, icon: 'chart', color: 'text-violet-600' },
    { label: 'Follow-ups', value: week.value.follow_ups, icon: 'refresh', color: 'text-amber-600' },
    { label: 'Calls', value: week.value.calls, icon: 'phone', color: 'text-blue-600' },
    { label: 'New prospects', value: week.value.new_prospects, icon: 'user-plus', color: 'text-cyan-600' },
    { label: 'Meetings', value: week.value.meetings, icon: 'users', color: 'text-pink-600' },
    { label: 'Joined', value: week.value.joined, icon: 'check', color: 'text-green-600' },
]);
</script>

<template>
    <Head title="Team Builder" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <TeamBuilderNav title="Dashboard" subtitle="Your network work at a glance">
                <template #actions>
                    <Link
                        :href="route('team-builder.prospects.index', { add: 1 })"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700"
                    >
                        <Icon name="plus" class="h-5 w-5" />
                        <span class="hidden sm:inline">Add Prospect</span>
                    </Link>
                </template>
            </TeamBuilderNav>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <!-- Follow-up alerts -->
                    <div class="grid grid-cols-2 gap-3">
                        <Link :href="route('team-builder.follow-ups')" class="rounded-xl bg-red-50 p-4 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30">
                            <p class="text-sm text-red-800 dark:text-red-300">Overdue follow-ups</p>
                            <p class="mt-1 text-3xl font-extrabold text-red-600">{{ dashboard.follow_ups.overdue }}</p>
                        </Link>
                        <Link :href="route('team-builder.follow-ups')" class="rounded-xl bg-amber-50 p-4 hover:bg-amber-100 dark:bg-amber-900/20 dark:hover:bg-amber-900/30">
                            <p class="text-sm text-amber-800 dark:text-amber-300">Due today</p>
                            <p class="mt-1 text-3xl font-extrabold text-amber-600">{{ dashboard.follow_ups.today }}</p>
                        </Link>
                    </div>

                    <!-- This week -->
                    <section>
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">THIS WEEK</h3>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div v-for="stat in stats" :key="stat.label" class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <Icon :name="stat.icon" class="h-5 w-5" :class="stat.color" />
                                <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ stat.value }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Prospects by status -->
                    <section>
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">MY PROSPECTS</h3>
                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                            <Link
                                v-for="status in PROSPECT_STATUSES"
                                :key="status"
                                :href="route('team-builder.prospects.index', { status })"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <span class="h-2.5 w-2.5 rounded-full" :class="PROSPECT_STATUS_META[status].dot" />
                                <span class="flex-1 text-sm text-gray-800 dark:text-gray-200">{{ PROSPECT_STATUS_META[status].label }}</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ dashboard.pipeline[status] ?? 0 }}</span>
                                <Icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                            </Link>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <!-- Weekly target -->
                    <section>
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">WEEKLY TARGET</h3>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <TargetProgress v-if="hasTarget" :target="target" :plans="week.plans_shown" :follow-ups="week.follow_ups" />
                            <p v-else class="text-sm text-gray-500 dark:text-gray-400">Set how many plans and follow-ups you want to do each week, and track it here.</p>
                            <button type="button" class="mt-3 text-sm font-bold text-amber-600 hover:text-amber-700" @click="showTarget = true">
                                {{ hasTarget ? 'Change target' : 'Set a target' }}
                            </button>
                        </div>
                    </section>

                    <!-- Newest announcement -->
                    <section v-if="dashboard.latest_announcement">
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">FROM YOUR TEAM</h3>
                        <Link :href="route('team-builder.teams.show', dashboard.latest_announcement.team_id)" class="block">
                            <AnnouncementCard :announcement="dashboard.latest_announcement" />
                        </Link>
                    </section>

                    <!-- Teams -->
                    <section>
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">MY TEAMS</h3>
                        <div v-if="dashboard.teams.length" class="space-y-2">
                            <Link
                                v-for="team in dashboard.teams"
                                :key="team.id"
                                :href="route('team-builder.teams.show', team.id)"
                                class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/50"
                            >
                                <Icon name="network" class="h-5 w-5 text-amber-600" />
                                <span class="flex-1 truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ team.name }}</span>
                                <span class="text-xs text-gray-500">{{ team.downline_count }} in team</span>
                            </Link>
                        </div>
                        <div v-else class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            <EmptyState
                                icon="network"
                                tone="amber"
                                title="You are not in a team yet"
                                text="Create your own team, or join your sponsor's team with their invite code."
                                action-label="Go to Teams"
                                :action-href="route('team-builder.teams.index')"
                                action-icon="chevron-right"
                                compact
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <TargetModal :show="showTarget" title="My weekly target" :initial="target" :action="route('team-builder.target.update')" @close="showTarget = false" />
    </AppLayout>
</template>
