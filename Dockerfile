FROM php:7.0-zts

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

RUN pecl install xdebug-2.5.0
RUN docker-php-ext-enable xdebug

RUN pecl install memcached-3.0.2
RUN docker-php-ext-enable memcached

RUN docker-php-ext-install sockets
RUN docker-php-ext-enable sockets

#RUN docker-php-ext-enable maintainer-zts
RUN pecl install pthreads
RUN docker-php-ext-enable pthreads

RUN pecl install event
RUN docker-php-ext-enable event

# install composer
ENV COMPOSER_HOME=/tmp/.composer

RUN curl -XGET https://getcomposer.org/installer > composer-setup.php && \
    php composer-setup.php --install-dir=/bin --filename=composer && \
    rm composer-setup.php

RUN groupadd -g 1000 user
RUN useradd -ms /bin/bash -u 1000 -g 1000 user

RUN mkdir -p /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /tmp/.composer

# Add users to sudoers, so no need to ask for password
RUN echo "user ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

EXPOSE 8080
EXPOSE 8000

USER root

WORKDIR /var/www