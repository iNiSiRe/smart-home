FROM ubuntu:latest

ENV DEBIAN_FRONTEND noninteractive

RUN apt update

RUN apt install -y \
    autoconf automake

RUN cd /
ADD "https://ffmpeg.org/releases/ffmpeg-4.1.tar.bz2" .
RUN tar -xf ffmpeg-4.1.tar.bz2

RUN apt install -y build-essential g++

RUN cd ffmpeg-4.1 \
    && ./configure --disable-x86asm \
    && make \
    && make install

RUN cd /
ADD "https://github.com/Motion-Project/motion/archive/release-4.2.1.tar.gz" .
RUN tar -xf release-4.2.1.tar.gz

# Pre-install motion dependencies
RUN apt install -y \
    autoconf automake build-essential pkgconf libtool libzip-dev \
    libjpeg-dev git libwebp-dev gettext libmicrohttpd-dev

RUN cd motion-release-4.2.1 \
    && autoreconf -fiv  \
    && ./configure --with-ffmpeg=./ffmpeg-4.1 \
    && make \
    && make install

EXPOSE 8080-8100

CMD [ "motion", "-n" ]