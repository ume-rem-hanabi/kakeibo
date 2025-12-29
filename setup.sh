#!/bin/bash

echo "=========================================="
echo "家計簿システム - 完全Docker版セットアップ"
echo "ホストマシンにComposer/Node.jsは不要！"
echo "=========================================="
echo ""

# カラー定義
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Dockerの確認
echo -e "${YELLOW}Dockerの動作を確認しています...${NC}"
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker が起動していません${NC}"
    echo "Docker Desktop を起動してから再度実行してください"
    exit 1
fi
echo -e "${GREEN}✓ Docker は起動しています${NC}"
echo ""

# ステップ1: MySQLとRedisだけ先に起動
echo -e "${YELLOW}[1/5] データベースサーバーを起動します...${NC}"
cd docker
docker-compose up -d mysql redis
echo -e "${GREEN}✓ MySQL と Redis の起動完了${NC}"
echo ""

# MySQLの起動を待つ
echo -e "${YELLOW}MySQLの起動を待っています...${NC}"
sleep 10
echo -e "${GREEN}✓ MySQL 起動完了${NC}"
echo ""

cd ..

# ステップ2: Dockerコンテナ内でLaravelをbackend直下に作成
echo -e "${YELLOW}[2/5] Laravelプロジェクトを作成します（Dockerコンテナ内）...${NC}"

if [ ! -f "backend/artisan" ]; then
    # backend/.env.dockerがあれば一時退避
    if [ -f "backend/.env.docker" ]; then
        mv backend/.env.docker /tmp/.env.docker.bak
    fi
    
    # backendディレクトリをクリーン
    rm -rf backend/*
    rm -rf backend/.* 2>/dev/null || true
    
    # Composerコンテナで Laravel を backend 直下に作成
    docker run --rm \
        -v $(pwd):/work \
        -w /work \
        composer:latest \
        composer create-project laravel/laravel backend --prefer-dist
    
    # .env.dockerを戻す
    if [ -f "/tmp/.env.docker.bak" ]; then
        mv /tmp/.env.docker.bak backend/.env.docker
    fi
    
    echo -e "${GREEN}✓ Laravel プロジェクトの作成完了${NC}"
else
    echo -e "${GREEN}✓ Laravel プロジェクトは既に存在します${NC}"
fi
echo ""

# ステップ3: Dockerコンテナ内でVue3をfront直下に作成
echo -e "${YELLOW}[3/5] Vue3プロジェクトを作成します（Dockerコンテナ内）...${NC}"

if [ ! -f "front/package.json" ]; then
    # front/.env.developmentがあれば一時退避
    if [ -f "front/.env.development" ]; then
        mv front/.env.development /tmp/.env.development.bak
    fi
    
    # frontディレクトリをクリーン
    rm -rf front/*
    rm -rf front/.* 2>/dev/null || true
    
    # Node.jsコンテナで Vue3 を front 直下に作成
    docker run --rm \
        -v $(pwd)/front:/app \
        -w /app \
        node:20-alpine \
        sh -c "npm create vite@latest . -- --template vue-ts && npm install && npm install vuetify @mdi/font vite-plugin-vuetify axios vue-router@4 pinia"
    
    # .env.developmentを戻す
    if [ -f "/tmp/.env.development.bak" ]; then
        mv /tmp/.env.development.bak front/.env.development
    fi
    
    echo -e "${GREEN}✓ Vue3 プロジェクトの作成完了${NC}"
else
    echo -e "${GREEN}✓ Vue3 プロジェクトは既に存在します${NC}"
fi
echo ""

# ステップ4: .envファイルの設定
echo -e "${YELLOW}[4/5] 環境設定ファイルを確認します...${NC}"
if [ ! -f "backend/.env" ]; then
    if [ -f "backend/.env.example" ]; then
        cp backend/.env.example backend/.env
        echo -e "${GREEN}✓ backend/.env を作成しました${NC}"
    fi
fi

if [ ! -f "front/.env" ]; then
    if [ -f "front/.env.development" ]; then
        cp front/.env.development front/.env
        echo -e "${GREEN}✓ front/.env を作成しました${NC}"
    fi
fi
echo ""

# ステップ5: 全コンテナを起動
echo -e "${YELLOW}[5/5] 全てのコンテナを起動します...${NC}"
cd docker
docker-compose up -d --build
cd ..

echo ""
echo -e "${GREEN}=========================================="
echo "セットアップ完了！"
echo "==========================================${NC}"
echo ""
echo "次のステップ:"
echo "1. backend/.env ファイルの設定を確認・編集"
echo ""
echo "2. Laravelの初期化（Dockerコンテナ内で実行）:"
echo "   docker exec -it kakeibo_backend composer install"
echo "   docker exec -it kakeibo_backend php artisan key:generate"
echo "   docker exec -it kakeibo_backend php artisan migrate"
echo ""
echo "アクセスURL:"
echo "  - フロントエンド: http://localhost:3000"
echo "  - バックエンドAPI: http://localhost:8000"
echo "  - phpMyAdmin: http://localhost:8080"
echo ""
echo "コンテナ確認:"
echo "  docker-compose -f docker/docker-compose.yml ps"
echo ""
echo "ログ確認:"
echo "  docker-compose -f docker/docker-compose.yml logs -f"
echo ""