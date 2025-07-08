#!/bin/sh
set -e

# Inicia php-fpm en segundo plano, pero en modo de primer plano (-F)
# Usamos la ruta absoluta más común para php-fpm en imágenes alpine
echo "Iniciando php-fpm..."
/usr/local/sbin/php-fpm -F &

# Inicia nginx en primer plano, reemplazando el proceso del script.
# Esto asegura que nginx sea el proceso principal del contenedor.
echo "Iniciando nginx..."
exec nginx -g 'daemon off;'

# Las líneas después de 'exec' no se ejecutarán.