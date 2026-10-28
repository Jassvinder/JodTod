<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import { formatShortDate, todayISO } from '@/Utils/dates.js';
import { formatCurrency } from '@/Utils/money.js';
import { telLink, whatsAppLink } from '@/Utils/phone.js';

const props = defineProps({
    people: { type: Array, default: () => [] },
    totals: { type: Object, required: true },
    selected: { type: Object, default: null },
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';
const labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300';

const search = ref('');
const visiblePeople = computed(() => {
    const query = search.value.trim().toLowerCase();
    return query ? props.people.filter((p) => p.name.toLowerCase().includes(query)) : props.people;
});

// balance > 0: they owe me (green). balance < 0: I owe them (red).
function balanceLabel(balance) {
    if (balance === 0) return 'Settled';
    return balance > 0 ? 'You will get' : 'You will give';
}
function balanceClass(balance) {
    if (balance === 0) return 'text-gray-400';
    return balance > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
}

// Add / edit person
const showPersonForm = ref(false);
const personForm = useForm({ name: '', phone: '', note: '' });
const editingPerson = ref(false);

function openAddPerson() {
    editingPerson.value = false;
    personForm.reset();
    personForm.clearErrors();
    showPersonForm.value = true;
}

function openEditPerson() {
    editingPerson.value = true;
    personForm.clearErrors();
    personForm.name = props.selected.name;
    personForm.phone = props.selected.phone || '';
    personForm.note = props.selected.note || '';
    showPersonForm.value = true;
}

function submitPerson() {
    const form = personForm.transform((data) => ({ ...data, phone: data.phone || null, note: data.note || null }));
    const options = { preserveScroll: true, onSuccess: () => { showPersonForm.value = false; } };
    if (editingPerson.value) {
        form.put(route('ledger.update', props.selected.id), options);
    } else {
        form.post(route('ledger.store'), options);
    }
}

async function deletePerson() {
    const confirmed = await confirmAction({
        title: 'Delete Person?',
        text: `${props.selected.name} and all ${props.selected.entries_count} entries will be permanently removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('ledger.destroy', props.selected.id));
    }
}

// Add / edit entry
const showEntryForm = ref(false);
const editingEntryId = ref(null);
const entryForm = useForm({ type: 'gave', amount: '', entry_date: todayISO(), note: '' });

function openAddEntry(type) {
    editingEntryId.value = null;
    entryForm.reset();
    entryForm.clearErrors();
    entryForm.type = type;
    entryForm.entry_date = todayISO();
    showEntryForm.value = true;
}

function openEditEntry(entry) {
    editingEntryId.value = entry.id;
    entryForm.clearErrors();
    entryForm.type = entry.type;
    entryForm.amount = entry.amount;
    entryForm.entry_date = entry.entry_date;
    entryForm.note = entry.note || '';
    showEntryForm.value = true;
}

function submitEntry() {
    const form = entryForm.transform((data) => ({ ...data, note: data.note || null }));
    const options = { preserveScroll: true, onSuccess: () => { showEntryForm.value = false; } };
    if (editingEntryId.value) {
        form.put(route('ledger.entries.update', [props.selected.id, editingEntryId.value]), options);
    } else {
        form.post(route('ledger.entries.store', props.selected.id), options);
    }
}

async function deleteEntry(entry) {
    const confirmed = await confirmAction({
        title: 'Delete Entry?',
        text: `"${entry.type === 'gave' ? 'You gave' : 'You got'} ${formatCurrency(entry.amount)}" on ${formatShortDate(entry.entry_date)} will be removed.`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('ledger.entries.destroy', [props.selected.id, entry.id]), { preserveScroll: true });
    }
}

// Statement written to the other person, same text as the mobile app.
const statementLink = computed(() => {
    const person = props.selected;
    if (!person) return '#';
    const entries = (person.entries || []).slice(0, 30).reverse();
    const balance = person.balance > 0
        ? `You have to pay me ${formatCurrency(person.balance)}`
        : person.balance < 0 ? `I have to pay you ${formatCurrency(Math.abs(person.balance))}` : 'All settled';
    const lines = [
        `Hi ${person.name},`,
        'Here is our Give & Take account from JodTod:',
        '',
        ...(person.entries_count > entries.length ? [`(Last ${entries.length} of ${person.entries_count} entries)`] : []),
        ...entries.map((e) => `${formatShortDate(e.entry_date)} - ${e.type === 'gave' ? 'I gave you' : 'You paid me'} ${formatCurrency(e.amount)}${e.note ? ` (${e.note})` : ''}`),
        '',
        `Total I gave: ${formatCurrency(person.total_gave)}`,
        `Total you paid: ${formatCurrency(person.total_got)}`,
        `Balance: ${balance}`,
    ];
    return whatsAppLink(person.phone, lines.join('\n'));
});
</script>

<template>
    <Head :title="selected ? `${selected.name} - Give & Take` : 'Give & Take'" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <!-- Header (on phones, hidden while a person is open so the detail gets the space) -->
            <div class="flex items-start justify-between gap-4" :class="{ 'hidden lg:flex': selected }">
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Give &amp; Take</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Money you gave to or got from people, with a running balance</p>
                </div>
                <button
                    type="button"
                    class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700"
                    @click="openAddPerson"
                >
                    <Icon name="plus" class="h-5 w-5" />
                    <span class="hidden sm:inline">Add Person</span>
                </button>
            </div>

            <!-- Totals -->
            <div class="mt-6 grid grid-cols-2 gap-3" :class="{ 'hidden lg:grid': selected }">
                <div class="rounded-xl bg-green-50 p-4 dark:bg-green-900/20">
                    <p class="text-sm text-green-800 dark:text-green-300">You will get</p>
                    <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(totals.you_will_get) }}</p>
                </div>
                <div class="rounded-xl bg-red-50 p-4 dark:bg-red-900/20">
                    <p class="text-sm text-red-800 dark:text-red-300">You will give</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(totals.you_will_give) }}</p>
                </div>
            </div>

            <!-- First-time empty state -->
            <div v-if="people.length === 0" class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <EmptyState
                    icon="swap"
                    tone="success"
                    title="Keep track of money you give and get"
                    text="Gave ₹500 to a friend, got some back later? Add the person, then record each time you give or get money. JodTod keeps the running total, so you always know who owes whom."
                    action-label="Add First Person"
                    @action="openAddPerson"
                />
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-5" :class="selected ? 'lg:mt-6' : 'mt-6'">
                <!-- People -->
                <section class="lg:col-span-2" :class="{ 'hidden lg:block': selected }">
                    <div class="relative mb-3">
                        <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input v-model="search" type="search" placeholder="Search people" :class="inputClass" class="pl-9" />
                    </div>
                    <p v-if="visiblePeople.length === 0" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No people match.</p>
                    <div class="space-y-2">
                        <Link
                            v-for="person in visiblePeople"
                            :key="person.id"
                            :href="route('ledger.show', person.id)"
                            preserve-scroll
                            class="flex items-center gap-3 rounded-xl border bg-white p-4 transition-colors dark:bg-gray-800"
                            :class="selected?.id === person.id
                                ? 'border-primary-400 ring-1 ring-primary-400 dark:border-primary-500 dark:ring-primary-500'
                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50'"
                        >
                            <InitialsAvatar :name="person.name" class="h-10 w-10 text-sm" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ person.name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ person.last_entry_on ? `Last entry ${formatShortDate(person.last_entry_on)}` : 'No entries yet' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold" :class="balanceClass(person.balance)">{{ person.balance === 0 ? '' : formatCurrency(Math.abs(person.balance)) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ balanceLabel(person.balance) }}</p>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- Detail -->
                <section class="lg:col-span-3">
                    <div v-if="selected" class="space-y-4 lg:sticky lg:top-24">
                        <Link
                            :href="route('ledger.index')"
                            class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden"
                        >
                            <Icon name="chevron-left" class="h-4 w-4" />
                            All people
                        </Link>

                        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start gap-3">
                                <InitialsAvatar :name="selected.name" class="h-12 w-12 text-base" />
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words text-xl font-bold text-gray-900 dark:text-gray-100">{{ selected.name }}</h3>
                                    <a v-if="selected.phone" :href="telLink(selected.phone)" class="text-sm text-primary-600 hover:underline">{{ selected.phone }}</a>
                                    <p v-if="selected.note" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ selected.note }}</p>
                                </div>
                                <a
                                    :href="statementLink"
                                    target="_blank"
                                    rel="noopener"
                                    class="rounded-lg p-2 text-green-600 transition-colors hover:bg-green-50 dark:hover:bg-green-900/20"
                                    title="Share statement on WhatsApp"
                                >
                                    <Icon name="share" class="h-5 w-5" />
                                </a>
                                <button type="button" class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200" title="Edit person" @click="openEditPerson">
                                    <Icon name="pencil" class="h-5 w-5" />
                                </button>
                                <button type="button" class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Delete person" @click="deletePerson">
                                    <Icon name="trash" class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="mt-4 border-t border-gray-100 pt-4 text-center dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ selected.balance === 0 ? 'All settled' : selected.balance > 0 ? `${selected.name} owes you` : `You owe ${selected.name}` }}
                                </p>
                                <p class="mt-1 text-3xl font-extrabold" :class="balanceClass(selected.balance)">{{ formatCurrency(Math.abs(selected.balance)) }}</p>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Total gave: <span class="font-semibold text-red-600">{{ formatCurrency(selected.total_gave) }}</span>
                                    &middot; Total got: <span class="font-semibold text-green-600">{{ formatCurrency(selected.total_got) }}</span>
                                </p>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-red-600 py-2.5 text-sm font-bold text-white hover:bg-red-700" @click="openAddEntry('gave')">
                                    <Icon name="arrow-up" class="h-4 w-4" />
                                    You Gave
                                </button>
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-green-600 py-2.5 text-sm font-bold text-white hover:bg-green-700" @click="openAddEntry('got')">
                                    <Icon name="arrow-down" class="h-4 w-4" />
                                    You Got
                                </button>
                            </div>
                        </div>

                        <!-- History -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            <h4 class="border-b border-gray-100 px-5 py-3 text-sm font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">History</h4>
                            <p v-if="!selected.entries?.length" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No entries yet. Use "You Gave" or "You Got" to add the first one.
                            </p>
                            <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                                <li v-for="entry in selected.entries" :key="entry.id" class="flex items-center gap-3 px-5 py-3">
                                    <Icon
                                        :name="entry.type === 'gave' ? 'arrow-up' : 'arrow-down'"
                                        class="h-5 w-5 flex-shrink-0"
                                        :class="entry.type === 'gave' ? 'text-red-500' : 'text-green-500'"
                                    />
                                    <button type="button" class="min-w-0 flex-1 text-left" title="Edit entry" @click="openEditEntry(entry)">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ entry.type === 'gave' ? 'You gave' : 'You got' }}</p>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatShortDate(entry.entry_date) }}<template v-if="entry.note"> &middot; {{ entry.note }}</template>
                                        </p>
                                    </button>
                                    <div class="text-right">
                                        <p class="text-sm font-bold" :class="entry.type === 'gave' ? 'text-red-600' : 'text-green-600'">{{ formatCurrency(entry.amount) }}</p>
                                        <p class="text-xs text-gray-400">
                                            Bal. {{ entry.balance_after === 0 ? formatCurrency(0) : `${formatCurrency(Math.abs(entry.balance_after))} ${entry.balance_after > 0 ? 'to get' : 'to give'}` }}
                                        </p>
                                    </div>
                                    <button type="button" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Delete entry" @click="deleteEntry(entry)">
                                        <Icon name="trash" class="h-4 w-4" />
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div
                        v-else
                        class="hidden h-full min-h-[16rem] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-600 lg:flex"
                    >
                        <Icon name="swap" class="h-10 w-10 text-gray-300 dark:text-gray-600" stroke-width="1.5" />
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Select a person to see the history and add what you gave or got.</p>
                    </div>
                </section>
            </div>
        </div>

        <!-- Add / edit person -->
        <Modal :show="showPersonForm" max-width="md" @close="showPersonForm = false">
            <form class="p-6" @submit.prevent="submitPerson">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ editingPerson ? 'Edit Person' : 'Add Person' }}</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="person-name" :class="labelClass">Name *</label>
                        <input id="person-name" v-model="personForm.name" type="text" maxlength="100" required placeholder="e.g. Rahul" :class="inputClass" />
                        <p v-if="personForm.errors.name" class="mt-1 text-xs text-red-600">{{ personForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="person-phone" :class="labelClass">Phone</label>
                        <input id="person-phone" v-model="personForm.phone" type="tel" maxlength="20" placeholder="For WhatsApp statement" :class="inputClass" />
                        <p v-if="personForm.errors.phone" class="mt-1 text-xs text-red-600">{{ personForm.errors.phone }}</p>
                    </div>
                    <div>
                        <label for="person-note" :class="labelClass">Note</label>
                        <input id="person-note" v-model="personForm.note" type="text" maxlength="255" placeholder="Optional" :class="inputClass" />
                        <p v-if="personForm.errors.note" class="mt-1 text-xs text-red-600">{{ personForm.errors.note }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showPersonForm = false">Cancel</button>
                    <button type="submit" :disabled="personForm.processing" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60">{{ editingPerson ? 'Save' : 'Add' }}</button>
                </div>
            </form>
        </Modal>

        <!-- Add / edit entry -->
        <Modal :show="showEntryForm" max-width="md" @close="showEntryForm = false">
            <form class="p-6" @submit.prevent="submitEntry">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ editingEntryId ? 'Edit Entry' : entryForm.type === 'gave' ? `You gave ${selected?.name}` : `You got from ${selected?.name}` }}
                </h2>
                <div class="mt-4 space-y-4">
                    <div v-if="editingEntryId" class="grid grid-cols-2 gap-2">
                        <button type="button" class="rounded-lg border py-2 text-sm font-semibold" :class="entryForm.type === 'gave' ? 'border-red-600 bg-red-600 text-white' : 'border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200'" @click="entryForm.type = 'gave'">You Gave</button>
                        <button type="button" class="rounded-lg border py-2 text-sm font-semibold" :class="entryForm.type === 'got' ? 'border-green-600 bg-green-600 text-white' : 'border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200'" @click="entryForm.type = 'got'">You Got</button>
                    </div>
                    <div>
                        <label for="entry-amount" :class="labelClass">Amount (₹) *</label>
                        <input id="entry-amount" v-model="entryForm.amount" type="number" min="0.01" step="0.01" required :class="inputClass" />
                        <p v-if="entryForm.errors.amount" class="mt-1 text-xs text-red-600">{{ entryForm.errors.amount }}</p>
                    </div>
                    <div>
                        <label for="entry-date" :class="labelClass">Date *</label>
                        <input id="entry-date" v-model="entryForm.entry_date" type="date" :max="todayISO()" required :class="inputClass" />
                        <p v-if="entryForm.errors.entry_date" class="mt-1 text-xs text-red-600">{{ entryForm.errors.entry_date }}</p>
                    </div>
                    <div>
                        <label for="entry-note" :class="labelClass">Note</label>
                        <input id="entry-note" v-model="entryForm.note" type="text" maxlength="255" placeholder="Optional, e.g. for rent" :class="inputClass" />
                        <p v-if="entryForm.errors.note" class="mt-1 text-xs text-red-600">{{ entryForm.errors.note }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showEntryForm = false">Cancel</button>
                    <button
                        type="submit"
                        :disabled="entryForm.processing"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                        :class="entryForm.type === 'gave' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                    >
                        Save
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
