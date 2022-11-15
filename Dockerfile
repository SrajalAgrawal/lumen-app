FROM php:8.1.10-fpm-alpine

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html/

COPY SSL-Vmock.crt /usr/local/share/ca-certificates/SSL-Vmock.crt

RUN cat /usr/local/share/ca-certificates/SSL-Vmock.crt >> /etc/ssl/certs/ca-certificates.crt

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

COPY . .

RUN composer install