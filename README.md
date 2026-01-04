# Note System

This project is a note management system built with a React frontend and a Symfony backend. The React frontend runs as a Single Page Application (SPA), meaning most user interface logic, routing, and rendering happen in the browser (Client-Side Rendering, CSR). It uses ES modules for modern JavaScript imports and modularity. The backend provides APIs for creating, reading, updating, and deleting notes, as well as interacting with external services.

這是一個筆記管理系統，前端使用React，後端使用Symfony。React 前端以單頁應用（SPA）形式運行，大部分用戶界面邏輯、路由和渲染都在瀏覽器中完成（客戶端渲染，CSR）。前端使用ES 模塊來進行現代 JavaScript 導入與模塊化。後端負責提供創建、讀取、更新和刪除筆記的 API，以及與外部服務交互。

--- 

# Project Structure

The frontend (frontend/) contains all React-related code: components, services, assets, and configuration files like package.json and vite.config.js. React builds and serves a client-side rendered SPA, using ES modules for modular JavaScript code. The backend mainly serves APIs and optionally the main index.html of the SPA. All dynamic page updates are handled in the browser without page reloads.

前端（frontend/）包含所有 React 相關代碼：組件（components）、服務（services）、資源文件（assets），以及配置文件如 package.json 和 vite.config.js。React 打包並提供客戶端渲染 SPA，前端 JavaScript 代碼使用ES 模塊實現模塊化。後端主要提供 API 接口，並可選擇性地提供 SPA 的 index.html。所有動態頁面更新都在瀏覽器中完成，不會刷新整個頁面。

The backend (src/, config/, public/) is mainly Symfony. It handles API endpoints, business logic, and data storage. The backend does not render React pages using Twig or server-side templates—it only returns JSON responses for the frontend to consume.

後端（src/、config/、public/）主要使用 Symfony。它負責提供API 接口、業務邏輯和數據存儲。後端不使用 Twig 或伺服器端模板渲染 React 頁面，僅返回 JSON 數據供前端使用。

---

# Environment Variables

Environment variables are set in ~/.bashrc or .env files. Symfony’s Kernel.php loads these variables and makes them available to backend services. The React app can also read some variables during build time (via Vite) if needed, but runtime data mostly comes from backend APIs.

環境變數設定在 ~/.bashrc 或 .env 文件中。Symfony 的 Kernel.php 會加載這些變數，使其可供後端服務使用。React 應用也可以在構建時（通過 Vite）讀取部分變數，但運行時數據主要來自後端 API。

---

# Frontend

The frontend/ folder contains all the client-side code of the application, built with React. It handles the user interface and client interactions, using ES modules to organize JavaScript files. This part of the project is a Single Page Application (SPA), meaning that most navigation and rendering happen in the browser without requiring full page reloads. It also uses Client-Side Rendering (CSR), where the HTML is generated dynamically in the browser based on the React components.

The folder structure typically includes:

src/: Main source code for React components, services, styles, and assets.

public/: Static files that are served directly, such as the HTML entry point and icons.

package.json and package-lock.json: Node.js configuration and dependencies.

vite.config.js (or webpack): Build configuration for bundling and running the React app.

The frontend communicates with the backend via API requests (e.g., JSON responses) but handles most UI rendering entirely in the browser.


frontend/ 資料夾包含應用程式所有的客戶端程式碼，使用 React 開發。它負責使用者介面與前端互動，並使用 ES 模組 來組織 JavaScript 檔案。這個部分是 單頁應用程式 (SPA)，也就是大部分的頁面切換與渲染在瀏覽器內完成，而不需要整頁重新載入。同時採用 客戶端渲染 (CSR)，HTML 在瀏覽器中根據 React 元件動態生成。

資料夾結構通常包含：

src/：React 元件、服務、樣式與資源檔的主要原始程式碼。

public/：靜態檔案，例如 HTML 入口點與圖示，會直接被伺服器提供。

package.json 和 package-lock.json：Node.js 的專案設定與依賴。

vite.config.js（或 webpack.config.js）：前端打包與執行設定。

前端會透過 API（例如 JSON 回應）與後端通訊，但大部分的使用者介面渲染都是在瀏覽器端完成的。

The project is structured as follows:

### 文件夾結構 / Folder Structure

項目結構如下所示：

