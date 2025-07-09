# Usa la imagen base de PHP-FPM basada en Debian (NO Alpine)
FROM php:8.2-fpm

# Instala Nginx en esta misma imagen
# Aquí usamos apt-get en lugar de apk
RUN apt-get update && apt-get install -y nginx

# Instala las extensiones de PostgreSQL para PHP
# ¡CAMBIO AQUÍ! Usamos apt-get y luego docker-php-ext-install
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Elimina el contenido HTML por defecto de Nginx
# Los paths pueden variar un poco en Debian, pero este es común
RUN rm -rf /var/www/html/* # En Debian, el default de Nginx suele estar en /var/www/html

# Copia tu código de aplicación al directorio de trabajo
COPY . /var/www/html/

# Copia la configuración de Nginx
# Asegúrate de que el path de tu nginx.conf sea correcto para Debian
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Copia y da permisos de ejecución al script entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expone el puerto 80
EXPOSE 80

# Define el entrypoint para iniciar php-fpm y nginx
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []