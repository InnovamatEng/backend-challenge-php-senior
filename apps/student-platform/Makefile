.PHONY: help setup seed migrate migration-diff test test-unit test-integration test-behat shell logs

ROOT    := $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/../..)
COMPOSE := docker compose -f $(ROOT)/docker-compose.yml
PHP     := $(COMPOSE) exec platform_php

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

setup: ## Install deps, generate JWT keys, run migrations, load fixtures (containers must be running)
	@echo "⏳ Waiting for MySQL..."
	@sleep 5
	$(PHP) composer install --no-interaction
	$(PHP) php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction
	$(PHP) php bin/console doctrine:database:create --env=test --if-not-exists
	$(PHP) php bin/console doctrine:migrations:migrate --env=test --no-interaction
	@echo "✅ Student platform ready! Backend: http://localhost:8080 | Frontend: http://localhost:3000"

seed: ## Reload fixtures (reset data)
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

migrate: ## Run pending migrations
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction

migration-diff: ## Generate a new migration based on entity changes
	$(PHP) php bin/console doctrine:migrations:diff

test: test-unit test-integration test-behat ## Run all tests

test-unit: ## Run unit tests only
	$(PHP) php vendor/bin/phpunit --testsuite Unit

test-integration: ## Run integration tests only
	$(PHP) php vendor/bin/phpunit --testsuite Integration

test-behat: ## Run Behat acceptance tests
	$(PHP) php vendor/bin/behat --config=behat.yml.dist

shell: ## Open a shell in the PHP container
	$(PHP) bash

logs: ## Tail PHP container logs
	$(COMPOSE) logs -f platform_php
