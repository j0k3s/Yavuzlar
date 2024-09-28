FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

# MySQL istemcisini yükle
RUN apt-get update && apt-get install -y default-mysql-client

# PHP'nin MySQL ile iletişim kurabilmesi için gerekli yapılandırmayı ekle
RUN echo "mysql.default_socket = /var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/php.ini
RUN echo "mysqli.default_socket = /var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/php.ini
RUN echo "pdo_mysql.default_socket = /var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/php.ini

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
WORKDIR /var/www/html
RUN echo "DirectoryIndex login.php" >> /etc/apache2/apache2.conf