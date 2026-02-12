.PHONY: help up down build restart install composer test test-unit test-integration test-coverage fixtures shell logs clean

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m # No Color

help: ## Show this help message
	@echo '${BLUE}Available commands:${NC}'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  ${GREEN}%-20s${NC} %s\n", $$1, $$2}'

up: ## Start all containers
	@echo "${BLUE}Starting containers...${NC}"
	docker-compose up -d
	@echo "${GREEN}Containers started successfully!${NC}"

down: ## Stop all containers
	@echo "${BLUE}Stopping containers...${NC}"
	docker-compose down
	@echo "${GREEN}Containers stopped successfully!${NC}"

build: ## Build Docker images
	@echo "${BLUE}Building Docker images...${NC}"
	docker-compose build --no-cache
	@echo "${GREEN}Build completed successfully!${NC}"

restart: down up ## Restart all containers

rebuild: ## Rebuild containers from scratch and setup project
	@echo "${BLUE}Stopping containers...${NC}"
	docker-compose down
	@echo "${BLUE}Building Docker images from scratch...${NC}"
	docker-compose build --no-cache
	@echo "${BLUE}Starting containers...${NC}"
	docker-compose up -d
	@echo "${BLUE}Waiting for services to be ready...${NC}"
	@sleep 10
	@echo "${BLUE}Installing dependencies...${NC}"
	docker-compose exec php composer install --no-interaction
	@echo "${BLUE}Clearing cache...${NC}"
	docker-compose exec php php bin/console cache:clear --no-warmup || true
	@echo "${BLUE}Creating database...${NC}"
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	@echo "${BLUE}Running migrations...${NC}"
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	@echo "${BLUE}Loading fixtures...${NC}"
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
	@echo "${GREEN}Rebuild completed successfully!${NC}"
	@echo "${GREEN}Swagger UI: ${YELLOW}http://localhost/api/doc${NC}"
	@echo "${GREEN}Health Check: ${YELLOW}http://localhost/api/health${NC}"

install: ## Install dependencies and setup project
	@echo "${BLUE}Installing dependencies...${NC}"
	docker-compose exec php composer install
	@echo "${BLUE}Creating database...${NC}"
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	@echo "${BLUE}Running migrations...${NC}"
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	@echo "${GREEN}Installation completed successfully!${NC}"

composer: ## Run composer command (use CMD="your command")
	docker-compose exec php composer $(CMD)

test: ## Run all tests
	@echo "${BLUE}Running all tests...${NC}"
	docker-compose exec php php bin/phpunit
	@echo "${GREEN}Tests completed!${NC}"

test-unit: ## Run unit tests only
	@echo "${BLUE}Running unit tests...${NC}"
	docker-compose exec php php bin/phpunit --testsuite=Unit
	@echo "${GREEN}Unit tests completed!${NC}"

test-integration: ## Run integration tests only
	@echo "${BLUE}Running integration tests...${NC}"
	docker-compose exec php php bin/phpunit --testsuite=Integration
	@echo "${GREEN}Integration tests completed!${NC}"

test-coverage: ## Run tests with coverage report
	@echo "${BLUE}Running tests with coverage...${NC}"
	docker-compose exec php php bin/phpunit --coverage-html coverage
	@echo "${GREEN}Coverage report generated in coverage/ directory${NC}"

fixtures: ## Load database fixtures
	@echo "${BLUE}Loading fixtures...${NC}"
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
	@echo "${GREEN}Fixtures loaded successfully!${NC}"

shell: ## Access PHP container shell
	docker-compose exec php bash

logs: ## Show container logs (use SERVICE=php or SERVICE=nginx)
	docker-compose logs -f $(SERVICE)

clean: ## Clean cache and logs
	@echo "${BLUE}Cleaning cache and logs...${NC}"
	docker-compose exec php php bin/console cache:clear
	@echo "${GREEN}Cache cleared successfully!${NC}"

api-doc: ## Show API documentation URL
	@echo "${GREEN}Swagger UI available at: ${YELLOW}http://localhost/api/doc${NC}"
	@echo "${GREEN}OpenAPI JSON available at: ${YELLOW}http://localhost/api/doc.json${NC}"

