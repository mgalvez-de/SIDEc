# syntax=docker/dockerfile:1
# Imagen de SIDEc para el servidor: PHP 8.2 con Apache (las dependencias del proyecto exigen PHP 8.2).
FROM php:8.2-apache

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip libzip-dev libicu-dev \
 && docker-php-ext-install pdo_mysql zip intl bcmath \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Apache sirve la carpeta public/ de Laravel
COPY <<EOF /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .

# El CSS y JS ya vienen compilados en public/, así que no hace falta Node
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && chown -R www-data:www-data storage bootstrap/cache
