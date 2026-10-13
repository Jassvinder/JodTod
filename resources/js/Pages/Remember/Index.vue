<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import HelpButton from '@/Components/Onboarding/HelpButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import { formatDaysAgo, formatShortDate, todayISO } from '@/Utils/dates.js';

const props = defineProps({
    items: { type: Array, default: () => [] },
    selected: { type: Object, default: null },
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';
const labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300';

// New item
const showCreate = ref(false);
const createForm = useForm({ name: '', note: '', done_on: todayISO() });

function openCreate() {
    createForm.reset();
    createForm.clearErrors();
    createForm.done_on = todayISO();
    showCreate.value = true;
}

function submitCreate() {
    createForm
        .transform((data) => ({ ...data, note: data.note || null, done_on: data.done_on || null }))
        .post(route('remember.store'), {
            preserveScroll: true,
            onSuccess: () => { showCreate.value = false; },
        });
}

// Edit item
const showEdit = ref(false);
const editForm = useForm({ name: '', note: '' });

function openEdit() {
    editForm.clearErrors();
    editForm.name = props.selected.name;
    editForm.note = props.selected.note || '';
    showEdit.value = true;
}

function submitEdit() {
    editForm
        .transform((data) => ({ ...data, note: data.note || null }))
        .put(route('remember.update', props.selected.id), {
            preserveScroll: true,
            onSuccess: () => { showEdit.value = false; },
        });
}

async function deleteItem() {
    const confirmed = await confirmAction({
        title: 'Delete Item?',
        text: `"${props.selected.name}" and all its dates will be permanently removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('remember.destroy', props.selected.id));
    }
}

// Add date (inline in the detail panel)
const entryForm = useForm({ done_on: todayISO(), note: '' });

function submitEntry() {
    entryForm
        .transform((data) => ({ ...data, note: data.note || null }))
        .post(route('remember.entries.store', props.selected.id), {
            preserveScroll: true,
            onSuccess: () => {
                entryForm.reset();
                entryForm.done_on = todayISO();
            },
        });
}

async function deleteEntry(entry) {
    const confirmed = await confirmAction({
        title: 'Delete Date?',
        text: `The entry for ${formatShortDate(entry.done_on)} will be removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('remember.entries.destroy', [props.selected.id, entry.id]), { preserveScroll: true });
    }
}
</script>

<template>
    <Head :title="selected ? `${selected.name} - Remember` : 'Remember'" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <!-- Header (on phones, hidden while an item is open so the detail gets the space) -->
            <div class="flex items-start justify-between gap-4" :class="{ 'hidden lg:flex': selected }">
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Remember</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Keep track of when recurring things last happened</p>
                </div>
                <div class="flex flex-shrink-0 items-center gap-2">
                    <HelpButton page="remember" />
                    <button
                        type="button"
                        data-tip="remember.add"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700"
                        @click="openCreate"
                    >
                        <Icon name="plus" class="h-5 w-5" />
                        <span class="hidden sm:inline">New Item</span>
                    </button>
                </div>
            </div>

            <!-- First-time empty state -->
            <div v-if="items.length === 0" class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <EmptyState
                    icon="bookmark"
                    tone="amber"
                    title="Never forget the little things"
                    text="When did you last change the gas cylinder, water filter or remote batteries? Save it here and add a new date each time. JodTod shows how many days have passed and how often it usually happens."
                    action-label="Add First Item"
                    @action="openCreate"
                />
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-5" :class="selected ? 'lg:mt-6' : 'mt-6'">
                <!-- Item list -->
                <section class="lg:col-span-2" :class="{ 'hidden lg:block': selected }">
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">{{ items.length }} {{ items.length === 1 ? 'item' : 'items' }}</p>
                    <div class="space-y-2">
                        <Link
                            v-for="item in items"
                            :key="item.id"
                            :href="route('remember.show', item.id)"
                            preserve-scroll
                            class="flex items-center gap-3 rounded-xl border bg-white p-4 transition-colors dark:bg-gray-800"
                            :class="selected?.id === item.id
                                ? 'border-primary-400 ring-1 ring-primary-400 dark:border-primary-500 dark:ring-primary-500'
                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50'"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ item.name }}</p>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    <template v-if="item.last_done_on">Last: {{ formatShortDate(item.last_done_on) }}</template>
                                    <template v-else>No dates added yet</template>
                                </p>
                                <p v-if="item.avg_gap_days !== null" class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                    Usually every ~{{ item.avg_gap_days }} days
                                </p>
                            </div>
                            <span
                                v-if="item.last_done_on"
                                class="flex-shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            >
                                {{ formatDaysAgo(item.last_done_on) }}
                            </span>
                            <Icon name="chevron-right" class="h-4 w-4 flex-shrink-0 text-gray-400" />
                        </Link>
                    </div>
                </section>

                <!-- Detail -->
                <section class="lg:col-span-3">
                    <div v-if="selected" class="space-y-4 lg:sticky lg:top-24">
                        <Link
                            :href="route('remember.index')"
                            class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden"
                        >
                            <Icon name="chevron-left" class="h-4 w-4" />
                            All items
                        </Link>

                        <!-- Summary -->
                        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words text-xl font-bold text-gray-900 dark:text-gray-100">{{ selected.name }}</h3>
                                    <p v-if="selected.note" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ selected.note }}</p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                    title="Edit item"
                                    @click="openEdit"
                                >
                                    <Icon name="pencil" class="h-5 w-5" />
                                </button>
                                <button
                                    type="button"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                    title="Delete item"
                                    @click="deleteItem"
                                >
                                    <Icon name="trash" class="h-5 w-5" />
                                </button>
                            </div>

                            <dl class="mt-4 grid grid-cols-3 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Last done</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ selected.last_done_on ? formatDaysAgo(selected.last_done_on) : '-' }}</dd>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Average gap</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ selected.avg_gap_days !== null ? `~${selected.avg_gap_days} days` : '-' }}</dd>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Times</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ selected.entries_count }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Add date -->
                        <form
                            class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                            @submit.prevent="submitEntry"
                        >
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add Date</h4>
                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start">
                                <div class="sm:w-44">
                                    <label for="entry-date" class="sr-only">Done on</label>
                                    <input id="entry-date" v-model="entryForm.done_on" type="date" :max="todayISO()" required :class="inputClass" />
                                    <p v-if="entryForm.errors.done_on" class="mt-1 text-xs text-red-600">{{ entryForm.errors.done_on }}</p>
                                </div>
                                <div class="flex-1">
                                    <label for="entry-note" class="sr-only">Note</label>
                                    <input id="entry-note" v-model="entryForm.note" type="text" maxlength="255" placeholder="Note (optional)" :class="inputClass" />
                                    <p v-if="entryForm.errors.note" class="mt-1 text-xs text-red-600">{{ entryForm.errors.note }}</p>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="entryForm.processing"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                                >
                                    <Icon name="plus" class="h-4 w-4" />
                                    Add
                                </button>
                            </div>
                        </form>

                        <!-- History -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            <h4 class="border-b border-gray-100 px-5 py-3 text-sm font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">History</h4>
                            <p v-if="!selected.entries?.length" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No dates yet. Add the first one above.
                            </p>
                            <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                                <li v-for="entry in selected.entries" :key="entry.id" class="flex items-center gap-3 px-5 py-3">
                                    <Icon name="calendar" class="h-5 w-5 flex-shrink-0 text-primary-500" />
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatShortDate(entry.done_on) }}</p>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatDaysAgo(entry.done_on) }}<template v-if="entry.note"> &middot; {{ entry.note }}</template>
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                        title="Delete date"
                                        @click="deleteEntry(entry)"
                                    >
                                        <Icon name="trash" class="h-4 w-4" />
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Nothing selected (wide screens only) -->
                    <div
                        v-else
                        class="hidden h-full min-h-[16rem] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-600 lg:flex"
                    >
                        <Icon name="bookmark" class="h-10 w-10 text-gray-300 dark:text-gray-600" stroke-width="1.5" />
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Select an item to see its history and add a new date.</p>
                    </div>
                </section>
            </div>
        </div>

        <!-- New item -->
        <Modal :show="showCreate" max-width="md" @close="showCreate = false">
            <form class="p-6" @submit.prevent="submitCreate">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">New Item</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="create-name" :class="labelClass">Name *</label>
                        <input id="create-name" v-model="createForm.name" type="text" maxlength="100" required placeholder="e.g. Gas cylinder" :class="inputClass" />
                        <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="create-note" :class="labelClass">Note</label>
                        <input id="create-note" v-model="createForm.note" type="text" maxlength="255" placeholder="Optional" :class="inputClass" />
                        <p v-if="createForm.errors.note" class="mt-1 text-xs text-red-600">{{ createForm.errors.note }}</p>
                    </div>
                    <div>
                        <label for="create-date" :class="labelClass">Last done on</label>
                        <input id="create-date" v-model="createForm.done_on" type="date" :max="todayISO()" :class="inputClass" />
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Leave empty if you don't know yet.</p>
                        <p v-if="createForm.errors.done_on" class="mt-1 text-xs text-red-600">{{ createForm.errors.done_on }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showCreate = false">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60">Add</button>
                </div>
            </form>
        </Modal>

        <!-- Edit item -->
        <Modal :show="showEdit" max-width="md" @close="showEdit = false">
            <form class="p-6" @submit.prevent="submitEdit">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Item</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="edit-name" :class="labelClass">Name *</label>
                        <input id="edit-name" v-model="editForm.name" type="text" maxlength="100" required :class="inputClass" />
                        <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-600">{{ editForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="edit-note" :class="labelClass">Note</label>
                        <input id="edit-note" v-model="editForm.note" type="text" maxlength="255" placeholder="Optional" :class="inputClass" />
                        <p v-if="editForm.errors.note" class="mt-1 text-xs text-red-600">{{ editForm.errors.note }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showEdit = false">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60">Update</button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
