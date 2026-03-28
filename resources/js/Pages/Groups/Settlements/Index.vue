<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    group: Object,
    balances: Array,
    suggestedTransactions: Array,
    settlements: Object,
    memberShares: Object,
});

const authUser = computed(() => usePage().props.auth.user);

const isAdmin = computed(() => {
    return props.group.members?.find(
        m => m.id === authUser.value.id && m.pivot?.role === 'admin'
    );
});

const settlingUp = ref(false);
const completingId = ref(null);
const settlingAll = ref(false);

function formatCurrency(amount) {
    return '₹' + Math.abs(parseFloat(amount)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-IN', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function getUserInitials(name) {
    if (!name) return '?';
    return name.charAt(0).toUpperCase();
}

function getMember(userId) {
    return props.group.members?.find(m => m.id === userId) || { name: 'Unknown', avatar: null };
}

function balanceCardClass(amount) {
    if (parseFloat(amount) > 0) return 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/30';
    if (parseFloat(amount) < 0) return 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/30';
    return 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800';
}

function balanceTextClass(amount) {
    if (parseFloat(amount) > 0) return 'text-green-700 dark:text-green-300';
    if (parseFloat(amount) < 0) return 'text-red-700 dark:text-red-300';
    return 'text-gray-500 dark:text-gray-400';
}

function balanceLabel(amount) {
    const val = parseFloat(amount);
    if (val > 0) return 'to receive';
    if (val < 0) return 'to pay';
    return 'settled';
}

async function settleUp() {
    const confirmed = await confirmAction({
        title: 'Settle Up',
        text: 'This will create settlement requests for all pending balances. Continue?',
        confirmText: 'Settle Up',
        danger: false,
    });
    if (confirmed) {
        settlingUp.value = true;
        router.post(route('groups.settle', props.group.id), {}, {
            preserveScroll: true,
            onFinish: () => {
                settlingUp.value = false;
            },
        });
    }
}

function completeSettlement(settlementId) {
    completingId.value = settlementId;
    router.put(route('groups.settlements.complete', [props.group.id, settlementId]), {}, {
        preserveScroll: true,
        onFinish: () => {
            completingId.value = null;
        },
    });
}

async function markAllPaid() {
    const confirmed = await confirmAction({
        title: 'Mark All as Paid',
        text: 'This will mark ALL pending settlements as completed. Are you sure?',
        confirmText: 'Mark All as Paid',
        danger: false,
    });
    if (confirmed) {
        settlingAll.value = true;
        router.post(route('groups.settle-all', props.group.id), {}, {
            preserveScroll: true,
            onFinish: () => {
                settlingAll.value = false;
            },
        });
    }
}

function canMarkAsPaid(settlement) {
    if (isAdmin.value) return true;
    return settlement.from_user?.id === authUser.value.id || settlement.to_user?.id === authUser.value.id;
}

const hasUnsettledBalances = computed(() => {
    return props.suggestedTransactions && props.suggestedTransactions.length > 0;
});

const hasPendingSettlements = computed(() => {
    return props.settlements?.data?.some(s => s.status === 'pending');
});

const isAllSettled = computed(() => {
    return !hasUnsettledBalances.value && !hasPendingSettlements.value;
});

const balancesOpen = ref(true);
const sharesOpen = ref(true);
const suggestedOpen = ref(true);
const historyOpen = ref(true);
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
    <Head :title="`${group.name} - Settlements`" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <Link :href="route('groups.show', group.id)" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Settlements</h1>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ group.name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('groups.expenses.index', group.id)"
                        class="px-3 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 transition-colors"
                    >
                        View Expenses
                    </Link>
                    <button
                        v-if="isAdmin && hasPendingSettlements"
                        @click="markAllPaid"
                        :disabled="settlingAll"
                        class="px-3 py-2 text-sm font-medium text-green-600 dark:text-green-400 border border-green-300 dark:border-green-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors disabled:opacity-50 cursor-pointer"
                    >
                        {{ settlingAll ? 'Marking...' : 'Mark All as Paid' }}
                    </button>
                </div>
            </div>

            <!-- Section 1: Balance Overview -->
            <div class="mt-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="balancesOpen = !balancesOpen" class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance Overview</h2>
                        <svg class="w-4 h-4 text-gray-400 chevron-icon" :class="{ rotated: !balancesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div class="collapse-content" :class="{ collapsed: !balancesOpen }">
                    <div class="collapse-inner">
                        <div v-if="balances && balances.length">
                            <div
                                v-for="balance in balances"
                                :key="balance.user_id"
                                class="flex items-center gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-700"
                            >
                                <img
                                    v-if="getMember(balance.user_id)?.avatar"
                                    :src="`/storage/${getMember(balance.user_id).avatar}`"
                                    :alt="balance.name"
                                    class="w-7 h-7 rounded-full object-cover shrink-0"
                                />
                                <div v-else class="w-7 h-7 rounded-full flex items-center justify-center font-semibold text-[10px] shrink-0"
                                    :class="parseFloat(balance.balance) > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : parseFloat(balance.balance) < 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'"
                                >
                                    {{ getUserInitials(balance.name) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate">{{ balance.name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                    :class="parseFloat(balance.balance) > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : parseFloat(balance.balance) < 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'"
                                >
                                    {{ balanceLabel(balance.balance) }}
                                </span>
                                <span class="text-sm font-semibold min-w-[80px] text-right" :class="balanceTextClass(balance.balance)">
                                    {{ parseFloat(balance.balance) >= 0 ? '+' : '' }}{{ formatCurrency(balance.balance) }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="px-5 py-8 text-center text-gray-400 dark:text-gray-500">
                            <p>No balance data available.</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Section: Member Shares -->
            <div v-if="memberShares && memberShares.members?.length && memberShares.total_expense > 0" class="mt-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="sharesOpen = !sharesOpen" class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Shares</h2>
                        <svg class="w-4 h-4 text-gray-400 chevron-icon" :class="{ rotated: !sharesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div class="collapse-content" :class="{ collapsed: !sharesOpen }">
                    <div class="collapse-inner">
                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total Unsettled</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(memberShares.total_expense) }}</span>
                        </div>
                        <div
                            v-for="member in memberShares.members"
                            :key="member.user_id"
                            class="flex items-center gap-3 px-4 py-2.5 border-t border-gray-100 dark:border-gray-700"
                        >
                            <img
                                v-if="member.avatar"
                                :src="`/storage/${member.avatar}`"
                                :alt="member.name"
                                class="w-6 h-6 rounded-full object-cover shrink-0"
                            />
                            <div v-else class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-semibold text-[10px] shrink-0">
                                {{ getUserInitials(member.name) }}
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate">{{ member.name }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(member.total_share) }}</span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Suggested Settlements -->
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="suggestedOpen = !suggestedOpen" class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Suggested Settlements</h2>
                        <svg class="w-4 h-4 text-gray-400 chevron-icon" :class="{ rotated: !suggestedOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div class="collapse-content" :class="{ collapsed: !suggestedOpen }">
                    <div class="collapse-inner">
                        <!-- All settled state -->
                        <div v-if="!hasUnsettledBalances" class="px-5 py-10 text-center border-t border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">All settled!</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending settlements. Everyone is square.</p>
                        </div>

                        <!-- Suggested transactions -->
                        <div v-else class="space-y-3 p-4 border-t border-gray-100 dark:border-gray-700">
                            <div
                                v-for="(txn, index) in suggestedTransactions"
                                :key="index"
                                class="bg-gray-50 dark:bg-gray-900/50 rounded-xl px-5 py-4 flex items-center justify-between flex-wrap gap-3"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- From user -->
                                    <div class="flex items-center gap-2 shrink-0">
                                        <img
                                            v-if="txn.from?.avatar"
                                            :src="`/storage/${txn.from.avatar}`"
                                            :alt="txn.from.name"
                                            class="w-9 h-9 rounded-full object-cover"
                                        />
                                        <div v-else class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-700 dark:text-red-300 font-semibold text-sm">
                                            {{ getUserInitials(txn.from?.name) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 hidden sm:inline">{{ txn.from?.name }}</span>
                                    </div>

                                    <!-- Arrow -->
                                    <div class="flex flex-col items-center mx-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ formatCurrency(txn.amount) }}</span>
                                    </div>

                                    <!-- To user -->
                                    <div class="flex items-center gap-2 shrink-0">
                                        <img
                                            v-if="txn.to?.avatar"
                                            :src="`/storage/${txn.to.avatar}`"
                                            :alt="txn.to.name"
                                            class="w-9 h-9 rounded-full object-cover"
                                        />
                                        <div v-else class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-700 dark:text-green-300 font-semibold text-sm">
                                            {{ getUserInitials(txn.to?.name) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 hidden sm:inline">{{ txn.to?.name }}</span>
                                    </div>
                                </div>

                                <!-- Mobile names -->
                                <div class="w-full sm:hidden text-center text-sm text-gray-600 dark:text-gray-400">
                                    {{ txn.from?.name }} pays {{ txn.to?.name }}
                                </div>
                            </div>

                            <!-- Smart Action Button (admin only) -->
                            <div v-if="isAdmin" class="flex flex-col items-center pt-2 gap-2">
                                <!-- All Settled -->
                                <div v-if="isAllSettled" class="inline-flex items-center px-6 py-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-green-700 dark:text-green-300 font-semibold">All Settled!</span>
                                </div>
                                <!-- Mark All as Paid -->
                                <button
                                    v-else-if="hasPendingSettlements"
                                    @click="markAllPaid"
                                    :disabled="settlingAll"
                                    class="inline-flex items-center px-6 py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 cursor-pointer"
                                >
                                    <svg v-if="settlingAll" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    {{ settlingAll ? 'Marking...' : 'Mark All as Paid' }}
                                </button>
                                <!-- Settle Up -->
                                <button
                                    v-else-if="hasUnsettledBalances"
                                    @click="settleUp"
                                    :disabled="settlingUp"
                                    class="inline-flex items-center px-6 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 cursor-pointer"
                                >
                                    <svg v-if="settlingUp" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    {{ settlingUp ? 'Creating Settlements...' : 'Settle Up' }}
                                </button>
                            </div>
                            <div v-else class="text-center pt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Only group admin can initiate settlements.</p>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Settlement History -->
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="historyOpen = !historyOpen" class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Settlement History</h2>
                        <svg class="w-4 h-4 text-gray-400 chevron-icon" :class="{ rotated: !historyOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div class="collapse-content" :class="{ collapsed: !historyOpen }">
                    <div class="collapse-inner">
                        <!-- Empty state -->
                        <div v-if="!settlements?.data?.length" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 border-t border-gray-100 dark:border-gray-700">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No settlement history yet.</p>
                        </div>

                        <!-- Settlement rows -->
                        <div
                            v-for="settlement in settlements?.data"
                            :key="settlement.id"
                            class="border-t border-gray-100 dark:border-gray-700 px-5 py-4"
                        >
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <!-- From -> To -->
                                <div class="flex items-center gap-2 min-w-0">
                                    <!-- From user avatar -->
                                    <img
                                        v-if="settlement.from_user?.avatar"
                                        :src="`/storage/${settlement.from_user.avatar}`"
                                        :alt="settlement.from_user?.name"
                                        class="w-8 h-8 rounded-full object-cover shrink-0"
                                    />
                                    <div v-else class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-700 dark:text-red-300 font-semibold text-xs shrink-0">
                                        {{ getUserInitials(settlement.from_user?.name) }}
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-gray-100 truncate">{{ settlement.from_user?.name || 'Unknown' }}</span>

                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>

                                    <!-- To user avatar -->
                                    <img
                                        v-if="settlement.to_user?.avatar"
                                        :src="`/storage/${settlement.to_user.avatar}`"
                                        :alt="settlement.to_user?.name"
                                        class="w-8 h-8 rounded-full object-cover shrink-0"
                                    />
                                    <div v-else class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-700 dark:text-green-300 font-semibold text-xs shrink-0">
                                        {{ getUserInitials(settlement.to_user?.name) }}
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-gray-100 truncate">{{ settlement.to_user?.name || 'Unknown' }}</span>
                                </div>

                                <!-- Amount + Status + Action -->
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(settlement.amount) }}</span>

                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="settlement.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'"
                                    >
                                        {{ settlement.status === 'completed' ? 'Completed' : 'Pending' }}
                                    </span>

                                    <button
                                        v-if="settlement.status === 'pending' && canMarkAsPaid(settlement)"
                                        @click="completeSettlement(settlement.id)"
                                        :disabled="completingId === settlement.id"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors border border-green-300 dark:border-green-700 disabled:opacity-50"
                                    >
                                        <svg v-if="completingId === settlement.id" class="animate-spin -ml-0.5 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Mark as Paid
                                    </button>
                                </div>
                            </div>

                            <!-- Details row -->
                            <div class="mt-1.5 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                                <span>{{ formatDate(settlement.status === 'completed' ? settlement.settled_at : settlement.created_at) }}</span>
                                <span v-if="settlement.note">&middot;</span>
                                <span v-if="settlement.note" class="text-gray-600 dark:text-gray-300">{{ settlement.note }}</span>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="settlements?.data?.length && settlements.last_page > 1" class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing {{ settlements.from }} to {{ settlements.to }} of {{ settlements.total }} settlements
                            </p>
                            <div class="flex gap-1">
                                <Link
                                    v-for="link in settlements.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-1.5 rounded-md text-sm',
                                        link.active ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700',
                                        !link.url ? 'opacity-50 pointer-events-none' : '',
                                    ]"
                                    v-html="link.label"
                                    preserve-state
                                />
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
