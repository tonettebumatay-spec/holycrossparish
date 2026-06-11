# 1. Use an official production-ready PHP-Apache engine build
FROM php:8.2-apache

# 2. Install essential system utilities and PHP core extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql

# 3. Securely install Composer inside the container image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Enable Apache rewriting rules for Laravel routing
RUN a2enmod rewrite

# 5. Set up the working directory layout
WORKDIR /var/www/html
COPY . .

# 6. Memory-Optimized Dependency Install (Prevents Exit Code 4)
# We set php memory limits for composer explicitly and bypass heavy dev engines
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-interaction --no-plugins --no-scripts --optimize-autoloader

# 7. Configure Apache permissions and ensure required directories exist
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Expose the standard internet deployment port
EXPOSE 80
