# --- Stage 1: Build Nuxt ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app/frontend
# Copy only the frontend folder
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
RUN npx nuxi generate

# --- Stage 2: PHP 8.5 Backend ---
FROM php:8.5-fpm-alpine
WORKDIR /var/www/html

RUN apk update && apk upgrade --no-cache

# System dependencies for Laravel
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql redis mbstring zip bcmath gd intl opcache

# Copy the Laravel code from the root
COPY app/ ./app
COPY bootstrap/ ./bootstrap
COPY config/ ./config
COPY database/ ./database
COPY public/ ./public
COPY routes/ ./routes
COPY resources/ ./resources
COPY artisan .
COPY composer.json .

# Create cache directory for Laravel
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/logs \
             bootstrap/cache

# Composer setup
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Copy Nuxt build output into Laravel's public directory
# Nuxt usually outputs to .output/public
COPY --from=frontend-builder /app/frontend/.output/public ./public

# Permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]