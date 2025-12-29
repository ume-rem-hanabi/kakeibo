FROM php:8.4-fpm

# 作業ディレクトリ設定
WORKDIR /var/www/html

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Redis拡張のインストール
RUN pecl install redis && docker-php-ext-enable redis

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js と npm のインストール（Laravel Mixなどで必要な場合）
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 権限設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ポート公開
EXPOSE 8000

# 起動コマンド
CMD ["sh", "-c", "composer install && php artisan key:generate && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
