FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install core dependencies and Node.js
# The --no-install-recommends prevents l10n package conflicts
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip libpq-dev curl git build-essential \
    libz-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# Adjust Apache Document Root to /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf

# Final Install
# This now works because you removed the QR dependency locally
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install --no-audit --prefer-offline \
    && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]