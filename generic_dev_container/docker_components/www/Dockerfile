FROM php:8.2-fpm

# Install system dependencies (add PostgreSQL and OpenSSL build deps)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libssh2-1-dev \
    pkg-config \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (MySQL, PostgreSQL, GD, Intl, Zip, Opcache, etc.)
RUN docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install SSH2 extension via PECL
RUN pecl install ssh2 \
    && docker-php-ext-enable ssh2

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html
