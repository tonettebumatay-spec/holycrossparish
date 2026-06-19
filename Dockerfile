FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

WORKDIR /var/www/html
# This copies everything, including the massive vendor folder you just pushed via CMD!
COPY . .

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
