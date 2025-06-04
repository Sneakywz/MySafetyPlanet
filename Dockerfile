FROM php:8.2-apache

# Installer les extensions PHP + outils système
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Copier les fichiers de l'application
COPY . /var/www/html/

# Fixer les droits
RUN chown -R www-data:www-data /var/www/html
