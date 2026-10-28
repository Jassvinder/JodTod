<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import TeamBuilderNav from '@/Components/TeamBuilder/TeamBuilderNav.vue';
import ProspectRow from '@/Components/TeamBuilder/ProspectRow.vue';
import StatusBadge from '@/Components/TeamBuilder/StatusBadge.vue';
import ContactLinks from '@/Components/TeamBuilder/ContactLinks.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import {
    ACTIVITY_TYPES,
    ACTIVITY_TYPE_META,
    OPEN_PROSPECT_STATUSES,
    PROSPECT_STATUSES,
    PROSPECT_STATUS_META,
    formatDateTime,
    isOverdue,
    toLocalInput,
} from '@/Utils/teamBuilder.js';

const props = defineProps({
    prospects: { type: Array, default: () => [] },
    status_counts: { type: Object, default: () => ({}) },
    selected: { type: Object, default: null },
    teams: { type: Array, default: () => [] },
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';
const labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300';
const chipClass = (selected) => selected
    ? 'border-amber-600 bg-amber-600 text-white'
    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200';

// List filters (status comes from the dashboard link, e.g. ?status=joined)
const params = new URLSearchParams(window.location.search);
const statusFilter = ref(params.get('status') || 'all');
const search = ref('');
const total = computed(() => Object.values(props.status_counts).reduce((sum, n) => sum + n, 0));
const visible = computed(() => {
    const query = search.value.trim().toLowerCase();
    return props.prospects.filter((p) =>
        (statusFilter.value === 'all' || p.status === statusFilter.value)
        && (!query || p.name.toLowerCase().includes(query) || (p.phone || '').includes(query)));
});

// Add / edit prospect
const showForm = ref(false);
const editing = ref(false);
const form = useForm({ name: '', phone: '', source: '', team_id: '', status: 'new', next_follow_up_at: '', note: '' });
const formIsOpen = computed(() => OPEN_PROSPECT_STATUSES.includes(form.status));

function openAdd() {
    editing.value = false;
    form.reset();
    form.clearErrors();
    // New prospects go to the only team by default.
    form.team_id = props.teams.length === 1 ? props.teams[0].id : '';
    showForm.value = true;
}

function openEdit() {
    const p = props.selected;
    editing.value = true;
    form.clearErrors();
    Object.assign(form, {
        name: p.name, phone: p.phone || '', source: p.source || '', team_id: p.team_id || '',
        status: p.status, next_follow_up_at: toLocalInput(p.next_follow_up_at), note: p.note || '',
    });
    showForm.value = true;
}

function submitForm() {
    const payload = form.transform((data) => ({
        ...data,
        phone: data.phone || null,
        source: data.source || null,
        team_id: data.team_id || null,
        note: data.note || null,
        next_follow_up_at: OPEN_PROSPECT_STATUSES.includes(data.status) && data.next_follow_up_at ? data.next_follow_up_at.replace('T', ' ') : null,
    }));
    const options = { preserveScroll: true, onSuccess: () => { showForm.value = false; } };
    if (editing.value) payload.put(route('team-builder.prospects.update', props.selected.id), options);
    else payload.post(route('team-builder.prospects.store'), options);
}

async function deleteProspect() {
    const confirmed = await confirmAction({
        title: 'Delete Prospect?',
        text: `${props.selected.name} and all their activity will be permanently removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) router.delete(route('team-builder.prospects.destroy', props.selected.id));
}

// Log activity (inline in the detail panel)
const activityForm = useForm({ type: 'follow_up', note: '', status: '', next_follow_up_at: '' });
const activityIsOpen = computed(() => OPEN_PROSPECT_STATUSES.includes(activityForm.status));

function resetActivityForm() {
    activityForm.reset();
    activityForm.clearErrors();
    activityForm.status = props.selected?.status || 'new';
}

function chooseActivityType(type) {
    activityForm.type = type;
    // Showing the plan to a new prospect moves them to "Plan Shown".
    if (type === 'plan_shown' && activityForm.status === 'new') activityForm.status = 'plan_shown';
}

function submitActivity() {
    activityForm
        .transform((data) => ({
            ...data,
            note: data.note || null,
            // Empty means no follow-up is planned, so the old (done) one is cleared.
            next_follow_up_at: activityIsOpen.value && data.next_follow_up_at ? data.next_follow_up_at.replace('T', ' ') : null,
        }))
        .post(route('team-builder.prospects.activities.store', props.selected.id), {
            preserveScroll: true,
            onSuccess: resetActivityForm,
        });
}

async function deleteActivity(activity) {
    const confirmed = await confirmAction({
        title: 'Delete Activity?',
        text: `"${ACTIVITY_TYPE_META[activity.type].label}" on ${formatDateTime(activity.done_at)} will be removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('team-builder.prospects.activities.destroy', [props.selected.id, activity.id]), { preserveScroll: true });
    }
}

onMounted(() => {
    resetActivityForm();
    if (params.get('add')) openAdd();
});
</script>

<template>
    <Head :title="selected ? `${selected.name} - Prospects` : 'Prospects'" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <div :class="{ 'hidden lg:block': selected }">
                <TeamBuilderNav title="Prospects" subtitle="People you show the plan to, their status and follow-ups">
                    <template #actions>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700" @click="openAdd">
                            <Icon name="plus" class="h-5 w-5" />
                            <span class="hidden sm:inline">Add Prospect</span>
                        </button>
                    </template>
                </TeamBuilderNav>
            </div>

            <div v-if="total === 0" class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <EmptyState
                    icon="user-plus"
                    tone="amber"
                    title="Add the people you show the plan to"
                    text="Keep each prospect's status, notes and next follow-up in one place. Overdue follow-ups show on your Team Builder dashboard."
                    action-label="Add First Prospect"
                    @action="openAdd"
                />
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-5">
                <!-- List -->
                <section class="lg:col-span-2" :class="{ 'hidden lg:block': selected }">
                    <div class="relative mb-3">
                        <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input v-model="search" type="search" placeholder="Search by name or phone" :class="inputClass" class="pl-9" />
                    </div>
                    <div class="mb-3 flex gap-2 overflow-x-auto pb-1">
                        <button type="button" class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-semibold" :class="chipClass(statusFilter === 'all')" @click="statusFilter = 'all'">
                            All ({{ total }})
                        </button>
                        <button
                            v-for="status in PROSPECT_STATUSES"
                            :key="status"
                            type="button"
                            class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-semibold"
                            :class="chipClass(statusFilter === status)"
                            @click="statusFilter = status"
                        >
                            {{ PROSPECT_STATUS_META[status].label }} ({{ status_counts[status] ?? 0 }})
                        </button>
                    </div>
                    <p v-if="visible.length === 0" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No prospects match.</p>
                    <div class="space-y-2">
                        <ProspectRow
                            v-for="prospect in visible"
                            :key="prospect.id"
                            :prospect="prospect"
                            :href="route('team-builder.prospects.show', prospect.id)"
                            :active="selected?.id === prospect.id"
                        />
                    </div>
                </section>

                <!-- Detail -->
                <section class="lg:col-span-3">
                    <div v-if="selected" class="space-y-4">
                        <Link :href="route('team-builder.prospects.index')" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 lg:hidden">
                            <Icon name="chevron-left" class="h-4 w-4" />
                            All prospects
                        </Link>

                        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start gap-3">
                                <InitialsAvatar :name="selected.name" class="h-12 w-12 text-base" tone="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" />
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words text-xl font-bold text-gray-900 dark:text-gray-100">{{ selected.name }}</h3>
                                    <div class="mt-1"><StatusBadge :status="selected.status" /></div>
                                </div>
                                <ContactLinks :phone="selected.phone" />
                                <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700" title="Edit prospect" @click="openEdit">
                                    <Icon name="pencil" class="h-5 w-5" />
                                </button>
                                <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Delete prospect" @click="deleteProspect">
                                    <Icon name="trash" class="h-5 w-5" />
                                </button>
                            </div>
                            <dl class="mt-3 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                <div v-if="selected.phone">Phone: {{ selected.phone }}</div>
                                <div v-if="selected.team_name">Team: {{ selected.team_name }}</div>
                                <div v-if="selected.source">Met: {{ selected.source }}</div>
                                <div v-if="selected.joined_at">Joined on {{ formatDateTime(selected.joined_at) }}</div>
                                <div v-if="selected.note" class="whitespace-pre-line">{{ selected.note }}</div>
                            </dl>
                        </div>

                        <div
                            v-if="OPEN_PROSPECT_STATUSES.includes(selected.status)"
                            class="flex items-center gap-2 rounded-xl p-3 text-sm font-semibold"
                            :class="isOverdue(selected) ? 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300' : 'bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300'"
                        >
                            <Icon name="clock" class="h-5 w-5" />
                            <template v-if="selected.next_follow_up_at">
                                {{ isOverdue(selected) ? 'Follow-up overdue since' : 'Next follow-up:' }} {{ formatDateTime(selected.next_follow_up_at) }}
                            </template>
                            <template v-else>No follow-up planned</template>
                        </div>

                        <!-- Log activity -->
                        <form class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800" @submit.prevent="submitActivity">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Log Activity</h4>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    v-for="type in ACTIVITY_TYPES"
                                    :key="type"
                                    type="button"
                                    class="rounded-full border px-3 py-1 text-xs font-semibold"
                                    :class="chipClass(activityForm.type === type)"
                                    @click="chooseActivityType(type)"
                                >
                                    {{ ACTIVITY_TYPE_META[type].label }}
                                </button>
                            </div>
                            <input v-model="activityForm.note" type="text" maxlength="500" placeholder="Note (optional), e.g. asked to call after salary" :class="inputClass" class="mt-3" />
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="activity-status" :class="labelClass">Status</label>
                                    <select id="activity-status" v-model="activityForm.status" :class="inputClass">
                                        <option v-for="status in PROSPECT_STATUSES" :key="status" :value="status">{{ PROSPECT_STATUS_META[status].label }}</option>
                                    </select>
                                </div>
                                <div v-if="activityIsOpen">
                                    <label for="activity-follow-up" :class="labelClass">Next follow-up</label>
                                    <input id="activity-follow-up" v-model="activityForm.next_follow_up_at" type="datetime-local" :class="inputClass" />
                                    <p class="mt-1 text-xs text-gray-400">Leave empty if none.</p>
                                </div>
                            </div>
                            <p v-for="(error, key) in activityForm.errors" :key="key" class="mt-1 text-xs text-red-600">{{ error }}</p>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" :disabled="activityForm.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">Save Activity</button>
                            </div>
                        </form>

                        <!-- History -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            <h4 class="border-b border-gray-100 px-5 py-3 text-sm font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">History</h4>
                            <p v-if="!selected.activities?.length" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No activity yet. Log it after you call, show the plan or follow up.
                            </p>
                            <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                                <li v-for="activity in selected.activities" :key="activity.id" class="flex items-start gap-3 px-5 py-3">
                                    <Icon :name="ACTIVITY_TYPE_META[activity.type].icon" class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" />
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ACTIVITY_TYPE_META[activity.type].label }}</p>
                                        <p class="text-xs text-gray-400">{{ formatDateTime(activity.done_at) }}</p>
                                        <p v-if="activity.note" class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ activity.note }}</p>
                                    </div>
                                    <button type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Delete activity" @click="deleteActivity(activity)">
                                        <Icon name="trash" class="h-4 w-4" />
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div v-else class="hidden h-full min-h-[16rem] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-600 lg:flex">
                        <Icon name="user-plus" class="h-10 w-10 text-gray-300 dark:text-gray-600" stroke-width="1.5" />
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Select a prospect to log a call, plan or follow-up.</p>
                    </div>
                </section>
            </div>
        </div>

        <!-- Add / edit prospect -->
        <Modal :show="showForm" max-width="lg" @close="showForm = false">
            <form class="p-6" @submit.prevent="submitForm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ editing ? 'Edit Prospect' : 'Add Prospect' }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="p-name" :class="labelClass">Name *</label>
                        <input id="p-name" v-model="form.name" type="text" maxlength="100" required placeholder="e.g. Rahul Sharma" :class="inputClass" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label for="p-phone" :class="labelClass">Phone</label>
                        <input id="p-phone" v-model="form.phone" type="tel" maxlength="20" placeholder="For call and WhatsApp" :class="inputClass" />
                    </div>
                    <div>
                        <label for="p-source" :class="labelClass">Where you met</label>
                        <input id="p-source" v-model="form.source" type="text" maxlength="100" placeholder="e.g. College friend" :class="inputClass" />
                    </div>
                    <div v-if="teams.length">
                        <label for="p-team" :class="labelClass">Team</label>
                        <select id="p-team" v-model="form.team_id" :class="inputClass">
                            <option value="">No team</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="p-status" :class="labelClass">Status</label>
                        <select id="p-status" v-model="form.status" :class="inputClass">
                            <option v-for="status in PROSPECT_STATUSES" :key="status" :value="status">{{ PROSPECT_STATUS_META[status].label }}</option>
                        </select>
                    </div>
                    <div v-if="formIsOpen" class="sm:col-span-2">
                        <label for="p-follow-up" :class="labelClass">Next follow-up</label>
                        <input id="p-follow-up" v-model="form.next_follow_up_at" type="datetime-local" :class="inputClass" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="p-note" :class="labelClass">Note</label>
                        <textarea id="p-note" v-model="form.note" rows="3" maxlength="500" placeholder="Anything to remember about this person" :class="inputClass" />
                    </div>
                </div>
                <p v-if="Object.keys(form.errors).length && !form.errors.name" class="mt-2 text-xs text-red-600">{{ Object.values(form.errors)[0] }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showForm = false">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">{{ editing ? 'Save' : 'Add Prospect' }}</button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
