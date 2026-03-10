<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    users: Object, // paginated
    filters: {
        type: Object,
        default: () => ({ search: '', role: '' }),
    },
});

const currentUser = computed(() => usePage().props.auth?.user);

// Filter state
const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');

// Debounced search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch(roleFilter, () => {
    applyFilters();
});

function applyFilters() {
    router.get(route('admin.users'), {
        search: search.value || undefined,
        role: roleFilter.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

// Role change
function changeRole(user, newRole) {
    if (user.id === currentUser.value?.id) return;
    router.put(route('admin.users.role', user.id), {
        role: newRole,
    }, {
        preserveScroll: true,
    });
}

// Delete user
const showDeleteModal = ref(false);
const userToDelete = ref(null);

function confirmDelete(user) {
    userToDelete.value = user;
    showDeleteModal.value = true;
}

function deleteUser() {
    if (!userToDelete.value) return;
    router.delete(route('admin.users.delete', userToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="User Management" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Management</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">{{ users.total }} {{ users.total === 1 ? 'user' : 'users' }} total</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Search by name or email..."
                        class="w-full"
                    />
                </div>
                <select
                    v-model="roleFilter"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-gray-900 dark:text-gray-100 shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Users Table -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div v-if="users.data.length === 0" class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No users found</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Joined</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <!-- User info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                            {{ user.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Phone -->
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div v-if="user.phone" class="flex items-center gap-1.5">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ user.phone }}</span>
                                        <span
                                            v-if="user.phone_verified_at"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400"
                                            title="Verified"
                                        >
                                            <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Verified
                                        </span>
                                    </div>
                                    <span v-else class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4">
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
                                </td>

                                <!-- Joined -->
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(user.created_at) }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Role Selector -->
                                        <select
                                            v-if="user.id !== currentUser?.id"
                                            :value="user.role"
                                            @change="changeRole(user, $event.target.value)"
                                            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-700 dark:text-gray-300 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                        >
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>

                                        <!-- Delete Button (hidden for current user) -->
                                        <button
                                            v-if="user.id !== currentUser?.id"
                                            @click="confirmDelete(user)"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="Delete user"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <!-- Current user indicator -->
                                        <span
                                            v-if="user.id === currentUser?.id"
                                            class="text-xs text-gray-400 italic"
                                        >You</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.last_page > 1" class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing {{ users.from }} to {{ users.to }} of {{ users.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <template v-for="link in users.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1.5 rounded-lg text-sm transition-colors',
                                        link.active
                                            ? 'bg-primary-600 text-white font-medium'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                    ]"
                                    v-html="link.label"
                                    preserve-scroll
                                />
                                <span
                                    v-else
                                    class="px-3 py-1.5 text-sm text-gray-300 dark:text-gray-600"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="md" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete User</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>? This action cannot be undone. All their data including expenses, group memberships, and settlements will be permanently removed.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteUser"
                        class="px-4 py-2 rounded-lg bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors"
                    >
                        Delete User
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