```
note_react/
├── config/
│   ├── packages/
│   │   ├── cache.yaml
│   │   ├── framework.yaml
│   │   ├── monolog.yaml
│   │   ├── nelmio_cors.yaml
│   │   ├── routing.yaml
│   │   └── security.yaml
│   └── routes/
│       ├── annotations.yaml
│       └── api.yaml
│   ├── NoteConstants.php
│   ├── bundles.php
│   ├── preload.php
│   ├── routes.yaml
│   └── services.yaml
├── data/
│   └── stock_funnhub.json
├── public/
│   └── index.php
├── src/
│   ├── Contract/
│   │   ├── NoteRequestStrategyInterface.php
│   │   ├── NoteServiceInterface.php
│   │   ├── ReadFileRepositoryInterface.php
│   │   ├── SaveFileRepositoryInterface.php
│   │   └── UpdateFileRepositoryInterface.php
│   ├── Controller/
│   │   └── Api/
│   │       ├── MenuApiController.php
│   │       └── NoteApiController.php
│   │   ├── EnvController.php
│   │   ├── HelloController.php
│   │   ├── NoteController.php
│   │   ├── ReadmeController.php
│   │   └── StockController.php
│   ├── CredentialReader/
│   │   └── CredentialReader.php
│   ├── DTO/
│   │   └── NoteDTO.php
│   ├── Entity/
│   │   └── Note.php
│   ├── Factory/
│   │   └── NoteFactory.php
│   ├── Mapping/
│   │   └── UrlMapping.php
│   ├── Repository/
│   │   ├── ReadFileRepository.php
│   │   ├── SaveFileRepository.php
│   │   └── UpdateFileRepository.php
│   ├── Service/
│   │   └── Base/
│   │       └── NoteBase.php
│   │   ├── HtmlHeadService.php
│   │   ├── MenuService.php
│   │   ├── NoteBuilderService.php
│   │   ├── ReadFileService.php
│   │   ├── SaveFileService.php
│   │   ├── StockService.php
│   │   └── UpdateFileService.php
│   ├── Strategy/
│   │   ├── ReadRequestStrategy.php
│   │   ├── SaveRequestStrategy.php
│   │   └── UpdateRequestStrategy.php
│   └── Util/
│       ├── DateTimeUtil.php
│       ├── EmojiUtil.php
│       ├── LoggerTrait.php
│       └── ProjectPaths.php
│   ├── Kernel.php
│   ├── index.js
│   └── xdebug_stub.php
├── frontend/
│   ├── public/
│   │   └── vite.svg
│   └── src/
│       ├── assets/
│       │   ├── helpers/
│       │   │   ├── clipboard.css
│       │   │   ├── clipboardHelper.js
│       │   │   ├── cookieHelper.js
│       │   │   ├── emojiHelper.js
│       │   │   ├── noteApi.js
│       │   │   └── noteHelper.js
│       │   ├── img/
│       │   │   ├── chatgpt-icon.png
│       │   │   ├── gitlab.png
│       │   │   ├── jenkins.png
│       │   │   ├── jira_cloud.png
│       │   │   ├── salesforce.png
│       │   │   ├── tool-home-page.png
│       │   │   ├── tripadvisor-4.png
│       │   │   ├── vault.png
│       │   │   ├── viator-3.png
│       │   │   ├── work-note.png
│       │   │   ├── work-note.svg
│       │   │   └── work-note2.png
│       │   └── note/
│       │       └── noteBuilder.css
│       │   └── react.svg
│       ├── components/
│       │   └── menu/
│       │       ├── Clipboard.jsx
│       │       ├── Credential.jsx
│       │       ├── EmojiSelector.jsx
│       │       ├── Environment.jsx
│       │       ├── Gitlab.jsx
│       │       ├── Jenkins.jsx
│       │       ├── Jira.jsx
│       │       ├── MenuIcons.jsx
│       │       ├── Salesforce.jsx
│       │       ├── TaWork.jsx
│       │       ├── Vault.jsx
│       │       ├── emojiSelector.css
│       │       ├── index.js
│       │       └── stock.css
│       │   ├── AddNewNoteForm.jsx
│       │   ├── Menu.jsx
│       │   ├── NoteBuilder.jsx
│       │   ├── NoteDate.jsx
│       │   ├── NoteEditable.jsx
│       │   ├── NoteForm.jsx
│       │   ├── NoteStatus.jsx
│       │   ├── Notes.jsx
│       │   └── Stock.jsx
│       ├── services/
│       │   ├── clipboardService.js
│       │   ├── credential.js
│       │   ├── noteService.js
│       │   ├── noteStatus.js
│       │   └── stock.js
│       └── styles/
│           └── global.css
│       ├── App.css
│       ├── App.jsx
│       ├── index.css
│       └── main.jsx
│   ├── .gitignore
│   ├── README.md
│   ├── eslint.config.js
│   ├── index.html
│   ├── package-lock.json
│   ├── package.json
│   └── vite.config.js
├── .env
├── .env.dev
├── .gitignore
├── LICENSE
├── README.html
├── README.md
├── clean.sh
├── compose.override.yaml
├── compose.yaml
├── composer.json
├── composer.lock
├── dev.sh
├── package.json.backup
├── phpunit.xml.dist
├── setup.sh
├── typescript
└── webpack.config.js.backup


```

