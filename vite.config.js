import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // <--- Impor ini
import tailwindcss from '@tailwindcss/vite'; // <--- Impor ini

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // <--- Jalankan ini
        tailwindcss(), // <--- Jalankan ini
    ],
});
