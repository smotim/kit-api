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
    libssl-dev  # Добавляем SSL зависимости

# Установка PHP расширений
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Установка расширения MongoDB для PHP с SSL
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html