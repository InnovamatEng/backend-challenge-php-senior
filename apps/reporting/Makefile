.PHONY: help setup migrate migration-diff test shell logs

ROOT    := $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/../..)
COMPOSE := docker compose -f $(ROOT)/docker-compose.yml
PHP     := $(COMPOSE) exec reporting_php

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

setup: ## Install deps, create the test database, run migrations (containers must be running)
	$(PHP) composer install --no-interaction
	$(COMPOSE) exec reporting_mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS reporting_test; GRANT ALL PRIVILEGES ON reporting_test.* TO 'reporting'@'%';"
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) php bin/console doctrine:migrations:migrate --env=test --no-interaction
	@echo "✅ Reporting service ready! http://localhost:8081"

migrate: ## Run pending migrations
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction

migration-diff: ## Generate a new migration based on entity changes
	$(PHP) php bin/console doctrine:migrations:diff

test: ## Run all tests
	$(PHP) php vendor/bin/phpunit

shell: ## Open a shell in the PHP container
	$(PHP) bash

logs: ## Tail PHP container logs
	$(COMPOSE) logs -f reporting_php
