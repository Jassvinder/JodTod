<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.post.title || '',
    slug: props.post.slug || '',
    content: props.post.content || '',
    excerpt: props.post.excerpt || '',
    meta_title: props.post.meta_title || '',
    meta_description: props.post.meta_description || '',
    featured_image: props.post.featured_image || '',
    is_published: props.post.is_published || false,
});

function submit() {
    form.put(route('admin.blog.update', props.post.id));
}
</script>

<template>
    <Head :title="'Edit: ' + post.title" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('admin.blog')" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Posts
                    </Link>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Blog Post</h2>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="mt-6 space-y-6">

                <!-- Title & Slug -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <TextInput
                            v-model="form.title"
                            type="text"
                            class="w-full"
                            placeholder="Enter post title"
                            autofocus
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-400 dark:text-gray-500">/blog/</span>
                            <TextInput
                                v-model="form.slug"
                                type="text"
                                class="flex-1"
                                placeholder="post-url-slug"
                            />
                        </div>
                        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                    <textarea
                        v-model="form.content"
                        rows="16"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-primary-500 focus:ring-primary-500 font-mono"
                        placeholder="Write your post content here... (HTML supported)"
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">HTML is supported. Use headings, paragraphs, lists, etc.</p>
                    <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                </div>

                <!-- Excerpt -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                    <textarea
                        v-model="form.excerpt"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Brief summary of the post (shown on blog listing page)"
                    ></textarea>
                    <p v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">{{ form.errors.excerpt }}</p>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">SEO Settings</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                        <TextInput
                            v-model="form.meta_title"
                            type="text"
                            class="w-full"
                            placeholder="Custom title for search engines (optional)"
                        />
                        <p v-if="form.errors.meta_title" class="mt-1 text-sm text-red-600">{{ form.errors.meta_title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                        <textarea
                            v-model="form.meta_description"
                            rows="2"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Custom description for search engines (optional)"
                        ></textarea>
                        <p v-if="form.errors.meta_description" class="mt-1 text-sm text-red-600">{{ form.errors.meta_description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Featured Image URL</label>
                        <TextInput
                            v-model="form.featured_image"
                            type="text"
                            class="w-full"
                            placeholder="https://example.com/image.jpg"
                        />
                        <p v-if="form.errors.featured_image" class="mt-1 text-sm text-red-600">{{ form.errors.featured_image }}</p>
                    </div>
                </div>

                <!-- Publish Toggle -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Publish</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Make this post visible on the public blog.</p>
                        </div>
                        <button
                            type="button"
                            @click="form.is_published = !form.is_published"
                            :class="form.is_published ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-600'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            <span
                                :class="form.is_published ? 'translate-x-5' : 'translate-x-0'"
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            />
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="route('admin.blog')"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.is_published ? 'Update & Publish' : 'Save as Draft' }}
                    </PrimaryButton>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>
