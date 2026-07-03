# Use PHP 8.2 with Apache
FROM php:8.2-apache

# 1. Install System Dependencies
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libpq-dev \
    curl \
    git \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql

# 2. Install Composer for PHP dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Enable Apache Rewrite Module
RUN a2enmod rewrite

# 4. Set Work Directory
WORKDIR /var/www/html
COPY . .

# 5. Install PHP and JS Dependencies
# --prefer-dist and no-audit help bypass common network/git errors
RUN composer install --no-dev --optimize-autoloader \
    && npm install --no-audit --prefer-offline \
    && npm run build

# 6. Configure Apache Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 7. Set Permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]