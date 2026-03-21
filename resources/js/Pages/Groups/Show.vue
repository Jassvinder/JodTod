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
    pendingMembers: { type: Array, default: () => [] },
    memberShares: { type: Array, default: () => [] },
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

const approveMember = (member) => {
    router.post(route('groups.members.approve', [props.group.id, member.id]), {}, {
        preserveScroll: true,
    });
};

const rejectMember = async (member) => {
    const confirmed = await confirmAction({
        title: 'Reject Join Request',
        text: `Are you sure you want to reject ${member.name}'s request to join this group?`,
        confirmText: 'Reject',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('groups.members.reject', [props.group.id, member.id]), {
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

const pendingOpen = ref(true);
const membersOpen = ref(true);
const memberSharesOpen = ref(true);
</script>

<style scoped>
.collapse-content {
    display: grid;
    grid-template-rows: 1fr;
    transition: grid-template-rows 0.3s ease;
}
.collapse-content.collapsed {
    grid-template-rows: 0fr;
}
.collapse-inner {
    overflow: hidden;
}
.chevron-icon {
    transition: transform 0.3s ease;
}
.chevron-icon.rotated {
    transform: rotate(180deg);
}
</style>

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
                    <img
                        v-if="group.photo_url"
                        :src="group.photo_url"
                        :alt="group.name"
                        class="w-12 h-12 rounded-full object-cover"
                    />
                    <div v-else class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-lg">
                        {{ group.name.charAt(0).toUpperCase() }}
                    </div>
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
                        Settlement
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

            <!-- Stats Summary -->
            <div v-if="totalExpensesCount > 0" class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalExpensesCount }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expenses</p>
                </div>
                <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800 p-4 text-center">
                    <p class="text-2xl font-bold text-primary-700 dark:text-primary-300">&#8377;{{ totalExpensesAmount.toLocaleString('en-IN', { minimumFractionDigits: 0 }) }}</p>
                    <p class="text-xs text-primary-600 dark:text-primary-400 mt-1">Total Spent</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ group.members.length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Members</p>
                </div>
            </div>

            <!-- Pending Join Requests -->
            <div v-if="isAdmin && pendingMembers.length > 0" class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 mb-6 overflow-hidden">
                <button @click="pendingOpen = !pendingOpen" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-amber-100/50 dark:hover:bg-amber-900/30 transition-colors">
                    <h2 class="text-base font-semibold text-amber-800 dark:text-amber-200">
                        Pending Requests ({{ pendingMembers.length }})
                    </h2>
                    <svg class="w-5 h-5 text-amber-600 chevron-icon" :class="{ rotated: !pendingOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <div class="collapse-content" :class="{ collapsed: !pendingOpen }">
                <div class="collapse-inner">
                <ul class="divide-y divide-amber-100 dark:divide-amber-800">
                    <li
                        v-for="member in pendingMembers"
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
                            <div v-else class="w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-300 font-semibold text-sm">
                                {{ getUserInitials(member.name) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ member.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ member.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="approveMember(member)"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"
                            >
                                Approve
                            </button>
                            <button
                                @click="rejectMember(member)"
                                class="px-3 py-1.5 text-xs font-medium text-red-600 border border-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                            >
                                Reject
                            </button>
                        </div>
                    </li>
                </ul>
                </div>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <button @click="membersOpen = !membersOpen" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Members ({{ group.members.length }})
                    </h2>
                    <svg class="w-5 h-5 text-gray-400 chevron-icon" :class="{ rotated: !membersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                <div class="collapse-content" :class="{ collapsed: !membersOpen }">
                <div class="collapse-inner">
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
                </div>
            </div>

            <!-- Member Shares Section -->
            <div v-if="memberShares.length > 0 && totalExpensesCount > 0" class="mt-6 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-200 dark:border-purple-800 overflow-hidden">
                <button @click="memberSharesOpen = !memberSharesOpen" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-purple-100/50 dark:hover:bg-purple-900/20 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        <h2 class="text-base font-semibold text-purple-800 dark:text-purple-200">Member Shares</h2>
                        <span class="text-xs text-purple-500 dark:text-purple-400">(All time)</span>
                    </div>
                    <svg class="w-5 h-5 text-purple-400 chevron-icon" :class="{ rotated: !memberSharesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <div class="collapse-content" :class="{ collapsed: !memberSharesOpen }">
                <div class="collapse-inner">
                    <div class="flex items-center justify-between px-5 py-2.5 border-t border-purple-200 dark:border-purple-800 bg-purple-100/50 dark:bg-purple-900/20">
                        <span class="text-xs text-purple-600 dark:text-purple-400">Total Expenses</span>
                        <span class="text-sm font-bold text-purple-800 dark:text-purple-200">&#8377;{{ totalExpensesAmount.toLocaleString('en-IN', { minimumFractionDigits: 0 }) }}</span>
                    </div>
                    <div
                        v-for="member in memberShares"
                        :key="member.user_id"
                        class="flex items-center gap-3 px-5 py-2.5 border-t border-purple-100 dark:border-purple-800/50"
                    >
                        <img
                            v-if="member.avatar"
                            :src="`/storage/${member.avatar}`"
                            :alt="member.name"
                            class="w-6 h-6 rounded-full object-cover shrink-0"
                        />
                        <div v-else class="w-6 h-6 rounded-full bg-purple-200 dark:bg-purple-800 flex items-center justify-center text-purple-700 dark:text-purple-300 font-semibold text-[10px] shrink-0">
                            {{ getUserInitials(member.name) }}
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate">{{ member.name }}</span>
                        <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">&#8377;{{ member.total_share.toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}</span>
                    </div>
                </div>
                </div>
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
