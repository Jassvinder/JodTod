<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ImageUpload from '@/Components/Expenses/ImageUpload.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    group: Object,
    categories: Array,
});

const authUser = computed(() => usePage().props.auth.user);

const form = useForm({
    description: '',
    amount: '',
    category_id: '',
    expense_date: new Date().toISOString().slice(0, 16),
    paid_by: authUser.value.id,
    split_type: 'equal',
    splits: [],
    image_1: null,
    image_2: null,
});

const splitType = ref('equal');
const splitOpen = ref(false);

// Equal split: track which members are selected
const selectedMembers = ref(props.group.members.map(m => m.id));

// Custom split: track amounts per member
const customAmounts = ref({});
props.group.members.forEach(m => {
    customAmounts.value[m.id] = '';
});

// Percentage split: track percentages per member
const percentages = ref({});
props.group.members.forEach(m => {
    percentages.value[m.id] = '';
});

// Computed values for equal split
const equalPerPerson = computed(() => {
    const amount = parseFloat(form.amount) || 0;
    const count = selectedMembers.value.length;
    if (count === 0 || amount <= 0) return 0;
    return Math.floor((amount / count) * 100) / 100;
});

const equalRemainder = computed(() => {
    const amount = parseFloat(form.amount) || 0;
    const count = selectedMembers.value.length;
    if (count === 0 || amount <= 0) return 0;
    return Math.round((amount - (equalPerPerson.value * count)) * 100) / 100;
});

// Computed values for custom split
const customTotal = computed(() => {
    return Object.values(customAmounts.value).reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
});

const customRemaining = computed(() => {
    const amount = parseFloat(form.amount) || 0;
    return Math.round((amount - customTotal.value) * 100) / 100;
});

// Computed values for percentage split
const percentageTotal = computed(() => {
    return Object.values(percentages.value).reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
});

function toggleMember(memberId) {
    const index = selectedMembers.value.indexOf(memberId);
    if (index > -1) {
        selectedMembers.value.splice(index, 1);
    } else {
        selectedMembers.value.push(memberId);
    }
}

function buildSplits() {
    const splits = [];
    const amount = parseFloat(form.amount) || 0;

    if (splitType.value === 'equal') {
        const count = selectedMembers.value.length;
        if (count === 0) return splits;
        const perPerson = Math.floor((amount / count) * 100) / 100;
        const remainder = Math.round((amount - (perPerson * count)) * 100) / 100;

        selectedMembers.value.forEach((userId, index) => {
            splits.push({
                user_id: userId,
                share_amount: index === 0 ? Math.round((perPerson + remainder) * 100) / 100 : perPerson,
                percentage: null,
            });
        });
    } else if (splitType.value === 'custom') {
        props.group.members.forEach(m => {
            const amt = parseFloat(customAmounts.value[m.id]) || 0;
            if (amt > 0) {
                splits.push({
                    user_id: m.id,
                    share_amount: Math.round(amt * 100) / 100,
                    percentage: null,
                });
            }
        });
    } else if (splitType.value === 'percentage') {
        props.group.members.forEach(m => {
            const pct = parseFloat(percentages.value[m.id]) || 0;
            if (pct > 0) {
                splits.push({
                    user_id: m.id,
                    share_amount: Math.round((pct / 100) * amount * 100) / 100,
                    percentage: pct,
                });
            }
        });
    }

    return splits;
}

function submit() {
    form.split_type = splitType.value;
    form.splits = buildSplits();
    form.post(route('groups.expenses.store', props.group.id), {
        forceFormData: true,
    });
}

function getMemberInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}

// Validation helpers
const isCustomValid = computed(() => {
    const amount = parseFloat(form.amount) || 0;
    if (amount <= 0) return true;
    return Math.abs(customRemaining.value) < 0.01;
});

const isPercentageValid = computed(() => {
    if (percentageTotal.value === 0) return true;
    return Math.abs(percentageTotal.value - 100) < 0.01;
});

const canSubmit = computed(() => {
    if (!form.amount || !form.category_id || !form.description) return false;
    if (splitType.value === 'equal' && selectedMembers.value.length === 0) return false;
    if (splitType.value === 'custom' && !isCustomValid.value) return false;
    if (splitType.value === 'percentage' && !isPercentageValid.value) return false;
    return true;
});
</script>

