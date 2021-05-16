include .env

.PHONY: build
build:
	docker-compose build

.PHONY: up
up:
	make build-frontend
	docker-compose up -d
	docker-compose exec -u www-data backend php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: bash
bash:
	docker-compose exec -u www-data backend bash

.PHONY: down
down:
	docker-compose down

.PHONY: ps
ps:
	docker-compose ps

.PHONY: build-frontend
build-frontend:
	cd ./frontend && npm run build
