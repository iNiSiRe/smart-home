FROM ubuntu:15.10
ENV REFRESHED_AT 2015-06-08

RUN apt-get dist-upgrade
RUN apt-get -qq update

RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get -y install curl php5 php5-cli php5-dev php5-curl php5-gd php5-mysql \
    php5-intl php5-mcrypt php5-memcached supervisor \
    php5-redis \
    php5-mongo \
    php5-xdebug \
    git \
    libevent-dev

RUN pecl install channel://pecl.php.net/libevent-0.1.0

# install composer
ENV COMPOSER_HOME=/tmp/.composer

RUN curl -XGET https://getcomposer.org/installer > composer-setup.php && \
    php composer-setup.php --install-dir=/bin --filename=composer --version=1.3.0 && \
    rm composer-setup.php

# Add users
RUN adduser user --home /home/user --shell /bin/bash --disabled-password --gecos ""

# Add users to sudoers, so no need to ask for password
RUN echo "user ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

EXPOSE 8080
EXPOSE 8000

WORKDIR /var/www/