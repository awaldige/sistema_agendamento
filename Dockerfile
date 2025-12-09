# Imagem base do PHP com Apache
FROM php:8.2-apache

# Habilitar extensões necessárias do PHP
RUN docker-php-ext-install pdo pdo_mysql

# Ativar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar todos os arquivos do projeto para o servidor Apache
COPY . /var/www/html/

# Dar permissão para que o Apache acesse arquivos
RUN chown -R www-data:www-data /var/www/html

# Expor a porta 80 (Render usa)
EXPOSE 80



