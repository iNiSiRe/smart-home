FROM ubuntu:xenial

ENV DEBIAN_FRONTEND noninteractive

# Pre-install packages
RUN apt update && apt install -y \
 software-properties-common

RUN add-apt-repository -y ppa:jonathonf/ffmpeg-4

# Install packages
RUN apt update && apt install -y \
        tzdata \
        curl \
        ffmpeg \
        autoconf automake pkgconf libtool libjpeg8-dev build-essential libzip-dev gettext libmicrohttpd-dev \
        unzip \
        libavutil-dev libavformat-dev libavcodec-dev libswscale-dev libavdevice-dev

ADD "https://github.com/Motion-Project/motion/archive/release-4.1.1.zip" motion.zip

RUN unzip motion.zip \
    && cd motion-release-4.1.1 \
    && autoreconf -fiv \
    && ./configure \
    && make \
    && make install

EXPOSE 8080-8100

CMD [ "motion", "-n" ]