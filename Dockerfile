# Usar la imagen oficial de PHP como base
FROM php:8.0-apache

# Copiar todos los archivos del directorio actual al contenedor
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80

# Comando para iniciar el servidor Apache con PHP
CMD ["apache2-foreground"]
