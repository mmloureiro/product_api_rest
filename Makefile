.PHONY: help setup install-local setup-db test test-unit test-integration test-coverage test-quick \
		fixtures migrate migration-generate migration-status db-create db-drop db-reset \
		serve serve-symfony cache-clear clean lint check routes status info api-doc \
		docker-up docker-down docker-build docker-restart docker-rebuild docker-install docker-shell \
		docker-logs docker-composer docker-test docker-test-coverage docker-fixtures docker-migrate \
		up down build restart rebuild shell

# Default target
.DEFAULT_GOAL := help

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
## —— 🚀 Setup & Installation (Local - Sin Docker) ————————————————————————————
##

setup: install-local setup-db fixtures ## 🚀 Complete local setup (install + database + fixtures)
	@echo ''
	@echo '\033[0;32m╔════════════════════════════════════════════════════════════╗\033[0m'
	@echo '\033[0;32m║  ✅ Project setup completed successfully!                 ║\033[0m'
	@echo '\033[0;32m╚════════════════════════════════════════════════════════════╝\033[0m'
	@echo ''
	@echo '\033[0;33m📚 Next steps:\033[0m'
	@echo '  \033[0;34m1.\033[0m Start server: \033[0;32mmake serve\033[0m'
	@echo '  \033[0;34m2.\033[0m Run tests:    \033[0;32mmake test\033[0m'
	@echo '  \033[0;34m3.\033[0m View API:     \033[0;32mhttp://localhost:8000/api/doc\033[0m'
	@echo ''

install-local: ## 🚀 Install dependencies locally (without Docker)
	@echo "\033[0;34m📦 Installing Composer dependencies...\033[0m"
	@composer install --no-interaction --prefer-dist --optimize-autoloader
	@echo "\033[0;32m✅ Dependencies installed successfully!\033[0m"

setup-db: ## 🚀 Setup database and run migrations
	@echo "\033[0;34m💾 Setting up database...\033[0m"
	@mkdir -p var
	@php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@echo "\033[0;32m✅ Database setup completed!\033[0m"

setup-test-db: ## 🚀 Setup test database
	@echo "\033[0;34m💾 Setting up test database...\033[0m"
	@mkdir -p var
	@php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "\033[0;32m✅ Test database ready!\033[0m"

##
## —— 🧪 Testing (Local) ——————————————————————————————————————————————————————
##

test: ## 🧪 Run all tests with detailed output
	@echo "\033[0;34m🧪 Running all tests...\033[0m"
	@php bin/phpunit --testdox --colors=always
	@echo "\033[0;32m✅ All tests passed!\033[0m"

test-unit: ## 🧪 Run unit tests only
	@echo "\033[0;34m🧪 Running unit tests...\033[0m"
	@php bin/phpunit tests/Unit --testdox --colors=always
	@echo "\033[0;32m✅ Unit tests completed!\033[0m"

test-integration: ## 🧪 Run integration tests only
	@echo "\033[0;34m🧪 Running integration tests...\033[0m"
	@php bin/phpunit tests/Integration --testdox --colors=always
	@echo "\033[0;32m✅ Integration tests completed!\033[0m"

test-coverage: ## 🧪 Run tests with HTML coverage report
	@echo "\033[0;34m🧪 Generating coverage report...\033[0m"
	@XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage --testdox
	@echo "\033[0;32m✅ Coverage report generated!\033[0m"
	@echo "\033[0;33m📊 View report: \033[0;34mopen var/coverage/index.html\033[0m"

test-quick: ## 🧪 Run tests without coverage (faster)
	@php bin/phpunit --no-coverage

##
## —— 💾 Database (Docker) ——————————————————————————————————————————————————————
##

fixtures: ## 💾 Load database fixtures in Docker
	@echo "\033[0;34m💾 Loading fixtures in Docker...\033[0m"
	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction --env=dev
	@echo "\033[0;32m✅ Fixtures loaded successfully!\033[0m"

migrate: ## 💾 Run database migrations in Docker
	@echo "\033[0;34m💾 Running migrations in Docker...\033[0m"
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	@echo "\033[0;32m✅ Migrations completed!\033[0m"

migration-generate: ## 💾 Generate a new migration file in Docker
	@echo "\033[0;34m💾 Generating migration in Docker...\033[0m"
	@docker-compose exec php php bin/console make:migration
	@echo "\033[0;32m✅ Migration file created!\033[0m"

migration-status: ## 💾 Show migration status in Docker
	@docker-compose exec php php bin/console doctrine:migrations:status

db-reset: ## 💾 Reset database in Docker (⚠️  destructive)
	@echo "\033[0;31m⚠️  Resetting database in Docker...\033[0m"
	@docker-compose exec php rm -f var/data.db var/test_data.db
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction --env=dev
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "\033[0;32m✅ Database reset completed!\033[0m"

##
## —— 💾 Database (Local) ——————————————————————————————————————————————————————
##

fixtures-local: ## 💾 Load database fixtures locally
	@echo "\033[0;34m💾 Loading fixtures...\033[0m"
	@php bin/console doctrine:fixtures:load --no-interaction --env=dev
	@echo "\033[0;32m✅ Fixtures loaded successfully!\033[0m"

db-create: ## 💾 Create database locally
	@echo "\033[0;34m💾 Creating database...\033[0m"
	@mkdir -p var
	@touch var/data.db
	@echo "\033[0;32m✅ Database created!\033[0m"

