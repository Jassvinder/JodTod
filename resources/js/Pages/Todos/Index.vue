<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const authUserId = computed(() => usePage().props.auth?.user?.id);
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    todos: Object,
    stats: Object,
    categories: Array,
    contacts: Array,
    filters: Object,
});

// Preset colors for category picker
const presetColors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#14b8a6', '#f97316', '#64748b'];

// Add task form
const form = useForm({
    title: '',
    priority: 'medium',
    due_date: '',
    reminder_at: '',
    todo_category_id: '',
    assigned_to: '',
});

const editingTodo = ref(null);
const editForm = useForm({
    title: '',
    priority: 'medium',
    due_date: '',
    reminder_at: '',
    todo_category_id: '',
    assigned_to: '',
});

const showReminderAdd = ref(false);
const showReminderEdit = ref(false);

// Category management
const showCategoryManager = ref(false);
const editingCategory = ref(null);
const categoryForm = useForm({ name: '', color: '#6366f1' });
const editCategoryForm = useForm({ name: '', color: '#6366f1' });

function addTask() {
    form.post(route('todos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showReminderAdd.value = false;
        },
    });
}

function setDefaultReminder() {
    if (form.due_date && !form.reminder_at) {
        form.reminder_at = form.due_date + 'T08:00';
    }
    showReminderAdd.value = true;
}

function startEdit(todo) {
    editingTodo.value = todo.id;
    editForm.title = todo.title;
    editForm.priority = todo.priority;
    editForm.due_date = todo.due_date ? todo.due_date.split('T')[0] : '';
    editForm.reminder_at = todo.reminder_at ? todo.reminder_at.replace(':00.000000Z', '').replace(' ', 'T').slice(0, 16) : '';
    editForm.todo_category_id = todo.todo_category_id || '';
    editForm.assigned_to = todo.assigned_to || '';
    showReminderEdit.value = !!todo.reminder_at;
}

function saveEdit(todo) {
    editForm.put(route('todos.update', todo.id), {
        preserveScroll: true,
        onSuccess: () => { editingTodo.value = null; },
    });
}

function cancelEdit() {
    editingTodo.value = null;
    showReminderEdit.value = false;
}

function toggleTodo(todo) {
    router.put(route('todos.toggle', todo.id), {}, {
        preserveScroll: true,
    });
}

async function deleteTodo(todo) {
    // Assignee cannot delete
    if (todo.assigned_to === authUserId.value && todo.user_id !== authUserId.value) {
        await confirmAction({
            title: 'Cannot Delete',
            text: 'This task was assigned to you. Only the creator can delete it.',
            confirmText: 'OK',
            icon: 'info',
        });
        return;
    }

    const assigneeName = todo.assignee?.name;
    const isAssigned = todo.assigned_to && todo.assigned_to !== todo.user_id && assigneeName;

    const confirmed = await confirmAction({
        title: 'Delete Task?',
        ...(isAssigned
            ? { html: `This task is assigned to <b>${assigneeName}</b>. It will be permanently removed for both of you.` }
            : { text: 'This task will be permanently removed.' }),
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('todos.destroy', todo.id), {
            preserveScroll: true,
        });
    }
}

