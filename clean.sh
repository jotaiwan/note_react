#!/bin/bash
# clean.sh - 彻底清理开发环境

echo "=== 停止可能占用端口 ==="
fuser -k 8000/tcp || true  # 后端端口
fuser -k 5173/tcp || true  # 前端端口

echo "=== 清理前端依赖和打包文件 ==="
cd frontend || exit
rm -rf node_modules package-lock.json dist
npm cache clean --force

echo "=== 清理根目录遗留（如果存在） ==="
cd ../
rm -rf node_modules package-lock.json dist || true

echo "✅ 清理完成"
