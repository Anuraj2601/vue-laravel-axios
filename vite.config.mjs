import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';


export default defineConfig({
    css: {
            devSourcemap: true,
        },
    base:  '/',
    plugins: [
        vue(),
        tailwindcss(),
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
            refresh: true,
        }),
    ],
    server: {
    host: '192.168.1.23',
    port: 5173,
    cors: true,
    watch: {
      usePolling: true
    },
  }
});
