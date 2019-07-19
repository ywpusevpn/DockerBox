FROM centos:centos7

LABEL maintainer="<1129610361@qq.com>"

ENV SRC_DIR /usr/local
ENV PHP_DIR /usr/local/php/${PHP_VERSION}
ENV PHP_INI_DIR /etc/php/${PHP_VERSION}/cli
ENV INIT_FILE ${PHP_INI_DIR}/conf.d
ENV PHP_VERSION 7.1.14
ENV SWOOLE_VERSION 4.3.5
ENV HIREDIS_VERSION 0.13.3
ENV PHPREDIS_VERSION 3.1.6
ENV PHPDS_VERSION 1.2.4
ENV PHPINOTIFY_VERSION 2.0.0
ENV RABBITMQ_VERSION 0.8.0
ENV AMQP_VERSION 1.9.3
ENV LIBEVENT_VERSION 2.1.8
ENV EVENT_VERSION 2.4.1
ENV IMAGEMAGICK_VERSION 7.0.8
ENV IMAGICK_VERSION 3.4.3
ENV YAC_VERSION 2.0.2
ENV LIBMEMCACHED_VERSION 1.0.18
ENV MEMCACHED_VERSION 3.0.4
ENV MONGODB_VERSION 1.5.1
ENV COMPOSER_VERSION 1.7.3

#set ldconf
RUN echo "include /etc/ld.so.conf.d/*.conf" > /etc/ld.so.conf \
    && cd /etc/ld.so.conf.d \
    && echo "/usr/local/lib" > /etc/ld.so.conf.d/libc.conf

# tools
RUN yum -y install \
        wget \
        vim \
        gcc \
        make \
        git \
        libjpeg \
        libjpeg-devel \
        libpng \
        libpng-devel \
        freetype \
        freetype-devel \
        net-tools \
        autoconf \
        autoreconf \
        libxml2 \
        libxml2-devel \
        openssl \
        openssl-devel \
        curl \
        curl-devel \
        pcre \
        pcre-devel \
        php-gd \
        gd \
        libxslt \
        libxslt-devel \
        bzip2 \
        bzip2-devel \
        libedit \
        libedit-devel \
        glibc-headers \
        gcc-c++ \
    && rm -rf /var/cache/{yum,ldconfig}/* \
    && rm -rf /etc/ld.so.cache \
    && yum clean all

# php
ADD install/php-${PHP_VERSION}.tar.gz ${SRC_DIR}/
RUN cd ${SRC_DIR}/php-${PHP_VERSION} \
    && ln -s /usr/lib64/libssl.so /usr/lib \
    && ./configure --prefix=${PHP_DIR} \
       --with-config-file-path=${PHP_INI_DIR} \
       --with-config-file-scan-dir="${PHP_INI_DIR}/conf.d" \
       --disable-cgi \
       --enable-bcmath \
       --enable-mbstring \
       --enable-mysqlnd \
       --enable-opcache \
       --enable-pcntl \
       --enable-xml \
       --enable-zip \
       --enable-sockets \
       --enable-soap \
       --with-curl \
       --with-libedit \
       --with-openssl \
       --with-zlib \
       --with-curl \
       --with-mysqli \
       --with-gd \
       --with-jpeg-dir \
       --with-png-dir \
       --with-freetype-dir \
       --enable-gd-native-ttf \
       --enable-gd-jis-conv \
       --with-pdo-mysql \
       --with-pear \
       --with-zlib \
    && make clean > /dev/null \
    && make \
    && make install \
    && ln -s ${PHP_DIR}/bin/php /usr/local/bin/ \
    && ln -s ${PHP_DIR}/bin/phpize /usr/local/bin/ \
    && ln -s ${PHP_DIR}/bin/pecl /usr/local/bin/ \
    && ln -s ${PHP_DIR}/bin/php-config /usr/local/bin/ \
    && mkdir -p ${PHP_INI_DIR}/conf.d \
    && cp ${SRC_DIR}/php-${PHP_VERSION}/php.ini-production ${PHP_INI_DIR}/php.ini \
    && echo -e "opcache.enable=1\nopcache.enable_cli=1\nzend_extension=opcache.so" > ${PHP_INI_DIR}/conf.d/10-opcache.ini \
    && rm -f ${SRC_DIR}/php-${PHP_VERSION}.tar.gz \
    && rm -rf ${SRC_DIR}/php-${PHP_VERSION}

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

COPY ./config/* ${INIT_FILE}/
