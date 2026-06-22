FROM php:8.4-fpm-alpine

# 1. Установка системных пакетов, Nginx и Chromium для парсинга
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

# 2. Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# 3. Настройка рабочей директории
WORKDIR /var/www

# 4. Копируем исходный код проекта (включая локальный vendor и public/build)
COPY . /var/www

# 5. Создаем структуру папок Laravel и выставляем безопасные права www-data
RUN mkdir -p /var/www/storage/framework/cache/data \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 6. Настройка прав для Nginx и Chromium во временных папках Alpine
RUN chmod 777 /tmp \
    && chown -R www-data:www-data /tmp \
    && chown -R www-data:www-data /var/lib/nginx \
    && chown -R www-data:www-data /var/log/nginx

# 7. Настройка Nginx
RUN rm -rf /etc/nginx/http.d/default.conf && rm -rf /etc/nginx/nginx.conf
COPY ./nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# Стартуем от root: php-fpm и nginx сами сбросят права до www-data после старта
CMD php-fpm -D && nginx -g "daemon off;"
