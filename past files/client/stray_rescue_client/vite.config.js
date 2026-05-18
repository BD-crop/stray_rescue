import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    port: 3000, // optional, default is 5173
    proxy: {
      // Proxy /api requests to your PHP backend
      '/api': {
        target: 'http://localhost:80',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/api/, ''), // remove /api prefix
      },
    },
  },
})