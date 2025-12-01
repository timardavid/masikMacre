import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  base: '/Palyafoglalo/Frontend/',
  server: {
    port: 3000,
    host: true,
    strictPort: false,
    hmr: {
      protocol: 'ws',
      host: 'localhost'
    },
    proxy: {
      '/Palyafoglalo/Bakcend': {
        target: 'http://localhost',
        changeOrigin: true,
        secure: false,
      }
    }
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false,
  }
})

