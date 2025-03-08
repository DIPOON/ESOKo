FROM php:8.2.0-fpm

# 로컬 개발 환경에서 바로 접속하기 위한 포트
EXPOSE 8000

## 패키지 최신화. 개발 환경과 세팅하는 순간에 같은 패키지를 보도록 스킵
#RUN apt update && apt upgrade -y

# Install MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug 설정 추가
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Nginx 와 약속된 디렉토리에 애플리케이션 코드 복사
WORKDIR /var/www/html
COPY . /var/www/html
