FROM php:8.4-fpm

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN addgroup --system --gid ${GID} base
RUN adduser --system --home /bin/sh --no-create-home --uid ${UID} --ingroup base --disabled-login --disabled-password base

RUN sed -i 's/user = www-data/user = base/g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group = www-data/group = base/g' /usr/local/etc/php-fpm.d/www.conf

RUN chown -R ${UID}:${GID} /var/www/html

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libexif-dev \
    libonig-dev \
    autoconf \
    pkg-config \
    libssl-dev

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000

USER base
