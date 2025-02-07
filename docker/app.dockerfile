FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config \
    libssl-dev \
    zlib1g-dev

# Установка PHP расширений
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Install MongoDB extension with specific version
RUN pecl install mongodb-1.18.0 \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
    && docker-php-ext-enable mongodb

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html