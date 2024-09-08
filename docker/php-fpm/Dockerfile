FROM php:8.3-fpm

WORKDIR /var/www/backend

# Устанавливаем необходимые пакеты и расширения
RUN apt-get update && apt-get install -y \
    libzip-dev \
    wget \
    unzip \
    libprotobuf-dev \
    protobuf-compiler \
    libssl-dev \
    && pecl install redis grpc protobuf \
    && docker-php-ext-enable redis grpc protobuf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer require stanley-cheung/protobuf-php grpc/grpc

RUN chown -R www-data:www-data /var/www/backend \
    && chmod -R 755 /var/www/backend

RUN chmod 777 /tmp

EXPOSE 9000

CMD ["php-fpm", "--nodaemonize"]
