#!/bin/sh
# init.sh

cd /app

composer install --prefer-source --no-interaction

php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:warmup --env=dev

chown -R www-data:www-data ./var

php-fpm -F
