# ==========================
# Base PHP image
# ==========================
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# ==========================
# Stage 1: Development
# ==========================
FROM base AS development

RUN apt-get install -y nodejs npm

CMD ["php-fpm"]

# ==========================
# Stage 2: Production
# ==========================
FROM base AS production

RUN apt-get install -y nodejs npm

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build && rm -rf node_modules

CMD ["php-fpm"]

