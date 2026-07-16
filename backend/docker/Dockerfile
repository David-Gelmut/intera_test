FROM php:8.4-fpm-alpine

# Установка системных пакетов, Nginx и Chromium для парсинга
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    nginx \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont\
    tesseract-ocr \
    tesseract-ocr-data-rus  \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers

# Настройка расширения GD перед установкой
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Установка PHP расширений
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    dom \
    sockets

COPY redis.tgz /tmp/redis.tgz

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install /tmp/redis.tgz \
    && rm /tmp/redis.tgz \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#     && curl -fsSLk https://pecl.php.net/get/redis -o redis.tgz \
#     && pecl install redis.tgz \
#     && rm redis.tgz \
#     && docker-php-ext-enable redis \
#     && apk del .build-deps
#
# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#     && curl -fsSLk https://pecl.php.net/get/redis -o redis.tgz \
#     && pecl install redis.tgz \
#     && rm redis.tgz \
#     && docker-php-ext-enable redis \
#     && apk del .build-deps
#
# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS coreutils ca-certificates \
#     && update-ca-certificates \
#     && mkdir -p /usr/src/php/ext/redis \
#     && curl -fsSL https://pecl.php.net/get/redis | tar xvz -C /usr/src/php/ext/redis --strip-components=1 \
#     && docker-php-ext-install redis \
#     && apk del .build-deps

# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#     && pecl install redis \
#     && docker-php-ext-enable redis \
#     && apk del .build-deps

# RUN apk add --no-cache --virtual .build-deps \
#         $PHPIZE_DEPS \
#         coreutils \
#     && mkdir -p /usr/src/php/ext/redis \
#     && curl -fsSL https://pecl.php.net/get/redis | tar xvz -C /usr/src/php/ext/redis --strip-components=1 \
#     && docker-php-ext-install redis \
#     && apk del .build-deps

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www

# Копируем исходный код проекта (включая локальный vendor)
COPY . .

#RUN composer install --no-dev --optimize-autoloader --no-scripts

# Создаем структуру папок Laravel и выставляем безопасные права www-data
RUN mkdir -p /var/www/storage/framework/cache/data \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Настройка прав для Nginx и Chromium во временных папках Alpine
RUN chmod 777 /tmp \
    && chown -R www-data:www-data /tmp \
    && chown -R www-data:www-data /var/lib/nginx \
    && chown -R www-data:www-data /var/log/nginx

# Настройка Nginx
RUN rm -rf /etc/nginx/http.d/default.conf && rm -rf /etc/nginx/nginx.conf
COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# Стартуем от root: php-fpm и nginx сами сбросят права до www-data после старта
CMD php-fpm -D && nginx -g "daemon off;"
