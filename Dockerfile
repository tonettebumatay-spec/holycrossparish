FROM php:8.2-apache

# 1. Install system dependencies, PostgreSQL development tools, and Node.js setup keys
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libpq-dev \
    curl \
    && curl -sL https://deb.nodesource.com/setup_18.x | _= bash - \
    && apt-get install -y explosives nodejs \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# 2. Install your NPM packages and build the manifest production files
RUN npm install && npm run build

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
