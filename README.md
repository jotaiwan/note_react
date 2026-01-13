# 📝 Note System

A Note Management System built with a React (SPA) frontend and a Symfony backend API.

Frontend: React + Vite, Client-Side Rendering (CSR)

Backend: Symfony, REST-style JSON APIs

Architecture: Frontend and backend are clearly separated

This project is designed to demonstrate a modern SPA architecture where the backend acts purely as an API provider.

--- 

# 📌 Project Overview

The frontend is a Single Page Application (SPA) built with React.
All routing, UI rendering, and user interaction logic are handled in the browser (Client-Side Rendering, CSR).
The frontend communicates with the backend exclusively via HTTP APIs (JSON).

The backend is built with Symfony and provides:
- CRUD APIs for notes
- Integration with external services (e.g. stock data, credentials)
- File-based persistence (notes stored as files)

The backend does not render React pages using Twig.
It only returns JSON responses.

中文說明
這是一個使用 React + Symfony 的筆記管理系統。
- React 前端是 單頁應用程式（SPA）
- 頁面切換、渲染與互動全部在瀏覽器中完成（CSR）
- Symfony 後端只負責提供 API（JSON）
- 後端不使用 Twig 來渲染 React 頁面

--- 

# 🏗 Architecture
Browser (React SPA)
        |
        | HTTP (JSON)
        v
Symfony Backend (API only)
        |
        v
File system / External APIs

---

# 📁 Project Structure
note_react/
├── config/                 # Symfony configuration
├── public/                 # Symfony public entry (index.php)
├── src/                    # Symfony application source code
├── frontend/               # React SPA (Vite)
├── data/                   # Runtime data (e.g. stock JSON)
├── .env / .env.dev         # Environment variables
├── compose.yaml            # Docker (optional)
├── dev.sh                  # Start dev environment
├── setup.sh                # Initial setup script
└── README.md

# 🔍 Backend (Symfony)
Key Directories
Folder           Description
config/          Symfony configuration (packages, routes, services)
src/Controller/  API controllers (Notes, Menu, Stock, etc.)    
src/Service/     Business logic (note building, file handling)
src/Repository/  File-based persistence (read/save/update notes)
src/DTO/         Data Transfer Objects
src/Strategy/    Request strategy pattern (read/save/update)
src/Util/        Utility helpers (datetime, emoji, logging)

Important Config Files
- config/services.yaml – service dependency injection
- config/routes/api.yaml – API routing
- config/packages/nelmio_cors.yaml – CORS for React frontend

---

# 🎨 Frontend (React + Vite)

The frontend lives entirely in the frontend/ directory.
## Key Points
- Built with React
- Bundled by Vite
- Uses ES Modules
- Fully Client-Side Rendered (CSR)

## Frontend Structure
```text
frontend/
├── public/                 # Static assets
├── src/
│   ├── components/         # React components
│   ├── services/           # API calls
│   ├── assets/             # Images, helpers, CSS
│   ├── styles/             # Global styles
│   ├── App.jsx
│   └── main.jsx
├── package.json
├── vite.config.js
└── index.html
```

The frontend communicates with the backend via API calls such as:
```js
GET    /api/notes
POST   /api/note
PUT    /api/note
```

# ⚙️ Environment & Configuration

This project relies heavily on environment variables to define file locations and credentials.

## 1️⃣ Backend Environment Variables
Environment variables can be defined in:
- ~/.bashrc (recommended for local dev)
- .env / .env.dev (Symfony)

Example .bashrc Configuration (Ubuntu)
```bash
# Apache log directory
export APACHE_LOG_DIR=/var/log/apache2

# Optional base folder (can be empty)
export BASE_NAME=<your_surname>

# Home base resolution
export HOME_AND_BASE=${HOME}${BASE_NAME:+/$BASE_NAME}

# Project parent directory
export HOME_CODES=${HOME_AND_BASE}/codes

# Notes storage
export HOME_NOTES=${HOME_AND_BASE}/notes
export NOTE_DATA_FILE=${HOME_NOTES}/{SITE}/note.txt

# Private configuration (API keys, credentials)
export HOME_SITE_CONFIGS=${HOME_AND_BASE}/configs/sites
export NOTE_CREDENTIAL_JSON=${HOME_SITE_CONFIGS}/{SITE}/credential.json

# Site configuration
export NOTE_HOST=http://note.local
export NOTE_DOCKER_PORT=8078
```
## 📌 Why this matters
- Notes are stored as files (not a database)
- API credentials are kept outside the repository
- Multiple sites/environments can coexist safely

---

# 📦 Installation
Prerequisites
- PHP 8.2+
- Composer
- Node.js (LTS)
- npm
- Apache or Nginx

---

# Backend Setup
```bash
git clone https://github.com/yourusername/note.git
cd note
./setup.sh <your_host_name>
```

## Configure your web server to point to:
```text
/public
``` 

---

Frontend Setup (React)
cd frontend

```bash
# Install dependencies
npm install

# Development mode
npm run dev

# Production build
npm run build
```

---

# ▶️ Run the Project
```bash
cd note_react
./dev.sh
```

## This will start:
- Symfony backend
- React frontend (Vite dev server)

