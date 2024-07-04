FROM php:8.1.2-fpm
EXPOSE 8000
RUN docker-php-ext-install mysqli pdo pdo_mysql
WORKDIR /var/www/html
COPY . /var/www/html
