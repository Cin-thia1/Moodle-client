# =========================================================
# Dockerfile - Moodle Client (Laravel 11)
# Build en 3 étapes pour garder l'image finale légère
# =========================================================

# ---------- Etape 1 : dépendances PHP (composer) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader

# ---------- Etape 2 : build des assets front (Vite) ----------
# Si ton projet n'utilise pas Node/Vite, tu peux supprimer cette étape
# et la ligne "COPY --from=frontend" plus bas.
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ---------- Etape 3 : image finale PHP-FPM ----------
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
    bash \
    curl \
    mysql-client \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    icu-dev \
    zip \
    unzip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

WORKDIR /var/www/html

# Code de l'application
COPY . .

# Dépendances PHP compilées à l'étape 1
COPY --from=vendor /app/vendor ./vendor

# Assets front compilés à l'étape 2
COPY --from=frontend /app/public/build ./public/build

# Script de démarrage (migrations, clé d'app, attente DB...)
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
