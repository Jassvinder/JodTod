<script setup>
import { ref } from 'vue';

const props = defineProps({
    form: Object,
    existingImages: { type: Object, default: () => ({}) },
});

const imagePreview1 = ref(null);
const imagePreview2 = ref(null);
const fileInput1 = ref(null);
const fileInput2 = ref(null);

function onImageSelect(event, slot) {
    const file = event.target.files[0];
    if (!file) return;
    if (slot === 1) {
        props.form.image_1 = file;
        imagePreview1.value = URL.createObjectURL(file);
    } else {
        props.form.image_2 = file;
        imagePreview2.value = URL.createObjectURL(file);
    }
}

function removeImage(slot) {
    if (slot === 1) {
        props.form.image_1 = null;
        imagePreview1.value = null;
        if (fileInput1.value) fileInput1.value.value = '';
        if (props.existingImages.image_1) props.form.remove_image_1 = true;
    } else {
        props.form.image_2 = null;
        imagePreview2.value = null;
        if (fileInput2.value) fileInput2.value.value = '';
        if (props.existingImages.image_2) props.form.remove_image_2 = true;
    }
}

function getImageSrc(slot) {
    if (slot === 1) {
        if (imagePreview1.value) return imagePreview1.value;
        if (props.existingImages.image_1 && !props.form.remove_image_1) return `/storage/${props.existingImages.image_1}`;
    } else {
        if (imagePreview2.value) return imagePreview2.value;
        if (props.existingImages.image_2 && !props.form.remove_image_2) return `/storage/${props.existingImages.image_2}`;
    }
    return null;
}
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Images <span class="text-gray-400 dark:text-gray-500">(optional, max 2)</span>
        </label>
        <div class="flex gap-3">
            <template v-for="slot in [1, 2]" :key="slot">
                <div class="relative">
                    <div v-if="getImageSrc(slot)" class="w-24 h-24 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 relative group">
                        <img :src="getImageSrc(slot)" class="w-full h-full object-cover" />
                        <button
                            type="button"
                            @click="removeImage(slot)"
                            class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <label v-else class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center cursor-pointer hover:border-primary-400 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-[10px] text-gray-400 mt-1">Add</span>
                        <input :ref="slot === 1 ? (el) => fileInput1 = el : (el) => fileInput2 = el" type="file" accept="image/*" class="hidden" @change="onImageSelect($event, slot)" />
                    </label>
                </div>
            </template>
        </div>
        <p v-if="form.errors.image_1" class="mt-1 text-sm text-accent-600">{{ form.errors.image_1 }}</p>
        <p v-if="form.errors.image_2" class="mt-1 text-sm text-accent-600">{{ form.errors.image_2 }}</p>
    </div>
</template>
