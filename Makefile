include .env

BACKEND_CLI=docker compose exec -u www-data backend

.PHONY: build
build:
	docker compose build

.PHONY: start
start:
	docker compose up -d

.PHONY: bash
bash:
	$(BACKEND_CLI) bash

.PHONY: stop
stop:
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
	$(BACKEND_CLI) php ./vendor/bin/deptrac analyze
	$(BACKEND_CLI) php ./vendor/bin/phpunit

# Live env

.PHONY: build-prod
build-prod:
	RELEASE_VERSION=$(git rev-parse --short HEAD) docker compose -f docker-compose.prod.yaml build

.PHONY: start-prod
start-prod:
	docker compose -f docker-compose.prod.yaml up -d
