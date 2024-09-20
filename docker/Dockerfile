FROM php:8.1-fpm-alpine3.15

WORKDIR /var/www/html

RUN apk add --no-cache git bash

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip pdo pdo_mysql bcmath @composer; \
    rm /usr/local/bin/install-php-extensions;

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]