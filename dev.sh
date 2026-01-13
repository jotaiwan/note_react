#!/bin/bash
# dev.sh - 一键启动 Symfony + Vite 前端开发环境
# 自动安装依赖，显示 Symfony 日志并高亮

set -e  # 遇到错误立即停止

# check which directory the script is running from
# 1. Get current folder name
dir_folder=$(basename "$(pwd)")
# 2. Export to SITE variable
export SITE="${dir_folder}"
# 3. Print
echo "🤔 Site name: $SITE"


BACKEND_PORT=8000
FRONTEND_PORT=5173

# --- 检查端口占用函数 ---
function is_port_in_use() {
    lsof -i:$1 >/dev/null 2>&1
}

# --- 停掉占用端口的 Symfony ---
if is_port_in_use $BACKEND_PORT; then
    echo "⚠️ 检测到端口 $BACKEND_PORT 被占用，尝试停止旧 Symfony..."
    symfony server:stop || true
    sleep 1
fi

# --- 停掉占用端口的 Vite ---
if is_port_in_use $FRONTEND_PORT; then
    echo "⚠️ 检测到端口 $FRONTEND_PORT 被占用，尝试杀掉进程..."
    fuser -k $FRONTEND_PORT/tcp || true
    sleep 1
fi

# --- 清理 frontend dist ---
echo "=== 清理 frontend dist ==="
cd frontend || exit
rm -rf dist

# --- 安装前端依赖 ---
echo "=== 检查并安装前端依赖 ==="
FRONTEND_DEPS=(axios bootstrap jquery @fortawesome/fontawesome-free react react-dom)
for dep in "${FRONTEND_DEPS[@]}"; do
    if ! npm list "$dep" >/dev/null 2>&1; then
        echo "📦 安装 $dep ..."
        npm install "$dep"
    fi
done

# 安装其他可能缺失的依赖
npm install

cd ../ || exit

# --- 启动 Symfony 后端 ---
echo "=== 启动 Symfony 后端 (http://localhost:8000) ==="
symfony server:start -d
sleep 2

# --- tail Symfony 日志并高亮 ---
echo "=== 显示 Symfony 日志 ==="
# 使用 ANSI 颜色：
# 红色：ERROR / CRITICAL
# 黄色：WARNING
# 绿色：INFO / HTTP请求
tail -f var/log/dev.log | \
    sed -E "s/(ERROR|CRITICAL)/\x1b[31m\1\x1b[0m/g; s/WARNING/\x1b[33m&\x1b[0m/g; s/INFO|GET|POST/\x1b[32m&\x1b[0m/g" &

# --- 启动 Vite 前端 ---
cd frontend || exit
echo "=== 启动 Vite 前端 (http://localhost:5173) ==="
npm run dev

echo "✅ 开发环境已启动"
