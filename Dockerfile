FROM php:8.5-apache

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        intl \
        pdo_mysql \
        zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN sed -ri \
        's!/var/www/html!/var/www/html/webroot!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && mkdir -p tmp/cache/models tmp/cache/persistent tmp/cache/views logs \
    && chown -R www-data:www-data tmp logs \
    && chmod -R 775 tmp logs

EXPOSE 10000

CMD ["sh", "-c", "php bin/check-runtime.php && sed -ri \"s/^Listen .*/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -ri \"s/<VirtualHost \\*:[0-9]+>/<VirtualHost *:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
