import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler', // or "modern"
                // 1. Silence warnings from dependencies (Bootstrap)
                quietDeps: true,
                // 2. Silence the specific 'import' warning in YOUR files
                silenceDeprecations: ['import'],
            },
        },
    },
});