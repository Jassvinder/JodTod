<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Shared/Icon.vue';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});

const tabs = [
    { name: 'Dashboard', route: 'team-builder.dashboard', match: 'team-builder.dashboard', icon: 'chart' },
    { name: 'Prospects', route: 'team-builder.prospects.index', match: 'team-builder.prospects.*', icon: 'user-plus' },
    { name: 'Follow-ups', route: 'team-builder.follow-ups', match: 'team-builder.follow-ups', icon: 'clock' },
    { name: 'Teams', route: 'team-builder.teams.index', match: 'team-builder.teams.*', icon: 'network' },
];
</script>

<template>
    <!-- Header and section tabs shared by every Team Builder page -->
    <div class="mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Team Builder</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ title }}</h2>
                <p v-if="subtitle" class="mt-1 text-gray-500 dark:text-gray-400">{{ subtitle }}</p>
            </div>
            <div class="flex flex-shrink-0 items-center gap-2">
                <slot name="actions" />
            </div>
        </div>
        <nav class="mt-4 flex gap-1 overflow-x-auto border-b border-gray-200 dark:border-gray-700">
            <Link
                v-for="tab in tabs"
                :key="tab.route"
                :href="route(tab.route)"
                class="-mb-px inline-flex items-center gap-1.5 whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition-colors"
                :class="route().current(tab.match)
                    ? 'border-amber-500 text-amber-700 dark:text-amber-300'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
            >
                <Icon :name="tab.icon" class="h-4 w-4" />
                {{ tab.name }}
            </Link>
        </nav>
    </div>
</template>
