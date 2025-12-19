import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'

const inputs = {
    'resources/admin/bootstrap/app.js': 'resources/admin/bootstrap/app.js',
}

export default defineConfig({
    plugins: [vue()],
    
    css: {
        postcss: {
            plugins: [
                tailwindcss(),
                autoprefixer(),
            ],
        },
    },

    build: {
        manifest: true,
        outDir: 'assets',
        assetsDir: '.',
        emptyOutDir: true,
        rollupOptions: {
            input: inputs,
            output: {
                chunkFileNames: '[name].js',
                entryFileNames: '[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'app.css') return 'app.css';
                    return '[name].[ext]';
                }
            },
        },
    },

    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, 'resources/admin'),
        },
    },

    server: {
        port: 5173,
        strictPort: true,
        host: 'localhost',
        hmr: {
            port: 5173,
            host: 'localhost',
        },
        cors: {
            origin: true,
            credentials: true,
        },
        headers: {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
            'Access-Control-Allow-Headers': 'X-Requested-With, content-type, Authorization'
        }
    },

    optimizeDeps: {
        include: ['vue', 'atomico'],
    },
})

