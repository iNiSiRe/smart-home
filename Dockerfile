FROM php:7.0-cli

ENV REFRESHED_AT 2017-10-02

# install additional soft
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get -qq update && \
    apt-get -y install zip unzip git zlib1g-dev libmemcached-dev supervisor git libevent-dev && \
    rm -rf /var/lib/apt/lists/*

# install extensions
RUN docker-php-ext-install pdo_mysql

RUN pecl install xdebug-2.5.0
RUN docker-php-ext-enable xdebug

RUN pecl install memcached-3.0.2
RUN docker-php-ext-enable memcached

# install composer
ENV COMPOSER_HOME=/tmp/.composer

RUN curl -XGET https://getcomposer.org/installer > composer-setup.php && \
    php composer-setup.php --install-dir=/bin --filename=composer && \
    rm composer-setup.php

RUN usermod -u 1000 www-data && \
    mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /tmp/.composer

# Add users to sudoers, so no need to ask for password
RUN echo "user ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

EXPOSE 8080
EXPOSE 8000

USER root

WORKDIR /var/www