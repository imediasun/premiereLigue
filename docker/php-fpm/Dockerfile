FROM php:8.3-fpm

WORKDIR /var/www/backend

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends curl git apt-transport-https lsb-release build-essential software-properties-common wget zip unzip libxml2-dev  libcurl4-openssl-dev pkg-config libssl-dev dirmngr vim \
	&& curl -o nodejs.deb https://deb.nodesource.com/node_16.x/pool/main/n/nodejs/nodejs_16.0.0-1nodesource1_amd64.deb \
    && apt-get install -y ./nodejs.deb \
    && rm nodejs.deb \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get install -y npm \
    && apt-get clean \
    && apt-get install -y python3 \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
           libjpeg-dev \
           libfreetype6-dev \
           zlib1g-dev \
           libzip-dev \
           zip \
           redis-server \
    libfreetype6-dev

RUN docker-php-ext-install pdo pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install ctype
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install xml
RUN docker-php-ext-install sockets
RUN docker-php-ext-install pcntl


RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev

RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/backend \
    && chmod -R 755 /var/www/backend

RUN chmod 777 /tmp

EXPOSE 9000

CMD ["php-fpm", "--nodaemonize"]
