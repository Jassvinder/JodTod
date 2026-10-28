<script setup>
// This week's plans and follow-ups against a weekly target. Rows with a 0 target are hidden.
defineProps({
    target: { type: Object, required: true },
    plans: { type: Number, default: 0 },
    followUps: { type: Number, default: 0 },
});

function pct(done, goal) {
    return goal > 0 ? Math.min(100, Math.round((done / goal) * 100)) : 0;
}
</script>

<template>
    <div class="space-y-3">
        <template v-for="row in [
            { label: 'Plans shown', done: plans, goal: target.plans_per_week },
            { label: 'Follow-ups', done: followUps, goal: target.follow_ups_per_week },
        ]" :key="row.label">
            <div v-if="row.goal > 0">
                <div class="mb-1 flex justify-between text-sm">
                    <span class="text-gray-700 dark:text-gray-300">{{ row.label }}</span>
                    <span class="font-bold" :class="row.done >= row.goal ? 'text-green-600' : 'text-gray-900 dark:text-gray-100'">{{ row.done }} / {{ row.goal }}</span>
                </div>
                <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 rounded-full" :class="row.done >= row.goal ? 'bg-green-500' : 'bg-amber-500'" :style="{ width: `${pct(row.done, row.goal)}%` }" />
                </div>
            </div>
        </template>
        <p v-if="target.set_by_name" class="text-xs text-gray-400">Target set by {{ target.set_by_name }}</p>
    </div>
</template>