### Key Folders and Files / 主要文件夾和文件

### 主要文件夾和文件 / Key Folders and Files

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

## Installation / 安裝

To get started with this project, follow the steps below.

要開始使用此專案，請按照以下步驟操作：

### Prerequisites / 先決條件

Make sure you have the following installed:

- PHP (8.2 or higher)
- Composer
- A web server (e.g., Apache, Nginx)

### Steps to Install / 安裝步驟

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/note.git
   ```
2. In the cloned repository, find the setup.sh and run it 
   ```bash
   $ cd note
   $ ./setup.sh <your_host_name> 
   ```
3. Set up your web server to point to the public/ folder.
4. Open your browser and navigate to http://localhost/ to see the system in action.

## Configuration requirement / 設定

To run this site, some configuration is required.

### 1. `.bashrc` (Ubuntu system)

Add the following settings to your `.bashrc` file:

```bash
# apache default log
export APACHE_LOG_DIR=/var/log/apache2

# create base_name and try to setup everything under BASE_NAME folder
export BASE_NAME=<your_surname> (👉 can be empty if not need)

# set HOME_AND_BASE, eg 
# if BASE_NAME is empty, it will /home/<user>
# if BASE_NAME is NOT empty, it will be /home/<user>/${BASE_NAME}
export HOME_AND_BASE=${HOME}${BASE_NAME:+/$BASE_NAME}

# code parent directory
export HOME_CODES=${HOME_AND_BASE}/codes

# note file location
export HOME_NOTES=${HOME_AND_BASE}/notes
export NOTE_DATA_FILE=${HOME_NOTES}/{SITE}/note.txt (👉 for saved note)

# configuration files that keep privately, eg. credentials.json for any API key (as example)
export HOME_SITE_CONFIGS=${HOME_AND_BASE}/configs/sites
export NOTE_CREDENTIAL_JSON=${HOME_SITE_CONFIGS}/{SITE}/credential.json (👉 for API Keys)

# site
export NOTE_HOST=http://note.local
export NOTE_DOCKER_PORT=8078
```

---
## Install **Node.js / npm** for managing frontend assets (JS/CSS) via **Webpack Encore** in a Symfony project

Follow the steps below to set up the frontend environment on Ubuntu.

```bash
# 1. Update package list
sudo apt update

# 2. Install Node.js (LTS version) and npm
sudo apt install -y nodejs npm

# 3. Verify installation
node -v
npm -v

# 4. Navigate to your project root
cd /path/to/your/project

# 5. Initialize package.json (if not already present)
npm init -y

# 6. Clean npm cache (optional but recommended)
npm cache clean --force

# 7. Install dependencies listed in package.json
npm install

# Optional: clean install (recommended after branch switch or pull)
npm ci

# 8. Run development build (compile JS/CSS)
npm run dev

# 9. Watch mode (rebuild automatically on changes)
npm run watch

# 10. Production build (minified/optimized)
npm run build

---

## How to run the service
```bash
# 1. go to repo directory
cd note_react
# 2. start frontend and backend servcie
./dev.sh 


### README.md

Run the following commnad to convert README.md to README.html and view it in http://<your_host>/readme

```bash
$ pandoc README.md -o README.html --standalone
```
