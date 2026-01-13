import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path'; // ← 必须加

export default defineConfig({
  plugins: [react()], // ← 保留一个
  assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf'],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'), // ← 添加 @ 别名
    },
  },

  server: (() => {
    const isDocker = (process.env.BACKEND_URL && process.env.BACKEND_URL.includes('backend')) || process.env.VITE_IN_DOCKER === '1';
    return {
      host: isDocker ? '0.0.0.0' : (process.env.VITE_HOST || 'note_react.local'),
      allowedHosts: ['note_react.local', 'localhost', 'note_react.docker'],
      port: Number(process.env.VITE_PORT || 5173),
      open: !isDocker,
      hmr: {
        host: process.env.HMR_HOST || (isDocker ? 'localhost' : 'note_react.local') || 'host.docker.internal',
        port: Number(process.env.PORT || process.env.VITE_PORT) || (isDocker ? 5174 : 5173),
      },
      proxy: {
        '/api': {
          target: process.env.BACKEND_URL || 'http://localhost:8000' || 'http://localhost:8001',
          changeOrigin: true,
          secure: false,
        },
      },
    };
  })(),
});
