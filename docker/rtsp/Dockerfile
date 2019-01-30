FROM php:7.2

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

# Install php-opencv
RUN apt install -y pkg-config cmake git wget

RUN wget https://raw.githubusercontent.com/php-opencv/php-opencv-packages/master/opencv_3.4_amd64.deb \
    && dpkg -i opencv_3.4_amd64.deb \
    && rm opencv_3.4_amd64.deb

RUN git clone https://github.com/php-opencv/php-opencv.git

RUN cd php-opencv \
    && phpize \
    && ./configure \
    && make \
    && make install

# Install ext-event (depends on ext-sockets)
RUN apt install -y libevent-dev libssl-dev \
    && docker-php-ext-install sockets \
    && docker-php-ext-enable sockets \
    && pecl install event \
    && docker-php-ext-enable event

EXPOSE 9001

ENTRYPOINT ["php"]