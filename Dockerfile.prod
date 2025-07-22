# Dockerfile

FROM php:8.3-fpm

# Installer nginx, supervisor et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    zip unzip \
    git curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tous les fichiers de l'application
COPY . .

# Installer les dépendances PHP (vendor)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copier la configuration nginx et supervisor
COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisord.conf

# Exposer le port HTTP
EXPOSE 80

# Lancer Supervisor (qui va gérer PHP-FPM + nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]