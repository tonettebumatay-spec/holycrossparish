FROM php:8.2-apache

# Set non-interactive mode to prevent hanging on prompts
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and Node.js
# The --no-install-recommends flag prevents the installation of 
# unnecessary packages (like extra language/l10n files) that cause conflicts.
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip \
    unzip \
    libpq-dev \
    curl \
    git \
    build-essential \
    libz-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    # Clean up apt caches to ensure a lightweight and non-corrupt build layer
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache rewrite module
RUN a2enmod rewrite

# Setup Working Directory
WORKDIR /var/www/html
COPY . .

# Adjust Apache configuration to point to the public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf

# Final build steps:
# If this step fails, it means your local composer.lock is not 
# synchronized with your composer.json.
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install --no-audit --prefer-offline \
    && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]