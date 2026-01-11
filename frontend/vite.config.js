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

  server: {
    host: "note_react.local",
    port: 5173,
    open: true,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
});
