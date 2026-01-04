// 1️⃣ 第三方样式（最先）
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

// 2️⃣ 第三方 JS（Bootstrap dropdown / modal）
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// 3️⃣ React
import React from 'react';
import ReactDOM from 'react-dom/client';

// 4️⃣ App
import App from './App';

// 5️⃣ 渲染
const rootElement = document.getElementById('root');
const root = ReactDOM.createRoot(rootElement);

root.render(
  // <React.StrictMode>
  <App />
  // </React.StrictMode>
);