---

## ⚙️ Run with Apache (local host) ✅
If you prefer to use your system Apache (useful for production-like testing), follow these steps:

1. Install PHP and required extensions (PHP 8.2+), Composer and Node.js.
2. Install PHP dependencies:

```bash
composer install --no-interaction --optimize-autoloader
```

3. Install frontend dependencies and build (or run dev):

```bash
cd frontend
npm install
# for dev
npm run dev
# for production
npm run build
cd ..
```

4. Configure Apache VirtualHost (example):

```apacheconf
<VirtualHost *:80>
    ServerName note.local
    DocumentRoot /absolute/path/to/note_react/public

    <Directory /absolute/path/to/note_react/public>
        Require all granted
        AllowOverride All
        Options Indexes FollowSymLinks
    </Directory>

    # Pass environment variables (alternatively set in your shell)
    SetEnv NOTE_DOCKER_PORT 8078
    SetEnv NOTE_HOST http://note.local

    ErrorLog ${APACHE_LOG_DIR}/note_error.log
    CustomLog ${APACHE_LOG_DIR}/note_access.log combined
</VirtualHost>
```

- Enable mod_rewrite and restart Apache:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

- Ensure writable directories and data file permissions (example):

```bash
mkdir -p "$HOME_NOTES" # if using env variables like README describes
chown -R www-data:www-data var/ data/ # or your apache user
chmod -R 775 var/ data/
```

> Note: Symfony entry point is `public/index.php` so the Apache DocumentRoot must point to `public`.

---

## 🐳 Run with Docker (Apache) ✅
There is Docker support for development in `docker/`. The default `docker/docker-compose.yml` runs a PHP built-in backend and a Vite frontend for development:

```bash
# development (backend uses PHP built-in server + frontend Vite)
docker-compose -f docker/docker-compose.yml up --build
```

If you prefer an Apache container instead, create a small `docker-compose.apache.yml` (example):

```yaml
version: '3.8'
services:
  apache:
    image: php:8.2-apache
    volumes:
      - ./:/var/www/note_react:cached
    working_dir: /var/www/note_react
    ports:
      - "8080:80"
    environment:
      NOTE_HOST: http://localhost:8080
    command: bash -lc "docker-php-ext-install pdo pdo_mysql && apache2-foreground"
```

Then run:

```bash
docker-compose -f docker-compose.apache.yml up --build
```

- The container serves the app at `http://localhost:8080` (DocumentRoot is `/var/www/note_react/public`).
- Make sure environment variables expected by the app (credentials, NOTE_DATA_FILE, etc.) are provided as `environment:` or mounted files.

---



---

# 🗒 Extra Notes – Folder Description（補充資料夾說明）

- **`config/`**：包含配置文件，包括包、路由和服務的設置 / Contains configuration files, including settings for packages, routes, and services
  - **`packages/`**：配置各種包，如緩存、調試和郵件服務 / Configuration for various packages such as cache, debug, and mailer
  - **`routes/`**：定義路由配置 / Defines routing configuration
  - **`services.yaml`**：定義系統使用的各種服務 / Defines various services used by the system

- **`data/`**：包含與項目相關的日誌和數據 / Contains logs and other data related to the project
  - **`logs/`**：存儲日誌文件，如從 API 獲取的股票數據 / Stores log files like stock data retrieved from APIs

- **`frontend/`** : 資料夾包含應用程式所有的客戶端程式碼，使用 React 開發。它負責使用者介面與前端互動，並使用 ES 模組 來組織 JavaScript 檔案。這個部分是 單頁應用程式 (SPA)，也就是大部分的頁面切換與渲染在瀏覽器內完成，而不需要整頁重新載入。同時採用 客戶端渲染 (CSR)，HTML 在瀏覽器中根據 React 元件動態生成。
  - **`src/`**： will update later
    - **`public/`**：包含所有公開文件，如圖像、JavaScript 和 CSS 文件 /

- **`public/`**：包含所有公開文件，如圖像、JavaScript 和 CSS 文件 / Holds all public-facing files, including assets like images, JavaScript, and CSS files

- **`src/`**：項目的源代碼 / The source code for the project
  - **`Controller/`**：處理 HTTP 請求，例如筆記管理或股票數據檢索 / Handles HTTP requests, such as note management or stock data retrieval
  - **`Service/`**：包含用於各種功能的服務，如筆記創建和文件處理 / Contains services for various functionalities like note creation and file handling
  - **`Repository/`**：管理數據持久化，包括讀取和保存文件 / Manages data persistence, including reading and saving files
  - **`DTO/`**：數據傳輸對象，用於在各層之間結構化數據 / Data Transfer Objects for structuring data between layers
  - **`Util/`**：實用工具類別，用於處理日誌或日期操作等任務 / Utility classes for tasks such as logging or date manipulation

- **其他文件 / Other files**:
  - **`composer.json`**：PHP 包的 Composer 依賴文件 / Composer dependency file for PHP packages

---

> ℹ️ **Note To Myself**
> Run the following commnad to convert README.md to README.html and view it in http://<your_host>/readme
```bash
$ pandoc README.md -o README.html --standalone
```

