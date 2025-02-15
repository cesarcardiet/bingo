import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ["pusher-js", "laravel-echo"]
    },
    build: {
        rollupOptions: {
            external: ["pusher-js", "laravel-echo"]
        }
    },
    server: {
        hmr: {
            overlay: false // 🔥 Desactiva el overlay de errores molestos
        }
    }
});
