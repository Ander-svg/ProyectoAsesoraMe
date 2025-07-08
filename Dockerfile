# Etapa 1: Construir la imagen PHP-FPM
FROM php:8.2-fpm-alpine as php_builder

# Instalar dependencias para extensiones PHP (pdo_pgsql para PostgreSQL)
# 'apk add' es para Alpine Linux, que es una base ligera para Docker.
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar los archivos de tu aplicación PHP al directorio de trabajo del contenedor
# Asume que tus archivos .php y .html están en la raíz de tu repositorio
WORKDIR /var/www/html
COPY . /var/www/html/

# Opcional: Si usas Composer para manejar dependencias de PHP
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN composer install --no-dev --optimize-autoloader

# Etapa 2: Servir con Nginx
FROM nginx:alpine

# Copiar la configuración de Nginx (crearemos este archivo en el siguiente paso)
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Eliminar la página de bienvenida por defecto de Nginx
RUN rm -rf /etc/nginx/html/*

# Copiar los archivos de la aplicación PHP desde la etapa de php_builder
COPY --from=php_builder /var/www/html /var/www/html

# Exponer el puerto 80 del contenedor (Nginx escuchará aquí)
EXPOSE 80

# Comando para iniciar Nginx en primer plano y PHP-FPM en segundo plano
# Render mapeará su puerto dinámico ($PORT) a este puerto 80 del contenedor.
CMD sh -c "php-fpm && nginx -g 'daemon off;'"