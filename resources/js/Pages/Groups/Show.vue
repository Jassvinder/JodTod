<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    group: Object,
    isAdmin: Boolean,
    recentExpenses: { type: Array, default: () => [] },
    totalExpensesCount: { type: Number, default: 0 },
    totalExpensesAmount: { type: Number, default: 0 },
    contacts: { type: Array, default: () => [] },
    membersWithUnsettled: { type: Array, default: () => [] },
});

const hasUnsettledExpenses = (memberId) => props.membersWithUnsettled.includes(memberId);

const authUser = computed(() => usePage().props.auth.user);
const showAddMemberModal = ref(false);

const hasUnsettledGroupExpenses = computed(() => {
    return props.membersWithUnsettled.length > 0;
});

const deleteGroup = async () => {
    if (hasUnsettledGroupExpenses.value) {
        await confirmAction({
            title: 'Cannot Delete Group',
            text: `"${props.group.name}" has unsettled expenses. Settle all expenses first before deleting the group.`,
            confirmText: 'OK',
            danger: false,
        });
        return;
    }
    const confirmed = await confirmAction({
        title: 'Delete Group',
        text: `Are you sure you want to delete "${props.group.name}"? This will remove all members and group data. This action cannot be undone.`,
        confirmText: 'Delete Group',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('groups.destroy', props.group.id));
    }
};

const confirmRemoveMember = async (member) => {
    const willDeactivate = hasUnsettledExpenses(member.id);
    const confirmed = await confirmAction({
        title: willDeactivate ? 'Deactivate Member' : 'Remove Member',
        text: willDeactivate
            ? `${member.name} has unsettled expenses. They will be deactivated — excluded from new expenses but their pending balances remain for settlement.`
            : `Are you sure you want to remove ${member.name} from this group?`,
        confirmText: willDeactivate ? 'Deactivate' : 'Remove',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('groups.members.remove', [props.group.id, member.id]), {
            preserveScroll: true,
        });
    }
};

const reactivateMember = async (member) => {
    const confirmed = await confirmAction({
        title: 'Reactivate Member',
        text: `Are you sure you want to reactivate ${member.name}? They will be included in new expenses again.`,
        confirmText: 'Reactivate',
        danger: false,
    });
    if (confirmed) {
        router.post(route('groups.members.reactivate', [props.group.id, member.id]), {}, {
            preserveScroll: true,
        });
    }
};

const addMember = (user) => {
    router.post(route('groups.add-member', props.group.id), {
        user_id: user.id,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showAddMemberModal.value = false;
        },
    });
};

const getUserInitials = (name) => {
    return name ? name.charAt(0).toUpperCase() : '?';
};
</script>

