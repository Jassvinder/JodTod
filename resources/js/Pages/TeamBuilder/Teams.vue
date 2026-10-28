<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import TeamBuilderNav from '@/Components/TeamBuilder/TeamBuilderNav.vue';
import TeamCard from '@/Components/TeamBuilder/TeamCard.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    teams: { type: Array, default: () => [] },
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';
const labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300';

const showJoin = ref(false);
const joinForm = useForm({ invite_code: '' });

function openJoin() {
    joinForm.reset();
    joinForm.clearErrors();
    showJoin.value = true;
}

function submitJoin() {
    joinForm
        .transform((data) => ({ invite_code: data.invite_code.replace(/[^a-zA-Z0-9]/g, '').toUpperCase() }))
        .post(route('team-builder.teams.join'), { onSuccess: () => { showJoin.value = false; } });
}

const showCreate = ref(false);
const createForm = useForm({ name: '', company_name: '' });

function openCreate() {
    createForm.reset();
    createForm.clearErrors();
    showCreate.value = true;
}

function submitCreate() {
    createForm
        .transform((data) => ({ ...data, company_name: data.company_name || null }))
        .post(route('team-builder.teams.store'), { onSuccess: () => { showCreate.value = false; } });
}
</script>

<template>
    <Head title="Teams" />

    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <TeamBuilderNav title="Teams" subtitle="Everyone who joins with your invite code comes under you">
                <template #actions>
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700" @click="openJoin">
                        <Icon name="arrow-in" class="h-5 w-5" />
                        <span class="hidden sm:inline">Join Team</span>
                    </button>
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-600 px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-900/20" @click="openCreate">
                        <Icon name="plus" class="h-5 w-5" />
                        <span class="hidden sm:inline">Create Team</span>
                    </button>
                </template>
            </TeamBuilderNav>

            <div v-if="teams.length === 0" class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <EmptyState
                    icon="network"
                    tone="amber"
                    title="Build your team here"
                    text="Got an invite code from your sponsor? Join their team. Starting your own? Create a team and share your invite code."
                    action-label="Join Team"
                    action-icon="arrow-in"
                    secondary-label="Create Team"
                    @action="openJoin"
                    @secondary="openCreate"
                />
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2">
                <TeamCard v-for="team in teams" :key="team.id" :team="team" :href="route('team-builder.teams.show', team.id)" />
            </div>
        </div>

        <Modal :show="showJoin" max-width="md" @close="showJoin = false">
            <form class="p-6" @submit.prevent="submitJoin">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Join a Team</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your sponsor's invite code. They become your upline in this team.</p>
                <input v-model="joinForm.invite_code" type="text" maxlength="8" required placeholder="e.g. AB12CD34" :class="inputClass" class="mt-4 text-center font-mono text-lg font-bold uppercase tracking-widest" />
                <p v-if="joinForm.errors.invite_code" class="mt-1 text-xs text-red-600">{{ joinForm.errors.invite_code }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showJoin = false">Cancel</button>
                    <button type="submit" :disabled="joinForm.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">Join</button>
                </div>
            </form>
        </Modal>

        <Modal :show="showCreate" max-width="md" @close="showCreate = false">
            <form class="p-6" @submit.prevent="submitCreate">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create Team</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="team-name" :class="labelClass">Team name *</label>
                        <input id="team-name" v-model="createForm.name" type="text" maxlength="100" required placeholder="e.g. Rising Stars" :class="inputClass" />
                        <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="team-company" :class="labelClass">Company name</label>
                        <input id="team-company" v-model="createForm.company_name" type="text" maxlength="100" placeholder="Optional" :class="inputClass" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showCreate = false">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">Create</button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
