FROM php:8.2-fpm

# Install system dependencies
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

# Install PHP extensions
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Install MongoDB extension
RUN pecl install mongodb-1.18.0 \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html