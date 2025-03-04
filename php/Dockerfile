FROM php:8.4-fpm-alpine

# Установка необходимых расширений PHP
RUN apk add --no-cache \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Установка рабочей директории
WORKDIR /var/www/html

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка прав
RUN chown -R www-data:www-data /var/www/html