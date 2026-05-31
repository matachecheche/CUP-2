FROM php:8.2-apache

# 1. Instalar dependencias del sistema (incluyendo libpq-dev para Postgres)
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

# 2. Instalar extensiones de PHP necesarias para Laravel y Postgres
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Instalar Composer desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Instalar Node.js (para poder ejecutar npm run build)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 5. Configurar Apache para Laravel
# Apuntamos el document root a la carpeta /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 6. Copiar los archivos de tu proyecto
COPY . /var/www/html

# 7. Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Ejecutar comandos de despliegue
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build