include .env

.PHONY: build
build:
	docker-compose build

.PHONY: up
up:
	docker-compose up -d

.PHONY: bash
bash:
	docker-compose exec -u www-data backend bash

.PHONY: down
down:
	docker-compose down

.PHONY: ps
ps:
	docker-compose ps
