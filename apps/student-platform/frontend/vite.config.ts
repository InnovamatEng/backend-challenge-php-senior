import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    proxy: {
      '/platform/api': {
        target: 'http://platform_nginx:80',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/platform/, ''),
      },
      '/reporting/api': {
        target: 'http://reporting_nginx:80',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/reporting\/api/, ''),
      },
    }
  }
})
