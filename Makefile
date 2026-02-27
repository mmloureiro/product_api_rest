.PHONY: help setup install-local setup-db test test-unit test-integration test-coverage test-quick \
		fixtures migrate migration-generate migration-status db-create db-drop db-reset \
		serve serve-symfony cache-clear clean lint check routes status info api-doc \
		docker-up docker-down docker-build docker-restart docker-rebuild docker-install docker-shell \
		docker-logs docker-test docker-test-coverage docker-fixtures docker-migrate \
		up down build restart rebuild shell

# Default target
.DEFAULT_GOAL := help

# Docker & PHP Configuration
PHP_CONT = php
DOCKER_COMPOSE = docker-compose -f docker/docker-compose.yml
CONSOLE = php bin/console
SYMFONY = $(DOCKER_COMPOSE) exec $(PHP_CONT) $(CONSOLE)

help: ## 📋 Show this help message
	@echo ''
	@echo '\033[0;34m════════════════════════════════════════════════════════════════\033[0m'
	@echo '\033[0;32m  Product API - Available Commands\033[0m'
	@echo '\033[0;34m════════════════════════════════════════════════════════════════\033[0m'
	@echo ''
	@echo '\033[0;33m🚀 Setup & Installation:\033[0m'
	@grep -E '^[a-zA-Z_-]+:.*?## .*🚀.*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;32m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ''
	@echo '\033[0;33m🧪 Testing:\033[0m'
	@grep -E '^[a-zA-Z_-]+:.*?## .*🧪.*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;32m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ''
	@echo '\033[0;33m💾 Database:\033[0m'
	@grep -E '^[a-zA-Z_-]+:.*?## .*💾.*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;32m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ''
	@echo '\033[0;33m🐳 Docker:\033[0m'
	@grep -E '^[a-zA-Z_-]+:.*?## .*🐳.*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;32m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ''
	@echo '\033[0;33m🛠️  Development:\033[0m'
	@grep -E '^[a-zA-Z_-]+:.*?## .*🛠️.*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;32m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ''
	@echo '\033[0;34m════════════════════════════════════════════════════════════════\033[0m'
	@echo ''

##
## —— 🐳 Docker Commands ———————————————————————————————————————————————————————
##

docker-up: ## 🐳 Start all Docker containers
	@echo "\033[0;34m🐳 Starting containers...\033[0m"
	@$(DOCKER_COMPOSE) up -d
	@echo "\033[0;32m✅ Containers started successfully!\033[0m"

docker-down: ## 🐳 Stop all Docker containers
	@echo "\033[0;34m🐳 Stopping containers...\033[0m"
	@$(DOCKER_COMPOSE) down
	@echo "\033[0;32m✅ Containers stopped successfully!\033[0m"

docker-build: ## 🐳 Build Docker images
	@echo "\033[0;34m🐳 Building Docker images...\033[0m"
	@$(DOCKER_COMPOSE) build --no-cache
	@echo "\033[0;32m✅ Build completed successfully!\033[0m"

docker-rebuild: ## 🚀 Rebuild containers from scratch and setup project
	@echo "\033[0;34m🐳 Stopping containers...\033[0m"
	@$(DOCKER_COMPOSE) down -v --remove-orphans
	@echo "\033[0;34m🐳 Building Docker images from scratch...\033[0m"
	@$(DOCKER_COMPOSE) build --no-cache
	@echo "\033[0;34m🐳 Starting containers...\033[0m"
	@$(DOCKER_COMPOSE) up -d
	@echo "\033[0;34m⏳ Waiting for database to be ready...\033[0m"
	@sleep 5
	@echo "\033[0;34m💾 Setup Development Database...\033[0m"
	@$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction
	@$(SYMFONY) doctrine:fixtures:load --no-interaction
	@echo "\033[0;34m💾 Setup Test Database...\033[0m"
	@$(SYMFONY) doctrine:database:create --if-not-exists --env=test
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction --env=test
	@echo "\033[0;32m✅ Rebuild completed successfully!\033[0m"
	@make api-doc

docker-shell: ## 🐳 Access PHP Docker container shell
	@$(DOCKER_COMPOSE) exec $(PHP_CONT) sh

##
## —— 🧪 Testing (Docker) —————————————————————————————————————————————————————
##

test: ## 🧪 Run all tests inside Docker
	@echo "\033[0;34m🧪 Running all tests in Docker...\033[0m"
	@$(DOCKER_COMPOSE) exec $(PHP_CONT) php bin/phpunit --testdox --colors=always

test-coverage: ## 🧪 Run tests with coverage in Docker
	@echo "\033[0;34m🧪 Generating coverage report in Docker...\033[0m"
	@$(DOCKER_COMPOSE) exec -e XDEBUG_MODE=coverage $(PHP_CONT) php bin/phpunit --coverage-html var/coverage --testdox
	@echo "\033[0;32m✅ Coverage report generated in var/coverage/index.html\033[0m"

##
## —— 💾 Database (Docker) —————————————————————————————————————————————————————
##

fixtures: ## 💾 Load database fixtures in Docker
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

migrate: ## 💾 Run database migrations in Docker
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

db-reset: ## 💾 Reset database in Docker (⚠️  destructive)
	@echo "\033[0;31m⚠️  Resetting database in Docker...\033[0m"
	@$(SYMFONY) doctrine:database:drop --force --if-exists
	@$(SYMFONY) doctrine:database:create
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction
	@$(SYMFONY) doctrine:fixtures:load --no-interaction
	@echo "\033[0;32m✅ Database reset completed!\033[0m"

##
## —— 🛠️  Development ————————————————————————————————————————————————————————
##

cache-clear: ## 🛠️ Clear application cache
	@$(SYMFONY) cache:clear

routes: ## 🛠️ Show all available routes
	@$(SYMFONY) debug:router

api-doc: ## 🛠️ Show API documentation URLs
	@echo ''
	@echo "\033[0;32m📚 API Documentation:\033[0m"
	@echo "  \033[0;33mSwagger UI:    \033[0;34mhttp://localhost:8000/api/doc\033[0m"
	@echo "  \033[0;33mAPI Base URL:  \033[0;34mhttp://localhost:8000/api/products\033[0m"
	@echo ''

# Shortcuts
up: docker-up
down: docker-down
build: docker-build
init: docker-rebuild
shell: docker-shell

.SILENT: help api-doc
