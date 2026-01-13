FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /etc/secrets

# Copy the credential file
COPY docker/tmp/credential.json /etc/secrets/credential.json

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# setup Xdebug (using build argument for dynamic host IP)
ARG XDEBUG_CLIENT_HOST
RUN echo "xdebug.mode=debug,develop,coverage" > /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/20-xdebug.ini

WORKDIR /var/www/note_react

ENV APP_ENV=dev
ENV APP_DEBUG=1

COPY src/composer.json src/composer.lock* ./src/
RUN cd src && composer install --no-interaction --prefer-dist

COPY . .

EXPOSE 8000

# # Start the application using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/note_react/public"]
