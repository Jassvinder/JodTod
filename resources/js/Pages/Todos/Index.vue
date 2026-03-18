<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';

const props = defineProps({
    todos: Object,
    stats: Object,
    filters: Object,
});

// Add task form
const form = useForm({
    title: '',
    priority: 'medium',
    due_date: '',
});

const editingTodo = ref(null);
const editForm = useForm({
    title: '',
    priority: 'medium',
    due_date: '',
});

function addTask() {
    form.post(route('todos.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function startEdit(todo) {
    editingTodo.value = todo.id;
    editForm.title = todo.title;
    editForm.priority = todo.priority;
    editForm.due_date = todo.due_date ? todo.due_date.split('T')[0] : '';
}

function saveEdit(todo) {
    editForm.put(route('todos.update', todo.id), {
        preserveScroll: true,
        onSuccess: () => { editingTodo.value = null; },
    });
}

function cancelEdit() {
    editingTodo.value = null;
}

function toggleTodo(todo) {
    router.put(route('todos.toggle', todo.id), {}, {
        preserveScroll: true,
    });
}

async function deleteTodo(todo) {
    const confirmed = await confirmAction({
        title: 'Delete Task?',
        text: 'This task will be permanently removed.',
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
                <form @submit.prevent="addTask" class="flex flex-col sm:flex-row gap-3">
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
                            type="submit"
                            :disabled="form.processing"
                            class="py-2.5 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 text-sm whitespace-nowrap"
                        >
                            Add
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filters -->
            <div class="mt-4 flex flex-wrap gap-2">
                <button
                    @click="filterBy('status', 'pending')"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                        filters.status === 'pending'
                            ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300'
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]"
                >
                    Pending
                </button>
                <button
                    @click="filterBy('status', 'completed')"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                        filters.status === 'completed'
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]"
                >
                    Completed
                </button>
                <button
                    v-for="p in ['high', 'medium', 'low']"
                    :key="p"
                    @click="filterBy('priority', p)"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors capitalize',
                        filters.priority === p
                            ? priorityColors[p]
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]"
                >
                    {{ p }}
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
                    <div v-if="editingTodo === todo.id" class="flex flex-col sm:flex-row gap-3">
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
                            <button @click="saveEdit(todo)" class="py-2 px-3 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Save</button>
                            <button @click="cancelEdit" class="py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        </div>
                    </div>

                    <!-- View mode -->
                    <div v-else class="flex items-center gap-3">
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
                            <div class="flex items-center gap-2 mt-1">
                                <span :class="['px-1.5 py-0.5 rounded text-[10px] font-medium capitalize', priorityColors[todo.priority]]">
                                    {{ todo.priority }}
                                </span>
                                <span v-if="todo.due_date" class="text-[11px]" :class="isOverdue(todo) ? 'text-red-600 dark:text-red-400 font-medium' : isDueToday(todo) ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500'">
                                    {{ isOverdue(todo) ? 'Overdue: ' : isDueToday(todo) ? 'Today' : '' }}{{ !isDueToday(todo) ? formatDate(todo.due_date) : '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 flex-shrink-0">
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
