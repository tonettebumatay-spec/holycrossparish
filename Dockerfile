# =====================================================================
# STAGE 1: COMPOSER BUILDER (Temporary environment to build dependencies)
# =====================================================================
FROM php:8.2-cli AS builder

# Install system dependencies required for Composer
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl

# Grab the official Composer binary image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Run composer installation inside the clean CLI environment
RUN composer install --no-dev --no-interaction --no-plugins --no-scripts --optimize-autoloader

# =====================================================================
# STAGE 2: PRODUCTION ENGINE (The actual small container that goes live)
# =====================================================================
FROM php:8.2-apache

# Install only essential runtime extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql

# Enable Apache rewrite engine for Laravel routes
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy your application source code directly from your project
COPY . .

# CRUCIAL: Copy the pre-compiled vendor directory straight from STAGE 1 (Builder)
COPY --from=builder /app/vendor /var/www/html/vendor

# Configure Apache document root pathways
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Build missing tracking directories and map permissions recursively
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
