# 1. Use an official production-ready PHP-Apache engine build
FROM php:8.2-apache

# 2. Install essential system dependencies and database tools
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd

# 3. Enable Apache rewriting rules for Laravel routing
RUN a2enmod rewrite

# 4. Install Composer directly inside the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copy your custom project source files to the web directory
WORKDIR /var/www/html
COPY . .

# 6. MEMORY OPTIMIZATION: Tell Composer to restrict its memory limit allocation
ENV COMPOSER_MEMORY_LIMIT=-1

# 7. Install production packages with strict low-memory optimization flags
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# 8. Configure Apache permissions for Laravel public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Expose the standard internet deployment port
EXPOSE 80
