FROM httpd:latest
RUN apt update && apt upgrade -y
COPY php-fpm-apache.conf /usr/local/apache2/conf/php-fpm-apache.conf
RUN echo "Include /usr/local/apache2/conf/php-fpm-apache.conf" >> /usr/local/apache2/conf/httpd.conf
