# --- Stage 1: Build Nuxt ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app/frontend
# Copy only the frontend folder
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# --- Stage 2: PHP 8.5 Backend ---
FROM php:8.5-fpm-alpine
WORKDIR /var/www/html

# System dependencies for Laravel
RUN apk add --no-cache \
    libpng-dev libzip-dev zip unzip git oniguruma-dev curl-dev icu-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip bcmath gd intl

# Copy the Laravel code from the root
COPY . .

# Copy Nuxt build output into Laravel's public directory
# Nuxt usually outputs to .output/public
COPY --from=frontend-builder /app/frontend/.output/public ./public/dist

# Composer setup
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]