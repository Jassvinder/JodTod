<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Shared/Icon.vue';

// Shown wherever a contact picker is empty because the user has no contacts yet.
defineProps({
    // What the user is trying to do, e.g. "add group members"
    purpose: { type: String, required: true },
    // Inline variant for tight form rows.
    compact: { type: Boolean, default: false },
});
</script>

<template>
    <p v-if="compact" class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
        <Icon name="info" class="h-4 w-4 flex-shrink-0 text-amber-500" />
        <span>To {{ purpose }},</span>
        <Link :href="route('contacts.index')" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">add contacts first</Link>
    </p>
    <div v-else class="rounded-lg border border-amber-300 bg-amber-50 p-3 dark:border-amber-700/60 dark:bg-amber-900/20">
        <div class="flex items-start gap-2">
            <Icon name="info" class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600 dark:text-amber-400" />
            <div class="text-sm text-amber-900 dark:text-amber-200">
                <p>To {{ purpose }}, first add your friends to your contact list. They appear here once they approve your request.</p>
                <Link
                    :href="route('contacts.index')"
                    class="mt-2 inline-flex items-center gap-1 font-semibold text-primary-700 hover:text-primary-800 dark:text-primary-300"
                >
                    <Icon name="user-plus" class="h-4 w-4" />
                    Add Contact
                </Link>
            </div>
        </div>
    </div>
</template>
