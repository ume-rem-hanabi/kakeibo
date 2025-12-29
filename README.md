# 家計簿システム - 開発環境セットアップ

## 構成
- **フロントエンド**: Vue 3 + TypeScript + Vuetify
- **バックエンド**: Laravel 11.x (最新版)
- **データベース**: MySQL 8.0
- **キャッシュ/セッション**: Redis 7
- **コンテナ管理**: Docker + Docker Compose

## ディレクトリ構造
```
.
├── front/              # Vue3フロントエンド
├── backend/            # Laravel API
├── docker/             # Docker関連ファイル
│   ├── docker-compose.yml
│   ├── backend/
│   │   └── Dockerfile
│   ├── frontend/
│   │   └── Dockerfile
│   └── mysql/
│       └── my.cnf
└── README.md
```

## 初回セットアップ手順

### 1. Laravelプロジェクトの作成
```bash
# backendディレクトリに移動
cd backend

# Laravelの最新版をインストール
composer create-project --prefer-dist laravel/laravel .

# .envファイルを編集（以下の設定を反映）
cp .env.example .env
```

**.env の設定例**:
```env
APP_NAME="家計簿API"
APP_ENV=local
APP_KEY=base64:... # php artisan key:generate で自動生成
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=kakeibo
DB_USERNAME=kakeibo_user
DB_PASSWORD=kakeibo_pass

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Vue3プロジェクトの作成
```bash
# frontディレクトリに移動
cd front

# Viteを使ってVue3プロジェクト作成
npm create vite@latest . -- --template vue-ts

# Vuetifyのインストール
npm install vuetify @mdi/font
npm install -D vite-plugin-vuetify

# Axiosのインストール（API通信用）
npm install axios

# Vue Routerのインストール
npm install vue-router@4

# Piniaのインストール（状態管理）
npm install pinia
```

### 3. Docker環境の起動
```bash
# dockerディレクトリに移動
cd docker

# コンテナのビルドと起動
docker-compose up -d --build

# ログの確認
docker-compose logs -f
```

### 4. Laravel初期設定（コンテナ起動後）
```bash
# バックエンドコンテナに入る
docker exec -it kakeibo_backend bash

# 依存関係のインストール（初回のみ）
composer install

# アプリケーションキーの生成
php artisan key:generate

# マイグレーション実行
php artisan migrate

# シーダー実行（必要に応じて）
php artisan db:seed

# ストレージリンク作成
php artisan storage:link
```

## アクセスURL
- **フロントエンド**: http://localhost:3000
- **バックエンドAPI**: http://localhost:8000/api
- **phpMyAdmin**: http://localhost:8080

## よく使うコマンド

### Docker操作
```bash
# コンテナ起動
docker-compose up -d

# コンテナ停止
docker-compose down

# コンテナ再起動
docker-compose restart

# ログ確認
docker-compose logs -f [service名]

# コンテナに入る
docker exec -it kakeibo_backend bash
docker exec -it kakeibo_frontend sh
```

### Laravel操作
```bash
# マイグレーション
php artisan migrate

# マイグレーションのロールバック
php artisan migrate:rollback

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# コントローラー作成
php artisan make:controller Api/TransactionController --api

# モデル作成（マイグレーション付き）
php artisan make:model Transaction -m

# シーダー作成
php artisan make:seeder TransactionSeeder
```

### フロントエンド操作
```bash
# 開発サーバー起動
npm run dev

# ビルド
npm run build

# プレビュー
npm run preview

# 依存関係の追加
npm install [package名]
```

## Redisについて（xserver運用時の注意）

### Redisが使える場合
現在の設定のままでOKです。

### Redisが使えない場合
`.env`ファイルを以下のように変更してください：

```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

また、`docker-compose.yml`から`redis`サービスを削除または無効化してください。

## トラブルシューティング

### ポートが既に使用されている
```bash
# 使用中のポートを確認
lsof -i :3000  # フロントエンド
lsof -i :8000  # バックエンド
lsof -i :3306  # MySQL
```

### パーミッションエラー
```bash
# バックエンドコンテナ内で実行
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### MySQLに接続できない
```bash
# MySQLコンテナのログを確認
docker-compose logs mysql

# MySQLコンテナに入って確認
docker exec -it kakeibo_mysql mysql -u kakeibo_user -p
```

## 次のステップ
1. 認証機能の実装（Laravel Sanctum）
2. 基本的なAPI設計（収支記録のCRUD）
3. フロントエンドの画面設計
4. データベース設計の詳細化

---

開発頑張ってください！ 🚀
