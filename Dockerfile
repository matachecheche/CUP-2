FROM php:8.2-apache

# 1. Instalar dependencias del sistema (libpq-dev es para PostgreSQL)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg

# 2. Instalar extensiones de PHP (pdo_pgsql en lugar de pdo_mysql)
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Instalar Node.js (para compilar Vite/Mix)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 5. Configurar Apache para apuntar a la carpeta /public de Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 6. Copiar los archivos de tu proyecto al contenedor
COPY . /var/www/html

# 7. Dar permisos correctos a las carpetas de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Instalar dependencias de PHP y Node (Pasos 2, 7 y 8 de tu lista)
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 9. El truco para las migraciones:
# Creamos un script que se ejecute CADA VEZ que el servidor arranque.
# Esto correrá las migraciones antes de encender Apache.
RUN echo '#!/bin/bin/sh\n\
php artisan migrate --force\n\
apachectl -D FOREGROUND' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Ejecutar el script de inicio
CMD ["/usr/local/bin/start.sh"]