FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath pdo_mysql gd zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