db-drop: ## 💾 Drop database locally (⚠️  destructive)
	@echo "\033[0;31m⚠️  Dropping database...\033[0m"
	@rm -f var/data.db var/test_data.db
	@echo "\033[0;33mDatabase dropped!\033[0m"


##
## —— 🐳 Docker Commands ———————————————————————————————————————————————————————
##

docker-up: ## 🐳 Start all Docker containers
	@echo "\033[0;34m🐳 Starting containers...\033[0m"
	@docker-compose up -d
	@echo "\033[0;32m✅ Containers started successfully!\033[0m"

docker-down: ## 🐳 Stop all Docker containers
	@echo "\033[0;34m🐳 Stopping containers...\033[0m"
	@docker-compose down
	@echo "\033[0;32m✅ Containers stopped successfully!\033[0m"

docker-build: ## 🐳 Build Docker images
	@echo "\033[0;34m🐳 Building Docker images...\033[0m"
	@docker-compose build --no-cache
	@echo "\033[0;32m✅ Build completed successfully!\033[0m"

docker-restart: docker-down docker-up ## 🐳 Restart all Docker containers

docker-rebuild: ## 🐳 Rebuild containers from scratch and setup project
	@echo "\033[0;34m🐳 Stopping containers...\033[0m"
	@docker-compose down
	@echo "\033[0;34m🐳 Building Docker images from scratch...\033[0m"
	@docker-compose build --no-cache
	@echo "\033[0;34m🐳 Starting containers...\033[0m"
	@docker-compose up -d
	@echo "\033[0;34m⏳ Waiting for services to be ready...\033[0m"
	@sleep 10
	@echo "\033[0;34m📦 Installing dependencies...\033[0m"
	@docker-compose exec php composer install --no-interaction
	@echo "\033[0;34m💾 Running migrations...\033[0m"
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@echo "\033[0;34m💾 Loading fixtures...\033[0m"
	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction --env=dev
	@echo "\033[0;34m💾 Setup Test Database...\033[0m"
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "\033[0;32m✅ Rebuild completed successfully!\033[0m"
	@echo ''
	@echo "\033[0;32m📚 Services available:\033[0m"
	@echo "  \033[0;33mSwagger UI:    \033[0;34mhttp://localhost/api/doc\033[0m"
	@echo "  \033[0;33mAPI Endpoint:  \033[0;34mhttp://localhost/api/products\033[0m"

docker-install: ## 🐳 Install dependencies in Docker container and setup
	@echo "\033[0;34m📦 Installing dependencies in Docker...\033[0m"
	@docker-compose exec php composer install --no-interaction
	@echo "\033[0;34m💾 Setup Development Database...\033[0m"
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction --env=dev
	@echo "\033[0;34m💾 Setup Test Database...\033[0m"
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "\033[0;32m✅ Installation completed successfully!\033[0m"
	@echo "\033[0;33m💡 Run tests with: \033[0;32mmake docker-test\033[0m"

docker-shell: ## 🐳 Access PHP Docker container shell
	@docker-compose exec php bash


##
## —— 🛠️  Development (Local) ——————————————————————————————————————————————————
##

cache-clear: ## 🛠️ Clear application cache
	@echo "\033[0;34m🧹 Clearing cache...\033[0m"
	@php bin/console cache:clear
	@echo "\033[0;32m✅ Cache cleared!\033[0m"

clean: ## 🛠️ Clean cache, logs and temporary files
	@echo "\033[0;34m🧹 Cleaning project...\033[0m"
	@rm -rf var/cache/* var/log/* var/coverage/*
	@echo "\033[0;32m✅ Project cleaned!\033[0m"

routes: ## 🛠️ Show all available routes in Docker
	@docker-compose exec php php bin/console debug:router

status: ## 🛠️ Show project status
	@echo ''
	@echo "\033[0;32m📊 Project Status:\033[0m"
	@echo ''
	@echo "\033[0;33mDocker Containers:\033[0m"
	@docker-compose ps
	@echo ''
	@echo "\033[0;33mDatabase:\033[0m"
	@if [ -f var/data.db ]; then echo "  \033[0;32m✅ Development database exists\033[0m"; else echo "  \033[0;33m⚠️  Development database missing\033[0m"; fi
	@if [ -f var/test_data.db ]; then echo "  \033[0;32m✅ Test database exists\033[0m"; else echo "  \033[0;33m⚠️  Test database missing\033[0m"; fi
	@echo ''

api-doc: ## 🛠️ Show API documentation URLs
	@echo ''
	@echo "\033[0;32m📚 API Documentation:\033[0m"
	@echo "  \033[0;33mSwagger UI:    \033[0;34mhttp://localhost/api/doc\033[0m"
	@echo "  \033[0;33mOpenAPI JSON:  \033[0;34mhttp://localhost/api/doc.json\033[0m"
	@echo "  \033[0;33mAPI Base URL:  \033[0;34mhttp://localhost/api/products\033[0m"
	@echo ''

info: status api-doc ## 🛠️ Show project information

##
## —— 📦 Shortcuts & Aliases ———————————————————————————————————————————————————
##

# Docker shortcuts (aliases)
up: docker-up
down: docker-down
build: docker-build
restart: docker-restart
rebuild: docker-rebuild
shell: docker-shell

# Make silent for better output
.SILENT: help status api-doc info
