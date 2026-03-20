<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    user: Object,
    recentExpenses: Array,
    groups: Array,
    contacts: Array,
});

const deletingContactId = ref(null);

async function deleteContact(contact) {
    const confirmed = await confirmAction({
        title: 'Delete Contact',
        text: `Are you sure you want to remove ${contact.contact_user?.name || 'this contact'} from ${props.user.name}'s contacts?`,
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        deletingContactId.value = contact.id;
        router.delete(route('admin.users.contacts.delete', [props.user.id, contact.id]), {
            preserveScroll: true,
            onFinish: () => {
                deletingContactId.value = null;
            },
        });
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amount || 0);
}
</script>

<template>
    <Head :title="`User: ${user.name}`" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Back Link -->
            <Link
                :href="route('admin.users')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors mb-6"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Users
            </Link>

            <!-- User Info Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                        {{ user.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.name }}</h2>
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                    user.role === 'admin'
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                ]"
                            >
                                {{ user.role === 'admin' ? 'Admin' : 'User' }}
                            </span>
                        </div>

                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ user.email }}
                                <span
                                    v-if="user.email_verified_at"
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400"
                                >
                                    <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Verified
                                </span>
                            </p>
                            <p v-if="user.phone" class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ user.phone }}
                                <span
                                    v-if="user.phone_verified_at"
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400"
                                >
                                    <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Verified
                                </span>
                            </p>
                        </div>

                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Joined {{ formatDate(user.created_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.expenses_count ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Groups</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.groups_count ?? 0 }}</p>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Expenses</h3>
                </div>

                <div v-if="!recentExpenses || recentExpenses.length === 0" class="px-6 py-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No expenses recorded yet</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="expense in recentExpenses" :key="expense.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ expense.description }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(expense.amount) }}</span>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span v-if="expense.category" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ expense.category?.name || expense.category }}
                                    </span>
                                    <span v-else class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(expense.date || expense.created_at) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Groups -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Groups</h3>
                </div>

                <div v-if="!groups || groups.length === 0" class="px-6 py-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Not a member of any groups</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Group Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Members</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="group in groups" :key="group.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                            {{ group.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ group.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                            group.pivot?.role === 'admin'
                                                ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                        ]"
                                    >
                                        {{ group.pivot?.role === 'admin' ? 'Admin' : 'Member' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        {{ group.members_count ?? 0 }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contacts -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contacts</h3>
                </div>

                <div v-if="!contacts || contacts.length === 0" class="px-6 py-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No contacts added yet</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Phone</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="contact.contact_user?.avatar"
                                            :src="`/storage/${contact.contact_user.avatar}`"
                                            :alt="contact.contact_user?.name"
                                            class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                        />
                                        <div v-else class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                            {{ contact.contact_user?.name?.charAt(0)?.toUpperCase() || '?' }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ contact.contact_user?.name || 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ contact.contact_user?.email || '-' }}</span>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ contact.contact_user?.phone || '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        @click="deleteContact(contact)"
                                        :disabled="deletingContactId === contact.id"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ deletingContactId === contact.id ? 'Deleting...' : 'Delete' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
