FROM php:7.1-fpm

LABEL maintainer="<1129610361@qq.com>"

ENV TIMEZONE Asia/Shanghai
ENV SWOOLE_VERSION 4.2.6
ENV SRC_DIR /home
ENV PHP_DIR /usr/local
ENV INIT_FILE /usr/local/etc/php/conf.d
ENV COMPOSER_VERSION 1.7.3
ENV HIREDIS_VERSION 0.13.3
ENV PHPREDIS_VERSION 3.1.6
ENV SWOOLE_VERSION 4.3.5
ENV RABBITMQ_VERSION 0.8.0
ENV AMQP_VERSION 1.9.3
ENV IMAGEMAGICK_VERSION 7.0.8
ENV IMAGICK_VERSION 3.4.3
ENV YAC_VERSION 2.0.2
ENV LIBMEMCACHED_VERSION 1.0.18
ENV MEMCACHED_VERSION 3.0.4
ENV LIBEVENT_VERSION 2.1.8
ENV EVENT_VERSION 2.4.1
ENV PHPINOTIFY_VERSION 2.0.0
ENV PHPDS_VERSION 1.2.4
ENV MONGODB_VERSION 1.5.1

# replace sources.list
RUN sed -i 's/deb.debian.org/mirrors.163.com/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/mirrors.163.com/g' /etc/apt/sources.list \
    && cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone

