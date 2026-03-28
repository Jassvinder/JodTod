<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    photo: null,
});

const photoPreview = ref(null);
const photoInput = ref(null);

const selectPhoto = () => {
    photoInput.value.click();
};

const onPhotoChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.photo = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const confirmRemovePhoto = () => {
    if (!confirm('Are you sure you want to remove this photo?')) return;
    form.photo = null;
    photoPreview.value = null;
    if (photoInput.value) photoInput.value.value = '';
};

const submit = () => {
    form.post(route('groups.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Group" />

    <AppLayout>
        <div class="max-w-xl mx-auto">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('groups.index')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Group</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Photo Upload -->
                    <div class="flex flex-col items-center">
                        <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="onPhotoChange" />
                        <div class="relative">
                            <button type="button" @click="selectPhoto()" class="relative group">
                                <div v-if="photoPreview">
                                    <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600" />
                                </div>
                                <div v-else class="w-24 h-24 rounded-full border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 hover:border-primary-400 hover:text-primary-500 transition-colors">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs mt-1">Add Photo</span>
                                </div>
                            </button>
                            <button
                                v-if="photoPreview"
                                type="button"
                                @click="confirmRemovePhoto()"
                                class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-sm transition-colors"
                                title="Remove photo"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.photo" />
                    </div>

                    <div>
                        <InputLabel for="name" value="Group Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            placeholder="e.g., Goa Trip, Flat Expenses"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Description (optional)" />
                        <textarea
                            id="description"
                            class="mt-1 block w-full rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100"
                            v-model="form.description"
                            rows="3"
                            placeholder="What's this group for?"
                            maxlength="500"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create Group' }}
                        </PrimaryButton>
                        <Link :href="route('groups.index')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