<template>
    <Head title="Add Group Expense" />

    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('groups.expenses.index', group.id)" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Expense</h1>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ group.name }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-5">
                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                        <div class="mt-1 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm">&#8377;</span>
                            <input
                                id="amount"
                                v-model="form.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                max="99999999.99"
                                placeholder="0.00"
                                class="block w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                required
                            />
                        </div>
                        <p v-if="form.errors.amount" class="mt-1 text-sm text-accent-600">{{ form.errors.amount }}</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            required
                        >
                            <option value="" disabled>Select category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1 text-sm text-accent-600">{{ form.errors.category_id }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <input
                            id="description"
                            v-model="form.description"
                            type="text"
                            maxlength="255"
                            placeholder="e.g. Dinner at restaurant"
                            class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            required
                        />
                        <p v-if="form.errors.description" class="mt-1 text-sm text-accent-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input
                            id="expense_date"
                            v-model="form.expense_date"
                            type="date"
                            :max="new Date().toISOString().split('T')[0]"
                            class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            required
                        />
                        <p v-if="form.errors.expense_date" class="mt-1 text-sm text-accent-600">{{ form.errors.expense_date }}</p>
                    </div>

                    <!-- Paid By -->
                    <div>
                        <label for="paid_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paid By</label>
                        <select
                            id="paid_by"
                            v-model="form.paid_by"
                            class="mt-1 block w-full py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            required
                        >
                            <option v-for="member in group.members" :key="member.id" :value="member.id">
                                {{ member.name }}{{ member.id === authUser.id ? ' (You)' : '' }}
                            </option>
                        </select>
                        <p v-if="form.errors.paid_by" class="mt-1 text-sm text-accent-600">{{ form.errors.paid_by }}</p>
                    </div>
                </div>

                <!-- Split Section (Collapsible) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button
                        type="button"
                        @click="splitOpen = !splitOpen"
                        class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Split Between</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">({{ splitType === 'equal' ? 'Equal' : splitType === 'custom' ? 'Custom' : 'Percentage' }})</span>
                        </div>
                        <svg
                            class="w-5 h-5 text-gray-400 transition-transform duration-200"
                            :class="splitOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div v-show="splitOpen" class="px-6 pb-6">

                    <!-- Split Type Tabs -->
                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden mb-5">
                        <button
                            type="button"
                            @click="splitType = 'equal'"
                            :class="[
                                'flex-1 py-2.5 text-sm font-medium transition-colors',
                                splitType === 'equal' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                            ]"
                        >
                            Equal
                        </button>
                        <button
                            type="button"
                            @click="splitType = 'custom'"
                            :class="[
                                'flex-1 py-2.5 text-sm font-medium transition-colors border-l border-r border-gray-300 dark:border-gray-600',
                                splitType === 'custom' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                            ]"
                        >
                            Custom
                        </button>
                        <button
                            type="button"
                            @click="splitType = 'percentage'"
                            :class="[
                                'flex-1 py-2.5 text-sm font-medium transition-colors',
                                splitType === 'percentage' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                            ]"
                        >
                            Percentage
                        </button>
                    </div>

                    <!-- Equal Split -->
                    <div v-if="splitType === 'equal'" class="space-y-3">
                        <div
                            v-for="member in group.members"
                            :key="member.id"
                            class="flex items-center gap-3 py-2"
                        >
                            <input
                                type="checkbox"
                                :checked="selectedMembers.includes(member.id)"
                                @change="toggleMember(member.id)"
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            />
                            <div class="flex items-center gap-2 flex-1">
                                <img
                                    v-if="member.avatar"
                                    :src="`/storage/${member.avatar}`"
                                    :alt="member.name"
                                    class="w-8 h-8 rounded-full object-cover"
                                />
                                <div v-else class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-xs">
                                    {{ getMemberInitial(member.name) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ member.name }}
                                    <span v-if="member.id === authUser.id" class="text-gray-400 dark:text-gray-500">(You)</span>
                                </span>
                            </div>
                            <span v-if="selectedMembers.includes(member.id)" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                &#8377;{{ equalPerPerson.toFixed(2) }}
                            </span>
                        </div>

                        <div v-if="selectedMembers.length > 0 && form.amount" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                &#8377;{{ equalPerPerson.toFixed(2) }} per person
                                <span v-if="equalRemainder > 0" class="text-gray-400">
                                    (&#8377;{{ equalRemainder.toFixed(2) }} extra to first person)
                                </span>
                            </p>
                        </div>
                        <p v-if="selectedMembers.length === 0" class="text-sm text-red-600">Select at least one member</p>
                    </div>

                    <!-- Custom Split -->
                    <div v-if="splitType === 'custom'" class="space-y-3">
                        <div
                            v-for="member in group.members"
                            :key="member.id"
                            class="flex items-center gap-3 py-2"
                        >
                            <div class="flex items-center gap-2 flex-1">
                                <img
                                    v-if="member.avatar"
                                    :src="`/storage/${member.avatar}`"
                                    :alt="member.name"
                                    class="w-8 h-8 rounded-full object-cover"
                                />
                                <div v-else class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-xs">
                                    {{ getMemberInitial(member.name) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ member.name }}
                                    <span v-if="member.id === authUser.id" class="text-gray-400 dark:text-gray-500">(You)</span>
                                </span>
                            </div>
                            <div class="relative w-32">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-500 dark:text-gray-400 text-xs">&#8377;</span>
                                <input
                                    v-model="customAmounts[member.id]"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="w-full pl-6 pr-2 py-2 rounded-lg border border-gray-300 text-sm text-right focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                />
                            </div>
                        </div>

                        <div v-if="form.amount" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total</span>
                                <span :class="isCustomValid ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-900 dark:text-gray-100 font-medium'">
                                    &#8377;{{ customTotal.toFixed(2) }} / &#8377;{{ parseFloat(form.amount || 0).toFixed(2) }}
                                </span>
                            </div>
                            <div v-if="!isCustomValid && customTotal > 0" class="mt-1 flex items-center justify-between text-sm">
                                <span class="text-red-600">Remaining</span>
                                <span class="text-red-600 font-medium">&#8377;{{ customRemaining.toFixed(2) }}</span>
                            </div>
                            <p v-if="!isCustomValid && customTotal > 0" class="mt-2 text-xs text-red-600">
                                Split amounts must equal the total expense amount
                            </p>
                        </div>
                    </div>

                    <!-- Percentage Split -->
                    <div v-if="splitType === 'percentage'" class="space-y-3">
                        <div
                            v-for="member in group.members"
                            :key="member.id"
                            class="flex items-center gap-3 py-2"
                        >
                            <div class="flex items-center gap-2 flex-1">
                                <img
                                    v-if="member.avatar"
                                    :src="`/storage/${member.avatar}`"
                                    :alt="member.name"
                                    class="w-8 h-8 rounded-full object-cover"
                                />
                                <div v-else class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-xs">
                                    {{ getMemberInitial(member.name) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ member.name }}
                                    <span v-if="member.id === authUser.id" class="text-gray-400 dark:text-gray-500">(You)</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="relative w-20">
                                    <input
                                        v-model="percentages[member.id]"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        placeholder="0"
                                        class="w-full pr-6 pl-2 py-2 rounded-lg border border-gray-300 text-sm text-right focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    />
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-500 dark:text-gray-400 text-xs">%</span>
                                </div>
                                <span v-if="form.amount && percentages[member.id]" class="text-xs text-gray-500 dark:text-gray-400 w-20 text-right">
                                    &#8377;{{ ((parseFloat(percentages[member.id]) || 0) / 100 * (parseFloat(form.amount) || 0)).toFixed(2) }}
                                </span>
                            </div>
                        </div>

                        <div v-if="percentageTotal > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total</span>
                                <span :class="isPercentageValid ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-900 dark:text-gray-100 font-medium'">
                                    {{ percentageTotal.toFixed(2) }}% / 100%
                                </span>
                            </div>
                            <p v-if="!isPercentageValid" class="mt-2 text-xs text-red-600">
                                Percentages must add up to 100%
                            </p>
                        </div>
                    </div>

                    <p v-if="form.errors.splits" class="mt-3 text-sm text-accent-600">{{ form.errors.splits }}</p>
                    </div>
                </div>

                <!-- Images -->
                <ImageUpload :form="form" />

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing || !canSubmit"
                        class="flex-1 py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Adding...' : 'Add Expense' }}
                    </button>
                    <Link
                        :href="route('groups.expenses.index', group.id)"
                        class="py-3 px-6 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
