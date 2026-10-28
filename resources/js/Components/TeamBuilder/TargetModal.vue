<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

// Weekly plans / follow-ups target, set by the user (Dashboard) or by their upline (Member page).
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, required: true },
    initial: { type: Object, default: null },
    action: { type: String, required: true },
});

const emit = defineEmits(['close']);

const form = useForm({ plans_per_week: 10, follow_ups_per_week: 20 });

watch(() => props.show, (open) => {
    if (!open) return;
    form.clearErrors();
    form.plans_per_week = props.initial?.plans_per_week ?? 10;
    form.follow_ups_per_week = props.initial?.follow_ups_per_week ?? 20;
});

function submit() {
    form.put(props.action, { preserveScroll: true, onSuccess: () => emit('close') });
}

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-center text-lg font-bold focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <form class="p-6" @submit.prevent="submit">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h2>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div>
                    <label for="target-plans" class="mb-1.5 block text-sm text-gray-600 dark:text-gray-300">Plans per week</label>
                    <input id="target-plans" v-model.number="form.plans_per_week" type="number" min="0" max="500" required :class="inputClass" />
                    <p v-if="form.errors.plans_per_week" class="mt-1 text-xs text-red-600">{{ form.errors.plans_per_week }}</p>
                </div>
                <div>
                    <label for="target-follow-ups" class="mb-1.5 block text-sm text-gray-600 dark:text-gray-300">Follow-ups per week</label>
                    <input id="target-follow-ups" v-model.number="form.follow_ups_per_week" type="number" min="0" max="500" required :class="inputClass" />
                    <p v-if="form.errors.follow_ups_per_week" class="mt-1 text-xs text-red-600">{{ form.errors.follow_ups_per_week }}</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-400">Use 0 for no target.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">Save</button>
            </div>
        </form>
    </Modal>
</template>
