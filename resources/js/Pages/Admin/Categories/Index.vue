<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

// Add Category
const showAddModal = ref(false);
const addForm = useForm({
    name: '',
    icon: '',
});

function openAddModal() {
    addForm.reset();
    addForm.clearErrors();
    showAddModal.value = true;
}

function addCategory() {
    addForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
}

// Edit Category
const showEditModal = ref(false);
const editForm = useForm({
    name: '',
    icon: '',
});
const editingCategory = ref(null);

function openEditModal(category) {
    editingCategory.value = category;
    editForm.name = category.name;
    editForm.icon = category.icon || '';
    editForm.clearErrors();
    showEditModal.value = true;
}

function updateCategory() {
    if (!editingCategory.value) return;
    editForm.put(route('admin.categories.update', editingCategory.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editingCategory.value = null;
            editForm.reset();
        },
    });
}

// Delete Category
const showDeleteModal = ref(false);
const categoryToDelete = ref(null);

function confirmDelete(category) {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
}

function deleteCategory() {
    if (!categoryToDelete.value) return;
    router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            categoryToDelete.value = null;
        },
    });
}
</script>

<template>
    <Head title="Category Management" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Categories</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">{{ categories.length }} {{ categories.length === 1 ? 'category' : 'categories' }}</p>
                </div>
                <PrimaryButton @click="openAddModal">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Category
                </PrimaryButton>
            </div>

            <!-- Categories List -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div v-if="categories.length === 0" class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No categories yet</p>
                    <button @click="openAddModal" class="mt-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Add your first category
                    </button>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expenses</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <!-- Category Name + Icon -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-lg flex-shrink-0">
                                            <span v-if="category.icon">{{ category.icon }}</span>
                                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ category.name }}</span>
                                    </div>
                                </td>

                                <!-- Expenses Count -->
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ category.expenses_count }} {{ category.expenses_count === 1 ? 'expense' : 'expenses' }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit -->
                                        <button
                                            @click="openEditModal(category)"
                                            class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            title="Edit category"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <!-- Delete -->
                                        <button
                                            v-if="category.expenses_count === 0"
                                            @click="confirmDelete(category)"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="Delete category"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <span
                                            v-else
                                            class="p-1.5 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed"
                                            title="Cannot delete: category has expenses"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <Modal :show="showAddModal" max-width="md" @close="showAddModal = false">
            <form @submit.prevent="addCategory" class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add Category</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new expense category.</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <TextInput
                            v-model="addForm.name"
                            type="text"
                            class="w-full"
                            placeholder="e.g. Food, Transport, Shopping"
                            autofocus
                        />
                        <p v-if="addForm.errors.name" class="mt-1 text-sm text-red-600">{{ addForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon (optional)</label>
                        <TextInput
                            v-model="addForm.icon"
                            type="text"
                            class="w-full"
                            placeholder="e.g. emoji or icon name"
                        />
                        <p v-if="addForm.errors.icon" class="mt-1 text-sm text-red-600">{{ addForm.errors.icon }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="showAddModal = false"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <PrimaryButton type="submit" :disabled="addForm.processing">
                        Save Category
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Edit Category Modal -->
        <Modal :show="showEditModal" max-width="md" @close="showEditModal = false">
            <form @submit.prevent="updateCategory" class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Category</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the category details.</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <TextInput
                            v-model="editForm.name"
                            type="text"
                            class="w-full"
                            placeholder="Category name"
                            autofocus
                        />
                        <p v-if="editForm.errors.name" class="mt-1 text-sm text-red-600">{{ editForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon (optional)</label>
                        <TextInput
                            v-model="editForm.icon"
                            type="text"
                            class="w-full"
                            placeholder="e.g. emoji or icon name"
                        />
                        <p v-if="editForm.errors.icon" class="mt-1 text-sm text-red-600">{{ editForm.errors.icon }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="showEditModal = false"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <PrimaryButton type="submit" :disabled="editForm.processing">
                        Update Category
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete Category</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete <strong>{{ categoryToDelete?.name }}</strong>? This action cannot be undone.
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
                        @click="deleteCategory"
                        class="px-4 py-2 rounded-lg bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors"
                    >
                        Delete Category
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
