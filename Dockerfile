FROM php:7.2-zts

ENV PHP_EXTRA_CONFIGURE_ARGS --enable-sockets

ENV REFRESHED_AT 2018-03-15

# install additional soft
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get -qq update && \
    apt-get -y install zip unzip git zlib1g-dev libmemcached-dev supervisor git libevent-dev \
    make \
    libssl-dev

# install extensions
RUN docker-php-ext-install pdo_mysql

RUN pecl install memcached-3.0.2
RUN docker-php-ext-enable memcached

RUN docker-php-ext-install sockets
RUN docker-php-ext-enable sockets

RUN git clone https://github.com/krakjoe/pthreads -b master /tmp/pthreads
RUN docker-php-ext-configure /tmp/pthreads --enable-pthreads
RUN docker-php-ext-install /tmp/pthreads

RUN pecl install event
RUN docker-php-ext-enable event

# install composer
ENV COMPOSER_HOME=/tmp/.composer

RUN curl -XGET https://getcomposer.org/installer > composer-setup.php && \
    php composer-setup.php --install-dir=/bin --filename=composer && \
    rm composer-setup.php

ARG ENABLE_XDEBUG=0

RUN if [ "$ENABLE_XDEBUG" -eq 1 ]; then \
    pecl install xdebug-2.6.1 && \
    docker-php-ext-enable xdebug; \
fi

RUN mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /tmp/.composer

ARG UID=1000
ARG GID=1000

RUN groupmod -g $GID www-data && \
    usermod -u $UID www-data

EXPOSE 8080
EXPOSE 8000

USER root

RUN apt-get -y install inetutils-ping

WORKDIR /var/www