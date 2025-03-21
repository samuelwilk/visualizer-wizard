# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)


## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Database 🗂️️ —————————————————————————————————————————————————————————————
database: ## Create the local dev database
		@make database-create
	 	@make migrations-execute
	 	@make fixtures-load

database-test: ## Create the test database
		@make database-create-test
		#@make database-migrations-execute-test
		#@make database-fixtures-test

database-create: ## Create the local dev database
		$(SYMFONY) doctrine:database:drop --if-exists --force
		$(SYMFONY) doctrine:database:create

database-create-test: ## Creates the test database
		$(SYMFONY) --env=test doctrine:database:drop --if-exists --force
		$(SYMFONY) --env=test doctrine:database:create

fixtures-load: ## Load fixtures
		$(SYMFONY) doctrine:fixtures:load -n --group=dev

migrations-generate: ## Generate doctrine migrations
		$(SYMFONY) make:migration

migrations-execute: ## Execute doctrine migrations
		$(SYMFONY) doctrine:migrations:migrate --no-interaction

migrations-status: ## View migration status
		$(SYMFONY) doctrine:migrations:status

database-fixtures-test: ## Runs the test fixtures
	@docker-compose exec --env=test  php bin/console doctrine:fixtures:load -n --group=test

database-migrations-execute-test: ## Runs the current set of migrations against the DB
	@docker-compose exec --env=test  php bin/console doctrine:migrations:migrate --no-interaction

## —— QA ———————————————————————————————————————————————————————————————————————
qa: tests rector quality phpstan lint-container lint-yaml ## Run all QA tools

lint-container: ## Lint the Symfony container
	@$(PHP_CONT) php bin/console lint:container --env=dev
	@$(PHP_CONT) php bin/console lint:container --env=prod

lint-yaml: ## Lint the yaml files
	@$(PHP_CONT) php bin/console lint:yaml config --env=dev
	@$(PHP_CONT) php bin/console lint:yaml config --env=prod

tests: ## Run tests
	@$(DOCKER_COMP) exec php vendor/bin/phpunit

phpstan: ## Run PHPStan
	@$(DOCKER_COMP) exec php vendor/bin/phpstan analyse --configuration=tools/phpstan/phpstan.neon --memory-limit=4G

rector: ## Run Rector
	@$(DOCKER_COMP) exec php vendor/bin/rector process src --config=tools/rector.php

quality: ## Run Swiss Knife
	@$(DOCKER_COMP) exec php vendor/bin/swiss-knife privatize-constants src tests --exclude-path=src/Contracts
	@$(DOCKER_COMP) exec php vendor/bin/swiss-knife finalize-classes src tests --skip-mocked
	@$(DOCKER_COMP) exec php vendor/bin/swiss-knife check-conflicts .
	@$(DOCKER_COMP) exec php vendor/bin/swiss-knife check-commented-code src --line-limit 5
	@$(DOCKER_COMP) exec php vendor/bin/swiss-knife find-multi-classes src

fix: ## Run PHP-CS-Fixer
	@$(DOCKER_COMP) exec php vendor/bin/php-cs-fixer fix
