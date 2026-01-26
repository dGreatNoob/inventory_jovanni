import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    build: {
        chunkSizeWarningLimit: 1000,
        // Use relative base for assets to work with any domain
        base: '/',
        // Manual chunking for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['axios'],
                    'charts': ['apexcharts'],
                    'ui': ['flowbite', 'sweetalert2'],
                    'qrcode': ['html5-qrcode'],
                },
            },
        },
    },
});
