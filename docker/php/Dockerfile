FROM php:7.1-fpm

ARG host_uid
ARG symfony_env
ENV SYMFONY_ENV=$symfony_env

RUN apt-get update; \
	apt-get install -y vim git zip curl sudo ;\
	usermod -u $host_uid www-data; \
	chown -R www-data /var/www

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install bcmath \
    opcache \
    json \
    soap
    pdo \
    pdo_mysql \
    && docker-php-ext-enable opcache

COPY ./php.ini /usr/local/etc/php/conf.d/custom-php.ini

USER www-data

WORKDIR /var/www/civils-app