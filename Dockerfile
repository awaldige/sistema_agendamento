FROM php:8.1-apache

# Habilitar mod_rewrite e extensões necessárias
RUN a2enmod rewrite

# Instalar extensões do PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todos os arquivos do projeto para /var/www/html
COPY . /var/www/html/

# Dar permissão ao Apache
RUN chown -R www-data:www-data /var/www/html