<template>
    <Head :title="group.name" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <Link :href="route('groups.index')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ group.name }}</h1>
                        <p v-if="group.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ group.description }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="isAdmin"
                        @click="showAddMemberModal = true"
                        class="px-3 py-2 text-sm font-medium text-green-600 dark:text-green-400 border border-green-300 dark:border-green-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors"
                    >
                        Add Member
                    </button>
                    <Link
                        :href="route('groups.settlements.index', group.id)"
                        class="px-3 py-2 text-sm font-medium text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                    >
                        Settle Up
                    </Link>
                    <Link
                        v-if="isAdmin"
                        :href="route('groups.edit', group.id)"
                        class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Edit
                    </Link>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Members ({{ group.members.length }})
                    </h2>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    <li
                        v-for="member in group.members"
                        :key="member.id"
                        class="flex items-center justify-between px-5 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                v-if="member.avatar"
                                :src="`/storage/${member.avatar}`"
                                :alt="member.name"
                                class="w-9 h-9 rounded-full object-cover"
                            />
                            <div v-else class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm">
                                {{ member.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ member.name }}
                                    <span v-if="member.id === authUser.id" class="text-gray-400 dark:text-gray-500">(You)</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ member.email }}
                                    <span v-if="member.phone">&middot; +91 {{ member.phone }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                v-if="member.pivot?.role === 'admin'"
                                class="px-2 py-0.5 text-xs font-medium bg-primary-100 text-primary-700 rounded-full"
                            >
                                Admin
                            </span>
                            <span
                                v-if="member.pivot?.is_active === false || member.pivot?.is_active === 0"
                                class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-full"
                            >
                                Inactive
                            </span>
                            <button
                                v-if="isAdmin && member.id !== authUser.id && member.pivot?.role !== 'admin' && (member.pivot?.is_active === false || member.pivot?.is_active === 0)"
                                @click="reactivateMember(member)"
                                class="text-xs text-green-500 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium cursor-pointer"
                            >
                                Reactivate
                            </button>
                            <button
                                v-if="isAdmin && member.id !== authUser.id && member.pivot?.role !== 'admin' && member.pivot?.is_active !== false && member.pivot?.is_active !== 0"
                                @click="confirmRemoveMember(member)"
                                class="text-xs font-medium cursor-pointer"
                                :class="hasUnsettledExpenses(member.id) ? 'text-amber-500 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300' : 'text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300'"
                            >
                                {{ hasUnsettledExpenses(member.id) ? 'Deactivate' : 'Remove' }}
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Expenses Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Expenses</h2>
                        <p v-if="totalExpensesCount > 0" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ totalExpensesCount }} {{ totalExpensesCount === 1 ? 'expense' : 'expenses' }} &middot;
                            Total: &#8377;{{ totalExpensesAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link
                            :href="route('groups.expenses.create', group.id)"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Expense
                        </Link>
                        <Link
                            :href="route('groups.expenses.index', group.id)"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                        >
                            View All
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Recent Expenses List -->
                <div v-if="recentExpenses.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div
                        v-for="expense in recentExpenses"
                        :key="expense.id"
                        class="flex items-center gap-3 px-5 py-3"
                    >
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-semibold text-xs shrink-0">
                            {{ expense.category?.name?.charAt(0) || '?' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ expense.description || 'No description' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ expense.category?.name }} &middot; Paid by {{ expense.payer?.name || 'Unknown' }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 shrink-0">
                            &#8377;{{ Number(expense.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}
                        </p>
                    </div>
                </div>

                <div v-else class="px-5 pb-4 text-sm text-gray-400 dark:text-gray-500">
                    No expenses added yet.
                    <Link :href="route('groups.expenses.create', group.id)" class="text-primary-600 hover:text-primary-700 font-medium">
                        Add the first expense
                    </Link>
                </div>
            </div>

            <!-- Actions -->
            <div v-if="isAdmin" class="mt-6 flex items-center gap-3">
                <button
                    @click="deleteGroup"
                    class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                >
                    Delete Group
                </button>
            </div>
        </div>

        <!-- Add Member Modal (from contacts) -->
        <Modal :show="showAddMemberModal" @close="showAddMemberModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Member from Contacts</h2>

                <div v-if="contacts.length === 0" class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No contacts available to add</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add users to your contacts first, then add them to groups.</p>
                    <Link :href="route('contacts.index')" class="mt-3 inline-block px-4 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                        Go to Contacts
                    </Link>
                </div>

                <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg max-h-80 overflow-y-auto">
                    <li
                        v-for="user in contacts"
                        :key="user.id"
                        class="flex items-center justify-between px-4 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                v-if="user.avatar"
                                :src="`/storage/${user.avatar}`"
                                :alt="user.name"
                                class="w-9 h-9 rounded-full object-cover"
                            />
                            <div v-else class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-semibold text-sm">
                                {{ getUserInitials(user.name) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email || user.phone }}</p>
                            </div>
                        </div>
                        <button
                            @click="addMember(user)"
                            class="px-3 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
                        >
                            Add
                        </button>
                    </li>
                </ul>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showAddMemberModal = false">Close</SecondaryButton>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>
