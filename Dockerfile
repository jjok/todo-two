FROM php:7.3-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
 && pecl install xdebug-2.7.2 \
 && docker-php-ext-enable xdebug

WORKDIR /tmp
