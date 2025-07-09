# Usa la imagen base de PHP-FPM, que ya incluye PHP y php-fpm
FROM php:8.2-fpm-alpine

# Instala Nginx en esta misma imagen
RUN apk add --no-cache nginx

# Instala las extensiones de PostgreSQL para PHP
# ¡CAMBIO AQUÍ! Primero actualizamos los índices de paquetes con 'apk update'
RUN apk update && apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Elimina el contenido HTML por defecto de Nginx
RUN rm -rf /etc/nginx/html/*

# Copia tu código de aplicación al directorio de trabajo
COPY . /var/www/html/

# Copia la configuración de Nginx
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Copia y da permisos de ejecución al script entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expone el puerto 80
EXPOSE 80

# Define el entrypoint para iniciar php-fpm y nginx
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []