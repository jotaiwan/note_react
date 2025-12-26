# Note System

This is a note management system designed to help users create, manage, and update their notes efficiently. The system includes features like note creation, reading, updating, and integration with external services.

這是一個筆記管理系統，旨在幫助用戶高效地創建、管理和更新筆記。該系統包括筆記創建、讀取、更新及與外部服務集成的功能。

## Project Structure / 項目結構

## Set up environment variables first.

The first step is to configure all related environment variables in the ~/.bashrc file. Then, use the Symfony Kernel.php to load them from the file and add them to the environment using the putenv method.


The project is structured as follows:

### 文件夾結構 / Folder Structure

項目結構如下所示：

```
note/
├── config/
│   ├── packages/
│   │   ├── cache.yaml
│   │   ├── debug.yaml
│   │   ├── doctrine.yaml
│   │   ├── doctrine_migrations.yaml
│   │   ├── framework.yaml
│   │   ├── mailer.yaml
│   │   ├── messenger.yaml
│   │   ├── monolog.yaml
│   │   ├── notifier.yaml
│   │   ├── routing.yaml
│   │   ├── security.yaml
│   │   ├── translation.yaml
│   │   ├── twig.yaml
│   │   ├── validator.yaml
│   │   └── web_profiler.yaml
│   └── routes/
│   |  ├── framework.yaml
│   |  └── web_profiler.yaml
│   ├── NoteConstants.php
│   ├── bundles.php
│   ├── preload.php
│   ├── routes.yaml
│   └── services.yaml  👉 add service for dependency injection
├── data/
│   └── logs/
│   ├── stock_alphavantage.json
│   └── stock_funnhub.json
├── public/
│   └── assets/
│   |   ├── img/
│   |   │   ├── chatgpt-icon.png
│   |   │   ├── jira_cloud.png
│   |   │   ├── tool-home-page.png
│   |   │   ├── tripadvisor-4.png
│   |   │   ├── viator-3.png
│   |   │   ├── work-note.png
│   |   │   ├── work-note.svg
│   |   │   └── work-note2.png
│   |   ├── lib/
│   |   │   ├── css/
│   |   │   │   ├── bootstrap.4.5.3.min.css
│   |   │   │   └── select2.4.1.0.min.css
│   |   │   └── js/
│   |   │       ├── bootstrap.bundle.4.5.3.min.js
│   |   │       ├── jquery-3.5.1.min.js
│   |   │       └── select2.4.1.0.min.js
│   |   └── note/
│   |       ├── noteBuilder.css
│   |       ├── noteBuilder.js
│   |       ├── noteEditable.js
│   |       ├── noteLinkFunctions.js
│   |       └── stockUpdate.js
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
│   │   ├── UpdateFileService.php
│   │   └── what_service_folder_for.txt
│   ├── Strategy/
│   │   ├── ReadRequestStrategy.php
│   │   ├── SaveRequestStrategy.php
│   │   └── UpdateRequestStrategy.php
│   └── Util/
│   |   ├── DateTimeUtil.php
│   │   ├── EmojiUtil.php
│   │   ├── LoggerTrait.php
│   │   └── ProjectPaths.php
│   └── Kernel.php
├── templates/
│   ├── env-test/
│   │   └── index.html.twig
│   ├── hello/
│   │   └── index.html.twig
│   └── note/
│   |   ├── head.html.twig
│   |   ├── index.html.twig
│   |   ├── menu.html.twig
│   |   ├── note.html.twig
│   |   └── stock.html.twig
│   └── base.html.twig
├── .gitignore
├── LICENSE
├── README.html
├── README.md
├── compose.override.yaml
├── compose.yaml
├── composer.json
├── composer.lock
├── phpunit.xml.dist
└── setup.sh

```

### Key Folders and Files / 主要文件夾和文件

### 主要文件夾和文件 / Key Folders and Files

- **`config/`**：包含配置文件，包括包、路由和服務的設置 / Contains configuration files, including settings for packages, routes, and services
  - **`packages/`**：配置各種包，如緩存、調試和郵件服務 / Configuration for various packages such as cache, debug, and mailer
  - **`routes/`**：定義路由配置 / Defines routing configuration
  - **`services.yaml`**：定義系統使用的各種服務 / Defines various services used by the system

- **`data/`**：包含與項目相關的日誌和數據 / Contains logs and other data related to the project
  - **`logs/`**：存儲日誌文件，如從 API 獲取的股票數據 / Stores log files like stock data retrieved from APIs

- **`public/`**：包含所有公開文件，如圖像、JavaScript 和 CSS 文件 / Holds all public-facing files, including assets like images, JavaScript, and CSS files
  - **`assets/`**：包含筆記構建器 UI 的圖像和腳本資源 / Contains image and script assets for the note builder UI

- **`src/`**：項目的源代碼 / The source code for the project
  - **`Controller/`**：處理 HTTP 請求，例如筆記管理或股票數據檢索 / Handles HTTP requests, such as note management or stock data retrieval
  - **`Service/`**：包含用於各種功能的服務，如筆記創建和文件處理 / Contains services for various functionalities like note creation and file handling
  - **`Repository/`**：管理數據持久化，包括讀取和保存文件 / Manages data persistence, including reading and saving files
  - **`DTO/`**：數據傳輸對象，用於在各層之間結構化數據 / Data Transfer Objects for structuring data between layers
  - **`Util/`**：實用工具類別，用於處理日誌或日期操作等任務 / Utility classes for tasks such as logging or date manipulation

- **`templates/`**：包含用於渲染前端視圖的 Twig 模板 / Contains Twig templates for rendering the frontend views
  - **`note/`**：與筆記管理系統相關的模板 / Templates specifically related to the note management system

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

# 1. Update package list
```
sudo apt update
```

# 2. Install Node.js (LTS version) and npm
```
sudo apt install -y nodejs npm
```

# 3. Verify installation
```
node -v
npm -v
```

# 4. Navigate to your project root
```
cd /path/to/your/project
```

# 5. Initialize package.json (if not already present)
```
npm init -y
```

# 6. Clean npm cache (optional but recommended)
```
npm cache clean --force
```

# 7. Install dependencies listed in package.json
```
npm install
```

# Optional: clean install (recommended after branch switch or pull)
```
npm ci
```

# 8. Run development build (compile JS/CSS)
```
npm run dev
```

# 9. Watch mode (rebuild automatically on changes)
```
npm run watch
```

# 10. Production build (minified/optimized)
```
npm run build
```


### README.md

Run the following commnad to convert README.md to README.html and view it in http://<your_host>/readme

```bash
$ pandoc README.md -o README.html --standalone
```
