FROM php:8.2-fpm-alpine as php_builder

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

RUN find / -name "php-fpm" 2>/dev/null || true
RUN which php-fpm || true


WORKDIR /var/www/html
COPY . /var/www/html/

FROM nginx:alpine

COPY nginx/default.conf /etc/nginx/conf.d/default.conf

RUN rm -rf /etc/nginx/html/*

COPY --from=php_builder /var/www/html /var/www/html

EXPOSE 80


CMD sh -c "/usr/local/sbin/php-fpm && nginx -g 'daemon off;'"