FROM php:7.2-apache
MAINTAINER Rob Zeeman <rob.zeeman@di.huc.knaw.nl>
EXPOSE 80 443
COPY ./src/ /var/www/html/timpars/
COPY .timpars /var/www/
CMD mkdir /var/www/html/timpars/views/templates_c &&
chmod -R 777 /var/www/html/timpars/views/templates_c
RUN a2enmod rewrite 

