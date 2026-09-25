.PHONY: help build up down setup test

APPS = apps/student-platform apps/reporting

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "Each app has its own Makefile with more targets: make -C <app> help"
	@for app in $(APPS); do echo "  $$app"; done

build: ## Build Docker images
	docker compose build

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

setup: build up ## Full setup: build images, start containers, then set up every app
	@for app in $(APPS); do $(MAKE) -C $$app setup || exit 1; done

test: ## Run the tests of every app
	@for app in $(APPS); do $(MAKE) -C $$app test || exit 1; done
