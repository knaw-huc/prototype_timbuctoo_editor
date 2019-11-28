FROM php:7.2-apache
MAINTAINER Rob Zeeman <rob.zeeman@di.huc.knaw.nl>
EXPOSE 80 443
COPY .timpars /var/www/
COPY --chown=www-data:www-data  ./src/ /var/www/html/timpars/
RUN a2enmod rewrite 

