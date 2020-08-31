FROM php:7.3-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

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
    mbstring \
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
RUN chown -R www-data:www-data /app

USER "${USER_ID}:${GROUP_ID}"

RUN composer install --prefer-source --no-interaction
RUN php bin/console cache:warmup --env=prod

CMD php-fpm -F
