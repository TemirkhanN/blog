FROM php:7.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG APP_ENV=prod
ENV APP_ENV=$APP_ENV
ENV APP_DEBUG=0

RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    zip \
    unzip \
    ssh \
    g++ \
    git \
    curl \
    libcurl4-gnutls-dev \
    libpq-dev \
    libicu-dev

RUN docker-php-ext-install \
    intl \
    curl \
    bcmath \
    gettext \
    pdo_pgsql

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear

RUN docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

COPY ./ /app

WORKDIR /app

RUN usermod -u ${USER_ID} www-data
RUN chown -R www-data:www-data /app /var/www

USER "${USER_ID}:${GROUP_ID}"

RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader
RUN php bin/console cache:clear

CMD php-fpm -F
