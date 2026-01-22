FROM richarvey/nginx-php-fpm:8.4

# Webroot para Laravel
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Copiar proyecto
COPY . /var/www/html

WORKDIR /var/www/html

# Crear .env para el build
RUN cp .env.example .env

# Instalar dependencias PHP (SIN scripts)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80
