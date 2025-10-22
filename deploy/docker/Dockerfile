FROM php:7.4-fpm-alpine3.16

ENV COMPOSER_V 2.6.1
ENV PHP_EXT_V 2.1.65

ENV DEPS \
    supervisor \
    nginx \
    make \
    bash \
    iproute2 \
    netcat-openbsd \
    nano

ENV PHP_EXTS \
    @composer-${COMPOSER_V} \
    gd \
    ldap \
    pdo_pgsql \
    pgsql \
    zip

# https://github.com/mlocati/docker-php-extension-installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/${PHP_EXT_V}/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN apk --no-cache update && apk --no-cache add ${DEPS}
RUN install-php-extensions ${PHP_EXTS}

COPY ./../.. /var/www
COPY deploy/docker/Makefile /var/www/Makefile
COPY deploy/docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY deploy/docker/nginx/app.conf /etc/nginx/http.d/default.conf
COPY deploy/docker/php/php.ini /usr/local/etc/php/php.ini
COPY deploy/docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY deploy/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www

RUN composer install

EXPOSE 80

RUN chmod +x ./deploy/docker/entrypoint.sh

ENTRYPOINT ["./deploy/docker/entrypoint.sh"]

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
