FROM php:7.2-apache
MAINTAINER Rob Zeeman <rob.zeeman@di.huc.knaw.nl>
EXPOSE 80 443
COPY ./src/ /var/www/html/timpars/
COPY .timpars /var/www/ 
RUN a2enmod rewrite 

