import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/site.css',
                'resources/js/site.js',
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
                'resources/js/builder/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // The site is opened through Laragon (http://ismail) as well as `artisan serve`:
        // allow the dev server's modules to be loaded from any local origin.
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**', '**/public/uploads/**'],
        },
    },
});
