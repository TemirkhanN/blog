FROM php:8.3-fpm AS base_container

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG APP_ENV=prod
ENV APP_ENV=$APP_ENV
ENV APP_DEBUG=0

RUN apt-get update && apt-get install -y --no-install-recommends \
apt-utils zip unzip ssh g++ git curl libcurl4-gnutls-dev libpq-dev libicu-dev \
&& docker-php-ext-install intl curl bcmath gettext pdo_pgsql \
&& pecl install -o -f redis \
&& docker-php-ext-enable redis \
&& rm -rf /tmp/pear

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer 

WORKDIR /app

RUN usermod -u ${USER_ID} www-data \
 && chown -R www-data:www-data /app /var/www

CMD ["php-fpm", "-F"]

FROM base_container AS dev_container

RUN pecl install xdebug

USER "${USER_ID}:${GROUP_ID}"

FROM base_container AS prod_container
ARG RELEASE_VERSION
ENV RELEASE_VERSION=${RELEASE_VERSION}
LABEL release.version=${RELEASE_VERSION}

USER "${USER_ID}:${GROUP_ID}"

COPY --chown=www-data:www-data ./ /app
RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader \
 && php bin/console cache:clear
