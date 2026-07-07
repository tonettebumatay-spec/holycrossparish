FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install core dependencies and Node.js
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip libpq-dev curl git build-essential \
    libz-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure PHP extensions (including pgsql for your Postgres connection)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# 🔥 Install symfony/mime before the main composer install to fix file upload validation
RUN composer require symfony/mime --no-interaction --no-scripts

# Adjust Apache Document Root to /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf

# Final Install
# --no-scripts prevents artisan from running before the DB is configured
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && npm install --no-audit --prefer-offline \
    && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the web port
EXPOSE 80

# Entrypoint script (migrate then start Apache)
RUN printf "#!/bin/bash\necho 'Running database migrations...'\nphp artisan migrate --force\necho 'Starting Apache...'\napache2-foreground" > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]