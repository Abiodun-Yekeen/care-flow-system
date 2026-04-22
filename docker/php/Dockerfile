# syntax=docker/dockerfile:1.7

# --- STAGE 1: Base PHP ---
FROM php:8.4-fpm-alpine AS base
WORKDIR /var/www

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql mbstring intl gd bcmath zip opcache exif pcntl redis

RUN apk add --no-cache bash git curl zip unzip

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# --------------------------------------------------

# --- STAGE 2: PHP Dependencies ---
FROM composer:2 AS vendor
WORKDIR /app

COPY src/composer.json src/composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --ignore-platform-reqs

# --------------------------------------------------

# --- STAGE 3: Frontend Build (Vite) ---
FROM node:20-alpine AS frontend
WORKDIR /app

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME

ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

COPY src/package.json src/package-lock.json ./
RUN npm ci

COPY src/ ./
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

# --------------------------------------------------

# --- TARGET: DEVELOPMENT ---
FROM base AS development

RUN install-php-extensions xdebug

RUN echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/app-dev.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/app-dev.ini

CMD ["php-fpm", "-F"]

# --------------------------------------------------

# --- TARGET: PRODUCTION ---
FROM base AS production

# Disable opcache revalidation (performance)
RUN echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/app-prod.ini

# Copy app source
COPY src/ /var/www/

# Copy vendor (PHP deps)
COPY --from=vendor /app/vendor /var/www/vendor

# Copy built frontend assets
COPY --from=frontend /app/public/build /var/www/public/build

# Optimize autoload
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev

# Permissions
RUN addgroup -g 1000 app && adduser -G app -u 1000 -D app \
    && chown -R app:app /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER app

EXPOSE 9000
CMD ["php-fpm", "-F"]




## --- STAGE 1: Base Environment (The Slow Part - Cached) ---
#FROM php:8.4-fpm-alpine AS base
#WORKDIR /var/www
#
#COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
#RUN install-php-extensions pdo_pgsql mbstring intl gd bcmath zip opcache exif pcntl redis
#RUN apk add --no-cache bash git curl zip unzip
#COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
#
#
## --- STAGE 2: PHP Dependencies ---
#FROM composer:2 AS vendor-extra
#WORKDIR /app
#COPY src/composer.json src/composer.lock ./
#RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --ignore-platform-reqs
#
#
## --- TARGET: DEVELOPMENT ---
#FROM base AS development
#RUN install-php-extensions xdebug
#RUN echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/app-dev.ini \
#    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/app-dev.ini
#CMD ["php-fpm", "-F"]
#
#
## --- TARGET: PRODUCTION ---
#FROM base AS production
#RUN echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/app-prod.ini
#
## Copy the source code (This now includes your LOCAL public/build folder)
#COPY src/ /var/www/
#
## Copy the vendor folder from Stage 2
#COPY --from=vendor-extra /app/vendor /var/www/vendor
#
## Finalize PHP optimizations
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
#RUN composer dump-autoload --optimize
#
##Permissions
#RUN addgroup -g 1000 app && adduser -G app -u 1000 -D app \
#    && chown -R app:app /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
#
#USER app
#EXPOSE 9000
#CMD ["php-fpm", "-F"]
#



## syntax=docker/dockerfile:1.7
#
## --- STAGE 1: PHP Dependencies (Ziggy Source) ---
#FROM composer:2 AS vendor-extra
#WORKDIR /app
#COPY src/composer.json src/composer.lock ./
#RUN composer install \
#    --no-dev \
#    --no-interaction \
#    --prefer-dist \
#    --no-scripts \
#    --ignore-platform-reqs
#
## --- STAGE 2: Asset Builder (Vite/NPM) ---
#FROM node:20-alpine AS asset-builder
#WORKDIR /app
#COPY src/package.json src/package-lock.json ./
#RUN npm install
#COPY src/ ./
#COPY --from=vendor-extra /app/vendor ./vendor
#RUN npm run build
#
## --- STAGE 3: Final Production Image (High Speed) ---
#FROM php:8.4-fpm-alpine
#WORKDIR /var/www
#
## 1. Pull the high-speed extension installer
#COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
#
## 2. Install PHP extensions (Faster and cleaner than manual apk/compile)
#RUN install-php-extensions pdo_pgsql mbstring intl gd bcmath zip opcache exif pcntl redis
#
## 3. Install basic system utilities
#RUN apk add --no-cache bash git curl zip unzip
#
## 4. PHP Configuration
#COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
#
## --- FINAL ASSEMBLY ---
#COPY src/ /var/www/
#COPY --from=vendor-extra /app/vendor /var/www/vendor
#COPY --from=asset-builder /app/public/build /var/www/public/build
#
## Copy composer for optimized autoloading
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
#RUN composer dump-autoload --optimize
#
## Permissions
#RUN addgroup -g 1000 app \
#    && adduser -G app -u 1000 -D app \
#    && chown -R app:app /var/www \
#    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
#
#USER app
#EXPOSE 9000
#CMD ["php-fpm", "-F"]
#
















