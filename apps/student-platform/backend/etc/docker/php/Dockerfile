FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath intl zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/platform

COPY apps/student-platform/backend/etc/docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY apps/student-platform/backend/ .

RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/platform

EXPOSE 9000
CMD ["php-fpm"]
