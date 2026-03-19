<script setup>
import { ref, watch } from 'vue';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Subscript,
    Superscript,
    Font,
    Paragraph,
    Heading,
    Link,
    List,
    TodoList,
    BlockQuote,
    CodeBlock,
    Code,
    Table,
    TableToolbar,
    TableCellProperties,
    TableProperties,
    TableColumnResize,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    ImageInsert,
    ImageUpload,
    LinkImage,
    Alignment,
    Indent,
    IndentBlock,
    MediaEmbed,
    RemoveFormat,
    SourceEditing,
    GeneralHtmlSupport,
    HorizontalLine,
    FindAndReplace,
    Highlight,
    Undo,
    SimpleUploadAdapter,
    PasteFromOffice,
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

const props = defineProps({
    modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const content = ref(props.modelValue);
const isFullscreen = ref(false);

watch(() => props.modelValue, (val) => {
    if (val !== content.value) {
        content.value = val;
    }
});

watch(content, (val) => {
    emit('update:modelValue', val);
});

const editorConfig = {
    licenseKey: 'GPL',
    plugins: [
        Essentials,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Subscript,
        Superscript,
        Font,
        Paragraph,
        Heading,
        Link,
        List,
        TodoList,
        BlockQuote,
        CodeBlock,
        Code,
        Table,
        TableToolbar,
        TableCellProperties,
        TableProperties,
        TableColumnResize,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize,
        ImageInsert,
        ImageUpload,
        LinkImage,
        Alignment,
        Indent,
        IndentBlock,
        MediaEmbed,
        RemoveFormat,
        SourceEditing,
        GeneralHtmlSupport,
        HorizontalLine,
        FindAndReplace,
        Highlight,
        Undo,
        SimpleUploadAdapter,
        PasteFromOffice,
    ],
    toolbar: {
        items: [
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'fontSize', 'fontColor', 'fontBackgroundColor',
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'code', 'removeFormat',
            '|',
            'link', 'insertImage', 'insertTable', 'mediaEmbed', 'blockQuote', 'codeBlock', 'horizontalLine',
            '|',
            'alignment',
            '|',
            'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent',
            '|',
            'highlight', 'findAndReplace',
            '|',
            'sourceEditing',
        ],
        shouldNotGroupWhenFull: true,
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        ],
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            'toggleImageCaption',
            '|',
            'imageStyle:alignLeft',
            'imageStyle:alignCenter',
            'imageStyle:alignRight',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            '|',
            'resizeImage',
        ],
        resizeOptions: [
            { name: 'resizeImage:original', value: null, label: 'Original' },
            { name: 'resizeImage:25', value: '25', label: '25%' },
            { name: 'resizeImage:50', value: '50', label: '50%' },
            { name: 'resizeImage:75', value: '75', label: '75%' },
        ],
    },
    table: {
        contentToolbar: [
            'tableColumn', 'tableRow', 'mergeTableCells',
            'tableProperties', 'tableCellProperties',
        ],
    },
    simpleUpload: {
        uploadUrl: '/admin/upload-image',
        withCredentials: true,
        headers: {
            'X-CSRF-TOKEN': typeof document !== 'undefined'
                ? document.querySelector('meta[name="csrf-token"]')?.content
                : '',
        },
    },
    htmlSupport: {
        allow: [
            { name: /.*/, attributes: true, classes: true, styles: true },
        ],
    },
};
</script>

<template>
    <div :class="{ 'ck-fullscreen': isFullscreen }">
        <div class="flex justify-end mb-1">
            <button
                type="button"
                @click="isFullscreen = !isFullscreen"
                class="px-2.5 py-1 text-xs font-medium rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
            >
                {{ isFullscreen ? 'Exit Fullscreen' : 'Fullscreen' }}
            </button>
        </div>
        <ckeditor
            v-model="content"
            :editor="ClassicEditor"
            :config="editorConfig"
        />
    </div>
</template>

<style>
.ck-fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    background: white;
    padding: 1rem;
    overflow-y: auto;
}

.dark .ck-fullscreen {
    background: #1f2937;
}

.ck-fullscreen .ck-editor {
    height: calc(100vh - 5rem);
    display: flex;
    flex-direction: column;
}

.ck-fullscreen .ck-editor__editable {
    flex: 1;
    overflow-y: auto;
}

.ck-editor__editable {
    min-height: 250px;
}

.ck-body-wrapper .ck-balloon-panel {
    z-index: 10001 !important;
}
</style>
