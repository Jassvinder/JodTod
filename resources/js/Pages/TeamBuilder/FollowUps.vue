<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import TeamBuilderNav from '@/Components/TeamBuilder/TeamBuilderNav.vue';
import ProspectRow from '@/Components/TeamBuilder/ProspectRow.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    followUps: { type: Object, required: true },
});

const sections = computed(() => [
    { key: 'overdue', title: 'Overdue', color: 'text-red-600' },
    { key: 'today', title: 'Today', color: 'text-amber-600' },
    { key: 'upcoming', title: 'Upcoming', color: 'text-gray-500 dark:text-gray-400' },
].map((section) => ({ ...section, items: props.followUps[section.key] || [] })).filter((section) => section.items.length));
</script>

<template>
    <Head title="Follow-ups" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <TeamBuilderNav title="Follow-ups" subtitle="Who to call next" />

            <div v-if="sections.length === 0" class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <EmptyState
                    icon="clock"
                    tone="amber"
                    title="No follow-ups planned"
                    text="Open a prospect and set the next follow-up date. It shows up here, overdue ones first."
                    action-label="Open Prospects"
                    :action-href="route('team-builder.prospects.index')"
                    action-icon="chevron-right"
                />
            </div>

            <section v-for="section in sections" :key="section.key" class="mb-6">
                <h3 class="mb-2 text-sm font-bold uppercase" :class="section.color">{{ section.title }} ({{ section.items.length }})</h3>
                <div class="space-y-2">
                    <ProspectRow
                        v-for="prospect in section.items"
                        :key="prospect.id"
                        :prospect="prospect"
                        :href="route('team-builder.prospects.show', prospect.id)"
                    />
                </div>
            </section>
        </div>
    </AppLayout>
</template>