# init
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    curl \
    vim \
    net-tools \
    wget \
    zip \
    git \
    unzip \
    procps \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libmemcached-dev \
    apt-utils \
    && rm -rf /var/lib/apt/lists/*

# install php extentions
RUN pecl channel-update pecl.php.net \
    && docker-php-ext-configure gd \
    --enable-gd-native-ttf \
    --with-jpeg-dir=/usr/lib \
    --with-freetype-dir=/usr/include/freetype2 \
    && docker-php-ext-install -j$(nproc) bcmath mysqli pdo_mysql gd zip opcache pcntl sockets

# install composer
ADD install/composer-${COMPOSER_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/composer-${COMPOSER_VERSION} \
    && mv composer.phar /usr/local/bin/composer \
    && composer config -g repositories.packagist composer https://mirrors.aliyun.com/composer \
    && composer self-update --clean-backups

#  hiredis
ADD install/hiredis-${HIREDIS_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/hiredis-${HIREDIS_VERSION} \
    && make clean > /dev/null \
    && make \
    && make install \
    && ldconfig \
    && rm -f ${SRC_DIR}/hiredis-${HIREDIS_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/hiredis-${HIREDIS_VERSION}

#  redis
ADD install/redis-${PHPREDIS_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/phpredis-${PHPREDIS_VERSION} \
    && phpize \
    && ./configure \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=redis.so" > ${INIT_FILE}/redis.ini \
    && rm -f ${SRC_DIR}/redis-${PHPREDIS_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/phpredis-${PHPREDIS_VERSION}

#  swoole
ADD install/swoole-src-${SWOOLE_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/swoole-src-${SWOOLE_VERSION} \
    && phpize \
    && ./configure --enable-async-redis --enable-openssl --enable-mysqlnd --enable-coroutine \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=swoole.so" > ${INIT_FILE}/swoole.ini \
    && rm -f ${SRC_DIR}/swoole-${SWOOLE_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/swoole-src-${SWOOLE_VERSION}

#   rabbitmq-c
ADD install/rabbitmq-c-${RABBITMQ_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/rabbitmq-c-${RABBITMQ_VERSION} \
    && ./configure --prefix=/usr/local/rabbitmq \
    && make \
    && make install \
    && rm -f ${SRC_DIR}/rabbitmq-c-${RABBITMQ_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/rabbitmq-c-${RABBITMQ_VERSION}

#   amqp
ADD install/amqp-${AMQP_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/amqp-${AMQP_VERSION} \
    && phpize \
    && ./configure --with-php-config=${PHP_DIR}/bin/php-config --with-amqp --with-librabbitmq-dir=/usr/local/rabbitmq \
    && make \
    && make install \
    && echo "extension=amqp.so" > ${INIT_FILE}/amqp.ini \
    && rm -f ${SRC_DIR}/amqp-${AMQP_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/amqp-${AMQP_VERSION}

#   ImageMagick
ADD install/ImageMagick-${IMAGEMAGICK_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/ImageMagick-${IMAGEMAGICK_VERSION} \
    && ./configure -prefix=/usr/local/imagemagick \
    && make \
    && make install \
    && rm -f ${SRC_DIR}/ImageMagick-${IMAGEMAGICK_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/ImageMagick-${IMAGEMAGICK_VERSION}

#   imagick
ADD install/imagick-${IMAGICK_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/imagick-${IMAGICK_VERSION} \
    && phpize \
    && ./configure --prefix=/usr/local/imagick --with-php-config=${PHP_DIR}/bin/php-config  --with-imagick=/usr/local/imagemagick \
    && make \
    && make install \
    && echo "extension=imagick.so" > ${INIT_FILE}/imagick.ini \
    && rm -f ${SRC_DIR}/imagick-${IMAGICK_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/imagick-${IMAGICK_VERSION}

#   yac
ADD install/yac-${YAC_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/yac-${YAC_VERSION} \
    && phpize \
    && ./configure --with-php-config=${PHP_DIR}/bin/php-config \
    && make \
    && make install \
    && echo "extension=yac.so" > ${INIT_FILE}/yac.ini \
    && rm -f ${SRC_DIR}/yac-${YAC_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/yac-${YAC_VERSION}

#   libmemcached
ADD install/libmemcached-${LIBMEMCACHED_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/libmemcached-${LIBMEMCACHED_VERSION} \
    && ./configure --prefix=/usr/local/libmemcached --with-memcached \
    && make \
    && make install \
    && rm -f ${SRC_DIR}/libmemcached-${LIBMEMCACHED_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/libmemcached-${LIBMEMCACHED_VERSION}

#   php-memcached
ADD install/memcached-${MEMCACHED_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/php-memcached-${MEMCACHED_VERSION} \
    && phpize \
    && ./configure --disable-memcached-sasl  --with-php-config=${PHP_DIR}/bin/php-config --with-libmemcached-dir=/usr/local/libmemcached \
    && make \
    && make install \
    && echo "extension=memcached.so" > ${INIT_FILE}/memcached.ini \
    && rm -f ${SRC_DIR}/memcached-${MEMCACHED_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/php-memcached-${MEMCACHED_VERSION}

#   libevent
ADD install/libevent-${LIBEVENT_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/libevent-${LIBEVENT_VERSION} \
    && ./configure --prefix=/usr/local/libevent \
    && make clean > /dev/null \
    && make \
    && make install \
    && rm -f ${SRC_DIR}/libevent-${LIBEVENT_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/libevent-${LIBEVENT_VERSION}

#   event
ADD install/event-${EVENT_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/event-${EVENT_VERSION} \
    && phpize \
    && ./configure --with-php-config=${PHP_DIR}/bin/php-config --with-event-libevent-dir=/usr/local/libevent \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=event.so" > ${INIT_FILE}/event.ini \
    && rm -f ${SRC_DIR}/event-${EVENT_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/event-${EVENT_VERSION}

#   mongodb
ADD install/mongodb-${MONGODB_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/mongodb-${MONGODB_VERSION} \
    && phpize \
    && ./configure --with-php-config=${PHP_DIR}/bin/php-config \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=mongodb.so" > ${INIT_FILE}/mongodb.ini \
    && rm -f ${SRC_DIR}/mongodb-${MONGODB_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/mongodb-${MONGODB_VERSION}

#  ds
ADD install/ds-${PHPDS_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/extension-${PHPDS_VERSION} \
    && phpize \
    && ./configure \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=ds.so" > ${INIT_FILE}/ds.ini \
    && rm -f ${SRC_DIR}/ds-${PHPDS_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/extension-${PHPDS_VERSION}


#  inotify
ADD install/inotify-${PHPINOTIFY_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/php-inotify-${PHPINOTIFY_VERSION} \
    && phpize \
    && ./configure \
    && make clean > /dev/null \
    && make \
    && make install \
    && echo "extension=inotify.so" > ${INIT_FILE}/inotify.ini \
    && rm -f ${SRC_DIR}/inotify-${PHPINOTIFY_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/php-inotify-${PHPINOTIFY_VERSION}

COPY ./config/* /usr/local/etc/php/conf.d/

EXPOSE 9000

CMD ["php-fpm"]