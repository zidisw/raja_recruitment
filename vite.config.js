import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

const devHost = process.env.VITE_DEV_HOST ?? '127.0.0.1';
const devPort = Number(process.env.VITE_DEV_PORT ?? 5173);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: devHost,
        port: devPort,
        strictPort: true,
        cors: true,
        hmr: {
            host: devHost,
            port: devPort,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
