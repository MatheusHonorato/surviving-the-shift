import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import vueDevTools from 'vite-plugin-vue-devtools'

/**
 * Vite configuration
 * https://vite.dev/config/
 */
export default defineConfig({
  plugins: [
    vue({
      // Enable reactivity transform for better performance
      reactivityTransform: false
    }),
    // Only enable devtools in development
    ...(process.env.NODE_ENV === 'development' ? [vueDevTools()] : []),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  // Build optimizations
  build: {
    target: 'esnext',
    minify: 'esbuild',
    sourcemap: false,
    rollupOptions: {
      output: {
        manualChunks: {
          'vue-vendor': ['vue', 'vue-router', 'pinia'],
          'axios-vendor': ['axios']
        }
      }
    }
  },
  // Server configuration
  server: {
    port: 5173,
    strictPort: false,
    open: false
  },
  // Preview configuration
  preview: {
    port: 4173,
    strictPort: false
  }
})
