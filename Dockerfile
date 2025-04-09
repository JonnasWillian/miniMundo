FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    nginx \
    supervisor \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY .docker/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY .docker/wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]