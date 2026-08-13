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

# The public admission form has three 2 MB file slots. Browsers send them as
# base64 data URLs, so reserve a small envelope above their 8 MB payload.
RUN printf '%s\\n' 'LimitRequestBody 9437184' > /etc/apache2/conf-available/ems-request-limits.conf \
    && a2enconf ems-request-limits

EXPOSE 10000

CMD ["sh", "bin/docker-entrypoint.sh"]
