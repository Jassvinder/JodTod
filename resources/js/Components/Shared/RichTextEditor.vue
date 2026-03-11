<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import { watch, ref } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Start writing...' },
});

const emit = defineEmits(['update:modelValue']);

const fileInputRef = ref(null);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Image.configure({ inline: false, allowBase64: true }),
        Link.configure({ openOnClick: false }),
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none focus:outline-none min-h-[200px] px-4 py-3 dark:prose-invert',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (val) => {
    if (editor.value && editor.value.getHTML() !== val) {
        editor.value.commands.setContent(val || '', false);
    }
});

const addLink = () => {
    const url = window.prompt('Enter URL:');
    if (url) {
        editor.value.chain().focus().setLink({ href: url }).run();
    }
};

const triggerImageUpload = () => {
    fileInputRef.value?.click();
};

const onImageSelected = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post(route('admin.upload-image'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        if (response.data.url) {
            editor.value.chain().focus().setImage({ src: response.data.url }).run();
        }
    } catch {
        alert('Failed to upload image. Max size: 2MB.');
    }

    event.target.value = '';
};

const buttons = [
    { action: () => editor.value?.chain().focus().toggleBold().run(), icon: 'B', title: 'Bold', check: 'bold', style: 'font-bold' },
    { action: () => editor.value?.chain().focus().toggleItalic().run(), icon: 'I', title: 'Italic', check: 'italic', style: 'italic' },
    { action: () => editor.value?.chain().focus().toggleStrike().run(), icon: 'S', title: 'Strikethrough', check: 'strike', style: 'line-through' },
    { type: 'divider' },
    { action: () => editor.value?.chain().focus().toggleHeading({ level: 2 }).run(), icon: 'H2', title: 'Heading 2', check: 'heading', checkAttrs: { level: 2 } },
    { action: () => editor.value?.chain().focus().toggleHeading({ level: 3 }).run(), icon: 'H3', title: 'Heading 3', check: 'heading', checkAttrs: { level: 3 } },
    { type: 'divider' },
    { action: () => editor.value?.chain().focus().toggleBulletList().run(), icon: '•', title: 'Bullet List', check: 'bulletList' },
    { action: () => editor.value?.chain().focus().toggleOrderedList().run(), icon: '1.', title: 'Ordered List', check: 'orderedList' },
    { type: 'divider' },
    { action: () => editor.value?.chain().focus().toggleBlockquote().run(), icon: '"', title: 'Quote', check: 'blockquote' },
    { action: () => editor.value?.chain().focus().setHorizontalRule().run(), icon: '—', title: 'Divider' },
    { type: 'divider' },
    { action: addLink, icon: '🔗', title: 'Link' },
    { action: triggerImageUpload, icon: '📷', title: 'Image' },
];
</script>

<template>
    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <template v-for="(btn, i) in buttons" :key="i">
                <div v-if="btn.type === 'divider'" class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                <button
                    v-else
                    type="button"
                    @click="btn.action"
                    :title="btn.title"
                    class="px-2 py-1 text-sm rounded transition-colors"
                    :class="[
                        btn.style || '',
                        editor?.isActive(btn.check, btn.checkAttrs || {})
                            ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
                    ]"
                >
                    {{ btn.icon }}
                </button>
            </template>
        </div>

        <!-- Editor Content -->
        <EditorContent :editor="editor" />

        <!-- Hidden file input for image upload -->
        <input
            ref="fileInputRef"
            type="file"
            accept="image/*"
            class="hidden"
            @change="onImageSelected"
        />
    </div>
</template>

<style>
/* Editor prose styles */
.ProseMirror {
    min-height: 200px;
    padding: 12px 16px;
}
.ProseMirror:focus {
    outline: none;
}
.ProseMirror h2 { font-size: 1.5em; font-weight: 700; margin: 1em 0 0.5em; }
.ProseMirror h3 { font-size: 1.25em; font-weight: 600; margin: 0.8em 0 0.4em; }
.ProseMirror p { margin: 0.5em 0; }
.ProseMirror ul, .ProseMirror ol { padding-left: 1.5em; margin: 0.5em 0; }
.ProseMirror li { margin: 0.2em 0; }
.ProseMirror blockquote {
    border-left: 3px solid #d1d5db;
    padding-left: 1em;
    margin: 0.5em 0;
    color: #6b7280;
}
.dark .ProseMirror blockquote {
    border-color: #4b5563;
    color: #9ca3af;
}
.ProseMirror img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1em 0;
}
.ProseMirror a { color: #6366f1; text-decoration: underline; }
.ProseMirror hr { border-color: #e5e7eb; margin: 1em 0; }
.dark .ProseMirror hr { border-color: #374151; }
.dark .ProseMirror { color: #e5e7eb; }
</style>
