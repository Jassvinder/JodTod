import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    // Treat cropperjs v2 web components as custom elements
                    isCustomElement: (tag) => tag.startsWith('cropper-'),
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        // CKEditor is only pulled into lazy-loaded admin editor pages, but its
        // minified editor chunk is intentionally larger than Vite's 500 kB default.
        chunkSizeWarningLimit: 1300,
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', '@inertiajs/vue3'],
                    'charts': ['chart.js', 'vue-chartjs'],
                },
            },
        },
    },
});
