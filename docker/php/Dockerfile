# ==============================================================================
# Stage 1: Node.js — Build frontend assets
# ==============================================================================
FROM node:20-alpine AS node-builder

WORKDIR /var/www/html

COPY package.json package-lock.json* ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js jsconfig.json* tailwind.config.js* postcss.config.js* ./
COPY components.json* ./

RUN npm run build

# ==============================================================================
# Stage 2: PHP — Install Composer dependencies
# ==============================================================================
FROM php:8.4-fpm-alpine AS php-builder

RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pcntl \
        bcmath \
        zip \
        gd \
        intl \
        mbstring \
        opcache

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

# ==============================================================================
# Stage 3: Production — Nginx + PHP-FPM + Supervisor
# ==============================================================================
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    oniguruma \
    curl

RUN apk add --no-cache --virtual .build-deps \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pcntl \
        bcmath \
        zip \
        gd \
        intl \
        mbstring \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

WORKDIR /var/www/html

# Copy application from php-builder
COPY --from=php-builder /var/www/html /var/www/html

# Copy compiled assets from node-builder
COPY --from=node-builder /var/www/html/public/build public/build

# Copy configuration files
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP production config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure OPcache
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.memory_consumption=128" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.interned_strings_buffer=8" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.max_accelerated_files=10000" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.validate_timestamps=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.revalidate_freq=0" >> "$PHP_INI_DIR/conf.d/opcache.ini"

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create log directory for supervisor
RUN mkdir -p /var/log/supervisor

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
