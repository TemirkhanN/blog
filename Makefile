include .env

BACKEND_CLI=docker compose exec -u www-data backend

.PHONY: build
build:
	docker compose build

.PHONY: up
up:
	docker compose up -d
	make composer
	make migrate

.PHONY: bash
bash:
	$(BACKEND_CLI) bash

.PHONY: down
down:
	docker compose down

.PHONY: ps
ps:
	docker compose ps

.PHONY: migrate
migrate:
	$(BACKEND_CLI) php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: composer
composer:
	$(BACKEND_CLI) composer install --no-interaction

.PHONY: code-check
code-check:
	$(BACKEND_CLI) php ./vendor/bin/phpcs
	$(BACKEND_CLI) php -d memory_limit=512M ./vendor/bin/phpstan
	$(BACKEND_CLI) php ./vendor/bin/phpunit

