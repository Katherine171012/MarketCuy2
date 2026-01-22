FROM php:8.4-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuración de Nginx
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Crear .env
RUN cp .env.example .env

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "nginx && php-fpm"]
