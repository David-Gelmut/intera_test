import { defineConfig } from 'vite';
//import laravel from 'laravel-vite-plugin';
//import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
       /* laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),*/
        /*vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),*/
        vue(),
        tailwindcss(),

    ],
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,

            },
            '/sanctum': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        }
        /*watch: {
            ignored: ['**!/storage/framework/views/!**'],
        },*/
    },
});
