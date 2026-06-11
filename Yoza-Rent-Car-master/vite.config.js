import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        https: true,
        cors: true,
        hmr: {
            host: 'croon-yo-yo-zesty.ngrok-free.dev',
            protocol: 'wss',         // pakai wss bukan ws karena HTTPS
        },
    },
});