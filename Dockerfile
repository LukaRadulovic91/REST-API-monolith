# Dockerfile

FROM php:8.2-fpm

# Instalacija sistema i PHP ekstenzija potrebnih za Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalacija Composer-a
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Kopiranje svih fajlova u container
COPY . .

# Instalacija PHP paketa
RUN composer install --no-dev --optimize-autoloader

# Podesi prava za storage i cache folder
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
