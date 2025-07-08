FROM php:8.2-fpm-alpine as php_builder

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html
COPY . /var/www/html/

FROM nginx:alpine

COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN rm -rf /etc/nginx/html/*

COPY --from=php_builder /var/www/html /var/www/html

EXPOSE 80


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD []