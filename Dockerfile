FROM public.ecr.aws/docker/library/php:8.2.1-apache

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install PHP extensions
RUN apt-get update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-install intl

RUN apt-get update && apt-get install -y nano zip unzip && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get upgrade -y

COPY ./000-default.conf /etc/apache2/sites-enabled/
RUN apt install ssl-cert;
RUN a2enmod ssl

# Shut off Apache Server Signature
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# uncomment these lines if you want error reporting
RUN echo "expose_php = off" >> "$PHP_INI_DIR/php.ini-production"
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ADD ./src /var/www/html
RUN chown -R www-data:www-data /var/www/html/
RUN export TERM=xterm
