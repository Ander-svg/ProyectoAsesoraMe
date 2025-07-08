# Etapa 1: Construir la imagen PHP-FPM
FROM php:8.2-fpm-alpine as php_builder

# Instalar dependencias para extensiones PHP (pdo_pgsql para PostgreSQL)
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# --- INICIO DE LÍNEAS DE DEPURACIÓN (puedes quitarlas después de que funcione) ---
RUN ls -la /
RUN ls -la .
# --- FIN DE LÍNEAS DE DEPURACIÓN ---

WORKDIR /var/www/html
COPY . /var/www/html/  # <-- ¡Esta es la única línea que debe quedar aquí, sin el comentario instructivo al final o con el "#" al principio!

# Opcional: Si usas Composer para manejar dependencias de PHP
# ... (el resto de tu Dockerfile)