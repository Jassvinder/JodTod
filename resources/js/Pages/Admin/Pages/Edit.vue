<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextEditor from '@/Components/Shared/RichTextEditor.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    page: Object,
});

const form = useForm({
    title: props.page.title,
    content: props.page.content || '',
    meta_title: props.page.meta_title || '',
    meta_description: props.page.meta_description || '',
    is_published: props.page.is_published,
});

const submit = () => {
    form.put(route('admin.pages.update', props.page.id));
};
</script>

<template>
    <Head :title="`Edit: ${page.title}`" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('admin.pages')" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700">
                        &larr; Back to Pages
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">Edit: {{ page.title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">URL: /{{ page.slug }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Page Title" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Content Editor -->
                        <div>
                            <InputLabel value="Content" />
                            <div class="mt-1">
                                <RichTextEditor v-model="form.content" />
                            </div>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">SEO Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="meta_title" value="Meta Title (max 60 chars)" />
                            <TextInput
                                id="meta_title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.meta_title"
                                maxlength="60"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ (form.meta_title || '').length }}/60</p>
                            <InputError class="mt-1" :message="form.errors.meta_title" />
                        </div>

                        <div>
                            <InputLabel for="meta_description" value="Meta Description (max 160 chars)" />
                            <textarea
                                id="meta_description"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                rows="2"
                                v-model="form.meta_description"
                                maxlength="160"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ (form.meta_description || '').length }}/160</p>
                            <InputError class="mt-1" :message="form.errors.meta_description" />
                        </div>
                    </div>
                </div>

                <!-- Publish & Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                v-model="form.is_published"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                            />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Published</span>
                        </label>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('admin.pages')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Page' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
