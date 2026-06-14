import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    /**
     * Konfigurasi HMR (Hot Module Replacement).
     *
     * Untuk development lokal biasa: biarkan kosong (undefined).
     * Untuk tunnel seperti ngrok: isi VITE_HMR_HOST di .env.
     *
     * Contoh .env:
     *   VITE_HMR_HOST=nama-subdomain.ngrok-free.app
     *   VITE_HMR_PROTOCOL=wss
     */
    const hmrConfig = env.VITE_HMR_HOST
        ? {
              host     : env.VITE_HMR_HOST,
              protocol : env.VITE_HMR_PROTOCOL ?? 'ws',
          }
        : undefined;

    return {
        plugins: [
            laravel({
                input  : ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: {
            https : env.VITE_SERVER_HTTPS === 'true',
            cors  : true,
            hmr   : hmrConfig,
        },
    };
});