function filterBy(key, value) {
    router.get(route('todos.index'), {
        ...props.filters,
        [key]: props.filters[key] === value ? null : value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function isOverdue(todo) {
    if (!todo.due_date || todo.is_completed) return false;
    return new Date(todo.due_date) < new Date(new Date().toDateString());
}

function isDueToday(todo) {
    if (!todo.due_date || todo.is_completed) return false;
    return new Date(todo.due_date).toDateString() === new Date().toDateString();
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
}

function formatReminderTime(reminderAt) {
    if (!reminderAt) return '';
    const date = new Date(reminderAt);
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }) + ' ' +
        date.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
}

// Category CRUD
function addCategory() {
    categoryForm.post(route('todo-categories.store'), {
        preserveScroll: true,
        onSuccess: () => categoryForm.reset('name'),
    });
}

function startEditCategory(cat) {
    editingCategory.value = cat.id;
    editCategoryForm.name = cat.name;
    editCategoryForm.color = cat.color;
}

function saveCategory(cat) {
    editCategoryForm.put(route('todo-categories.update', cat.id), {
        preserveScroll: true,
        onSuccess: () => { editingCategory.value = null; },
    });
}

async function deleteCategory(cat) {
    const confirmed = await confirmAction({
        title: 'Delete Category?',
        text: cat.todos_count > 0
            ? `${cat.todos_count} task(s) in this category will become uncategorized.`
            : 'This category will be removed.',
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) {
        router.delete(route('todo-categories.destroy', cat.id), {
            preserveScroll: true,
        });
    }
}

function getCategoryById(id) {
    return props.categories.find(c => c.id === id);
}

const priorityColors = {
    high: 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-900/30',
    medium: 'text-amber-600 bg-amber-50 dark:text-amber-400 dark:bg-amber-900/30',
    low: 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/30',
};
</script>

<template>
    <Head title="My Tasks" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Tasks</h2>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Keep track of things you need to do</p>
                </div>
                <button
                    @click="showCategoryManager = !showCategoryManager"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="showCategoryManager
                        ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300'
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categories
                </button>
            </div>

            <!-- Category Manager -->
            <div v-if="showCategoryManager" class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Manage Categories</h3>

                <!-- Add category form -->
                <form @submit.prevent="addCategory" class="flex items-center gap-2 mb-3">
                    <div class="flex items-center gap-1.5">
                        <label v-for="c in presetColors" :key="c" class="cursor-pointer">
                            <input type="radio" :value="c" v-model="categoryForm.color" class="sr-only peer" />
                            <span
                                class="block w-6 h-6 rounded-full border-2 transition-all peer-checked:ring-2 peer-checked:ring-offset-1 dark:peer-checked:ring-offset-gray-800"
                                :style="{ backgroundColor: c, borderColor: c }"
                                :class="categoryForm.color === c ? 'ring-2 ring-offset-1 dark:ring-offset-gray-800' : ''"
                            ></span>
                        </label>
                    </div>
                    <input
                        v-model="categoryForm.name"
                        type="text"
                        placeholder="Category name"
                        maxlength="50"
                        required
                        class="flex-1 py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500"
                    />
                    <button
                        type="submit"
                        :disabled="categoryForm.processing"
                        class="py-2 px-3 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
                    >
                        Add
                    </button>
                </form>
                <p v-if="categoryForm.errors.name" class="text-xs text-red-600 mb-2">{{ categoryForm.errors.name }}</p>

                <!-- Category list -->
                <div v-if="categories.length === 0" class="text-center py-4">
                    <p class="text-sm text-gray-400 dark:text-gray-500">No categories yet. Create one above!</p>
                </div>
                <div v-else class="space-y-2">
                    <div v-for="cat in categories" :key="cat.id" class="flex items-center gap-2 group">
                        <template v-if="editingCategory === cat.id">
                            <div class="flex items-center gap-1">
                                <label v-for="c in presetColors" :key="c" class="cursor-pointer">
                                    <input type="radio" :value="c" v-model="editCategoryForm.color" class="sr-only peer" />
                                    <span
                                        class="block w-5 h-5 rounded-full border-2 transition-all"
                                        :style="{ backgroundColor: c, borderColor: c }"
                                        :class="editCategoryForm.color === c ? 'ring-2 ring-offset-1 dark:ring-offset-gray-800' : ''"
                                    ></span>
                                </label>
                            </div>
                            <input
                                v-model="editCategoryForm.name"
                                type="text"
                                maxlength="50"
                                class="flex-1 py-1.5 px-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
                                @keyup.enter="saveCategory(cat)"
                                @keyup.escape="editingCategory = null"
                            />
                            <button @click="saveCategory(cat)" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Save</button>
                            <button @click="editingCategory = null" class="text-gray-400 hover:text-gray-600 text-xs">Cancel</button>
                        </template>
                        <template v-else>
                            <span class="w-4 h-4 rounded-full flex-shrink-0" :style="{ backgroundColor: cat.color }"></span>
                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">{{ cat.name }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ cat.todos_count }}</span>
                            <button @click="startEditCategory(cat)" class="p-1 text-gray-400 hover:text-primary-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteCategory(cat)" class="p-1 text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.total }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ stats.pending }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ stats.completed }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Done</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ stats.overdue }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Overdue</p>
                </div>
            </div>

            <!-- Add Task Form -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <form @submit.prevent="addTask" class="flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="What do you need to do?"
                                maxlength="255"
                                class="w-full py-2.5 px-4 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                        </div>
                        <div class="flex gap-2">
                            <select
                                v-model="form.priority"
                                class="py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <input
                                v-model="form.due_date"
                                type="date"
                                class="py-2.5 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
                            />
                            <button
                                type="button"
                                @click="setDefaultReminder"
                                class="p-2.5 rounded-lg border transition-colors text-sm"
                                :class="showReminderAdd
                                    ? 'border-purple-300 bg-purple-50 text-purple-600 dark:border-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                    : 'border-gray-300 dark:border-gray-600 text-gray-400 hover:text-purple-600 hover:border-purple-300 dark:hover:text-purple-400'"
                                title="Set Reminder"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="py-2.5 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 text-sm whitespace-nowrap"
                            >
                                Add
                            </button>
                        </div>
                    </div>
                    <!-- Category + Assign + Reminder row -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Category selector -->
                        <div v-if="categories.length > 0" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <select
                                v-model="form.todo_category_id"
                                class="py-1.5 px-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs"
                            >
                                <option value="">No category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <!-- Assign to contact -->
                        <div v-if="contacts.length > 0" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <select
                                v-model="form.assigned_to"
                                class="py-1.5 px-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs"
                            >
                                <option value="">Assign to...</option>
                                <option v-for="c in contacts" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <!-- Reminder -->
                        <div v-if="showReminderAdd" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">Reminder:</span>
                            <input
                                v-model="form.reminder_at"
                                type="datetime-local"
                                class="py-1.5 px-2 rounded-lg border border-purple-200 dark:border-purple-700 dark:bg-gray-700 dark:text-gray-200 text-xs focus:ring-2 focus:ring-purple-500"
                            />
                            <button
                                type="button"
                                @click="showReminderAdd = false; form.reminder_at = ''"
                                class="text-gray-400 hover:text-red-500 p-1"
                                title="Remove reminder"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filters -->
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <!-- Status -->
                <select
                    :value="filters.status || ''"
                    @change="filterBy('status', $event.target.value || null)"
                    class="py-1.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs font-medium focus:ring-primary-500"
                >
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>

                <!-- Priority -->
                <select
                    :value="filters.priority || ''"
                    @change="filterBy('priority', $event.target.value || null)"
                    class="py-1.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs font-medium focus:ring-primary-500"
                >
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>

                <!-- Assignment scope -->
                <select
                    v-if="contacts.length > 0"
                    :value="filters.scope || ''"
                    @change="filterBy('scope', $event.target.value || null)"
                    class="py-1.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs font-medium focus:ring-primary-500"
                >
                    <option value="">All Tasks</option>
                    <option value="mine">My Tasks</option>
                    <option value="assigned_to_me">Assigned to me</option>
                    <option value="assigned_by_me">Assigned by me</option>
                </select>

                <!-- Category -->
                <select
                    v-if="categories.length > 0"
                    :value="filters.category || ''"
                    @change="filterBy('category', $event.target.value || null)"
                    class="py-1.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs font-medium focus:ring-primary-500"
                >
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="'filter-' + cat.id" :value="String(cat.id)">
                        {{ cat.name }} ({{ cat.todos_count }})
                    </option>
                </select>

                <!-- Reset -->
                <button
                    v-if="filters.status || filters.priority || filters.scope || filters.category"
                    @click="router.get(route('todos.index'), {}, { preserveState: true, preserveScroll: true })"
                    class="py-1.5 px-3 rounded-lg text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                    Clear
                </button>
            </div>

            <!-- Task List -->
            <div class="mt-4 space-y-2">
                <div v-if="todos.data.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No tasks yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add your first task above!</p>
                </div>

                <div
                    v-for="todo in todos.data"
                    :key="todo.id"
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 transition-all"
                    :class="{
                        'border-red-200 dark:border-red-800/50 bg-red-50/30 dark:bg-red-900/10': isOverdue(todo),
                        'border-amber-200 dark:border-amber-800/50': isDueToday(todo),
                        'opacity-60': todo.is_completed,
                    }"
                >
                    <!-- Edit mode -->
                    <div v-if="editingTodo === todo.id" class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <input
                                    v-model="editForm.title"
                                    type="text"
                                    maxlength="255"
                                    class="w-full py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500"
                                    @keyup.enter="saveEdit(todo)"
                                    @keyup.escape="cancelEdit"
                                />
                            </div>
                            <div class="flex gap-2">
                                <select v-model="editForm.priority" class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <input v-model="editForm.due_date" type="date" class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                                <button
                                    type="button"
                                    @click="showReminderEdit = !showReminderEdit; if (!showReminderEdit) editForm.reminder_at = ''"
                                    class="p-2 rounded-lg border transition-colors"
                                    :class="showReminderEdit
                                        ? 'border-purple-300 bg-purple-50 text-purple-600 dark:border-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                        : 'border-gray-300 dark:border-gray-600 text-gray-400 hover:text-purple-600'"
                                    title="Set Reminder"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>
                                <button @click="saveEdit(todo)" class="py-2 px-3 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Save</button>
                                <button @click="cancelEdit" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            </div>
                        </div>
                        <!-- Edit category + assign + reminder row -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div v-if="categories.length > 0" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <select
                                    v-model="editForm.todo_category_id"
                                    class="py-1.5 px-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs"
                                >
                                    <option value="">No category</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                            <div v-if="contacts.length > 0" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <select
                                    v-model="editForm.assigned_to"
                                    class="py-1.5 px-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs"
                                >
                                    <option value="">Assign to...</option>
                                    <option v-for="c in contacts" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                            <div v-if="showReminderEdit" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">Reminder:</span>
                                <input
                                    v-model="editForm.reminder_at"
                                    type="datetime-local"
                                    class="py-1.5 px-2 rounded-lg border border-purple-200 dark:border-purple-700 dark:bg-gray-700 dark:text-gray-200 text-xs focus:ring-2 focus:ring-purple-500"
                                />
                                <button
                                    type="button"
                                    @click="showReminderEdit = false; editForm.reminder_at = ''"
                                    class="text-gray-400 hover:text-red-500 p-1"
                                    title="Remove reminder"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View mode -->
                    <div v-else class="flex items-center gap-3">
                        <!-- Category color bar -->
                        <span
                            v-if="todo.category"
                            class="w-1 self-stretch rounded-full flex-shrink-0"
                            :style="{ backgroundColor: todo.category.color }"
                        ></span>

                        <!-- Checkbox -->
                        <button
                            @click="toggleTodo(todo)"
                            class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
                            :class="todo.is_completed
                                ? 'border-green-500 bg-green-500'
                                : 'border-gray-300 dark:border-gray-600 hover:border-primary-500'
                            "
                        >
                            <svg v-if="todo.is_completed" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium truncate"
                                :class="todo.is_completed ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100'"
                            >
                                {{ todo.title }}
                            </p>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span :class="['px-1.5 py-0.5 rounded text-[10px] font-medium capitalize', priorityColors[todo.priority]]">
                                    {{ todo.priority }}
                                </span>
                                <span
                                    v-if="todo.category"
                                    class="px-1.5 py-0.5 rounded text-[10px] font-medium text-white"
                                    :style="{ backgroundColor: todo.category.color }"
                                >
                                    {{ todo.category.name }}
                                </span>
                                <span v-if="todo.assigned_to && todo.assigned_to !== todo.user_id && todo.assignee" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                    <template v-if="todo.assigned_to === authUserId">From: {{ todo.user?.name }}</template>
                                    <template v-else>To: {{ todo.assignee?.name }}</template>
                                </span>
                                <span v-if="todo.due_date" class="text-[11px]" :class="isOverdue(todo) ? 'text-red-600 dark:text-red-400 font-medium' : isDueToday(todo) ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500'">
                                    {{ isOverdue(todo) ? 'Overdue: ' : isDueToday(todo) ? 'Today' : '' }}{{ !isDueToday(todo) ? formatDate(todo.due_date) : '' }}
                                </span>
                                <span v-if="todo.reminder_at && !todo.reminder_sent" class="inline-flex items-center gap-0.5 text-[11px] text-purple-500 dark:text-purple-400" :title="'Reminder: ' + formatReminderTime(todo.reminder_at)">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    {{ formatReminderTime(todo.reminder_at) }}
                                </span>
                                <span v-if="todo.reminder_at && todo.reminder_sent" class="inline-flex items-center gap-0.5 text-[11px] text-gray-400 dark:text-gray-500" title="Reminder sent">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Reminded
                                </span>
                            </div>
                        </div>

                        <!-- Actions (only owner can edit/delete) -->
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <template v-if="todo.user_id === authUserId">
                                <button
                                    @click="startEdit(todo)"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="deleteTodo(todo)"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="todos.links && todos.last_page > 1" class="mt-6 flex justify-center gap-1">
                <template v-for="link in todos.links" :key="link.label">
                    <button
                        v-if="link.url"
                        @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm',
                            link.active
                                ? 'bg-primary-600 text-white'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                        ]"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="px-3 py-2 text-sm text-gray-400"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
