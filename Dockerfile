FROM php:8.3-fpm

COPY composer.lock composer.json /var/www/

WORKDIR /var/www/sport-spot

RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y \
        build-essential \
        libpng-dev \
        libwebp-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        locales \
        zip \
        jpegoptim optipng pngquant gifsicle \
        vim \
        unzip \
        git \
        curl \
        libzip-dev

RUN docker-php-ext-install pdo_mysql zip exif pcntl \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www/sport-spot

COPY --chown=www:www . /var/www/sport-spot

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

RUN php artisan key:generate

RUN chown -R www:www /var/www/sport-spot
RUN chown www:www /var/www/sport-spot
RUN chmod -R 777 /var/www/sport-spot

USER root

EXPOSE 9000
CMD ["php-fpm"]