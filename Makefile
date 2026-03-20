.PHONY: help build up down setup seed test test-unit test-integration test-behat shell logs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker images
	docker compose build

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

setup: build up ## Full setup: build images, install deps, generate JWT keys, run migrations, load fixtures
	@echo "⏳ Waiting for MySQL..."
	@sleep 5
	docker compose exec php composer install --no-interaction
	docker compose exec php php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
	docker compose exec php php bin/console doctrine:database:create --env=test --if-not-exists
	docker compose exec php php bin/console doctrine:migrations:migrate --env=test --no-interaction
	@echo "✅ Setup complete! Backend: http://localhost:8080 | Frontend: http://localhost:3000"

seed: ## Reload fixtures (reset data)
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

migrate: ## Run pending migrations
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

migration-diff: ## Generate a new migration based on entity changes
	docker compose exec php php bin/console doctrine:migrations:diff

test: test-unit test-integration test-behat ## Run all tests

test-unit: ## Run unit tests only
	docker compose exec php php vendor/bin/phpunit --testsuite Unit

test-integration: ## Run integration tests only
	docker compose exec php php vendor/bin/phpunit --testsuite Integration

test-behat: ## Run Behat acceptance tests
	docker compose exec php php vendor/bin/behat --config=behat.yml.dist

shell: ## Open a shell in the PHP container
	docker compose exec php bash

logs: ## Tail PHP container logs
	docker compose logs -f php
