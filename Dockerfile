# 1. Use an official production-ready PHP-Apache engine build
FROM php:8.2-apache

# 2. Install only essential system utilities (Lightweight)
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql

# 3. Enable Apache rewriting rules for Laravel routing
RUN a2enmod rewrite

# 4. Copy your custom project source files to the web directory
WORKDIR /var/www/html
COPY . .

# 5. Configure Apache permissions for Laravel public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Expose the standard internet deployment port
EXPOSE 80
