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
 RUN docker-php-ext-install pdo_mysql bcmath gd

# 2. Install Composer for PHP dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Enable Apache Rewrite Module
RUN a2enmod rewrite

# 4. Set Work Directory
WORKDIR /var/www/html
COPY . .

# # 5. Install PHP Dependencies separately
RUN composer install --no-dev --optimize-autoloader

# 6. Install JS Dependencies separately
RUN npm install --no-audit --prefer-offline

# 7. Build Assets
RUN npm run build
EXPOSE 80
CMD ["apache2-foreground"]