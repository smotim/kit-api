FROM php:8.2-cli

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
    zlib1g-dev \
    libbrotli-dev

# Install PHP extensions
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Swoole extension for Octane
RUN pecl install swoole --enable-brotli=no --enable-openssl=no \
    && docker-php-ext-enable swoole

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy entire application first
COPY . .

# Set environment variable for unattended installation
ENV COMPOSER_COMPILE=all

# Install dependencies
RUN php -d memory_limit=-1 /usr/bin/composer install --no-interaction

# Install Octane
RUN php -d memory_limit=-1 /usr/bin/composer require laravel/octane --with-all-dependencies

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Create .env file if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate application key only if not already set
RUN php artisan key:generate --force

# Expose port for Octane
EXPOSE 8000

# Run Octane server
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]