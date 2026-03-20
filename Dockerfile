FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY nginx.conf /etc/nginx/sites-available/default
COPY . /var/www/html

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /run/php

EXPOSE 80

CMD service nginx start && php-fpm
