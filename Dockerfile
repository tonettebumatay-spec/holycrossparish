# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set non-interactive to avoid hanging on configuration prompts
ENV DEBIAN_FRONTEND=noninteractive

# 1. Install System Dependencies & Essential Build Tools
# Using --no-install-recommends prevents unnecessary packages that cause conflicts
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip \
    unzip \
    libpq-dev \
    curl \
    git \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    build-essential \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    # Clean up APT cache to ensure no corrupted files remain
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    # Configure and install PHP extensions
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath gd

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Enable Apache Rewrite Module
RUN a2enmod rewrite

# 4. Set Work Directory
WORKDIR /var/www/html
COPY . .

# 5. Install PHP and JS Dependencies
# Using --no-interaction to prevent build hangs
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install --no-audit --prefer-offline \
    && npm run build

# 6. Configure Apache Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 7. Set Permissions for Storage/Cache
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]