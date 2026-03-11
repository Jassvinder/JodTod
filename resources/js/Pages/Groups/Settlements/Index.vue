<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    group: Object,
    balances: Array,
    suggestedTransactions: Array,
    settlements: Object,
});

const authUser = computed(() => usePage().props.auth.user);

const isAdmin = computed(() => {
    return props.group.members?.find(
        m => m.id === authUser.value.id && m.pivot?.role === 'admin'
    );
});

const showSettleAllModal = ref(false);
const settlingUp = ref(false);
const completingId = ref(null);
const settlingAll = ref(false);

function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toFixed(2);
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
    return props.group.members?.find(m => m.id === userId) || { name: 'Unknown' };
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
    if (val > 0) return 'gets back';
    if (val < 0) return 'owes';
    return 'settled up';
}

function settleUp() {
    settlingUp.value = true;
    router.post(route('groups.settle', props.group.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            settlingUp.value = false;
        },
    });
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

function settleAll() {
    settlingAll.value = true;
    router.post(route('groups.settle-all', props.group.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            settlingAll.value = false;
            showSettleAllModal.value = false;
        },
    });
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
</script>

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
                        @click="showSettleAllModal = true"
                        class="px-3 py-2 text-sm font-medium text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                    >
                        Settle All
                    </button>
                </div>
            </div>

            <!-- Section 1: Balance Overview -->
            <div class="mt-6">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Balance Overview</h2>
                <div v-if="balances && balances.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div
                        v-for="balance in balances"
                        :key="balance.user_id"
                        class="rounded-xl border p-4"
                        :class="balanceCardClass(balance.balance)"
                    >
                        <div class="flex items-center gap-2 mb-2">
                            <img
                                v-if="getMember(balance.user_id)?.avatar"
                                :src="`/storage/${getMember(balance.user_id).avatar}`"
                                :alt="balance.name"
                                class="w-8 h-8 rounded-full object-cover"
                            />
                            <div v-else class="w-8 h-8 rounded-full bg-white/80 flex items-center justify-center font-semibold text-sm"
                                :class="balanceTextClass(balance.balance)"
                            >
                                {{ getUserInitials(balance.name) }}
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ balance.name }}</p>
                        </div>
                        <p class="text-lg font-bold" :class="balanceTextClass(balance.balance)">
                            {{ parseFloat(balance.balance) >= 0 ? '+' : '' }}{{ formatCurrency(balance.balance) }}
                        </p>
                        <p class="text-xs mt-0.5" :class="balanceTextClass(balance.balance)">
                            {{ balanceLabel(balance.balance) }}
                        </p>
                    </div>
                </div>
                <div v-else class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-8 text-center text-gray-400 dark:text-gray-500">
                    <p>No balance data available.</p>
                </div>
            </div>

            <!-- Section 2: Suggested Settlements -->
            <div class="mt-8">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Suggested Settlements</h2>

                <!-- All settled state -->
                <div v-if="!hasUnsettledBalances" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">All settled!</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending settlements. Everyone is square.</p>
                </div>

                <!-- Suggested transactions -->
                <div v-else class="space-y-3">
                    <div
                        v-for="(txn, index) in suggestedTransactions"
                        :key="index"
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 flex items-center justify-between flex-wrap gap-3"
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

                    <!-- Settle Up button -->
                    <div class="flex justify-center pt-2">
                        <button
                            @click="settleUp"
                            :disabled="settlingUp"
                            class="inline-flex items-center px-6 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="settlingUp" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ settlingUp ? 'Creating Settlements...' : 'Settle Up' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 3: Settlement History -->
            <div class="mt-8">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settlement History</h2>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Empty state -->
                    <div v-if="!settlements?.data?.length" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No settlement history yet.</p>
                    </div>

                    <!-- Settlement rows -->
                    <div
                        v-for="settlement in settlements?.data"
                        :key="settlement.id"
                        class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 px-5 py-4"
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

        <!-- Settle All Confirmation Modal -->
        <Modal :show="showSettleAllModal" @close="showSettleAllModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Settle All Pending</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    This will mark ALL pending settlements as completed. Are you sure?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showSettleAllModal = false">Cancel</SecondaryButton>
                    <button
                        @click="settleAll"
                        :disabled="settlingAll"
                        class="inline-flex items-center rounded-md border border-transparent bg-amber-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        <svg v-if="settlingAll" class="animate-spin -ml-0.5 mr-1.5 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ settlingAll ? 'Settling...' : 'Settle All' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
