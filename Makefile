DOCKER = docker
DOCKER_COMPOSE = docker-compose
EXEC = $(DOCKER) exec -it back_php_1  sh -c

start: ## Start app
	$(DOCKER_COMPOSE) up -d

stop: ## Stop app
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")

restart:
	$(DOCKER_COMPOSE)  down -v
	$(DOCKER_COMPOSE)  up -d

composer-install:
	$(EXEC)  "compser install"

composer-update:
	$(EXEC) "compser update"

cc:
	$(EXEC) "php bin/console c:c"

php-sh:
	$(DOCKER) exec -it  back_php_1 /bin/sh
db-sh:
	$(DOCKER) exec -it back_database_1 psql -U eleven-kitchen -W eleven-kitchen

database-init: ## Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load

database-drop: ## Create database
	$(EXEC) "php bin/console d:d:d --force --if-exists"

database-create: ## Create database
	$(EXEC) "php bin/console d:d:c --if-not-exists"

database-remove: ## Drop database
	$(EXEC) "php bin/console d:d:d --force --if-exists"

database-migration: ## Make migration
	$(EXEC) "php bin/console make:migration"

migration: ## Alias : database-migration
	$(MAKE) database-migration

database-migrate: ## Migrate migrations
	$(EXEC) "php bin/console d:m:m --no-interaction"

migrate: ## Alias : database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Load fixtures
	$(EXEC) "php bin/console d:f:l --no-interaction"

fixtures: ## Alias : database-fixtures-load
	$(MAKE) database-fixtures-load

purge-db:
	$(EXEC) "bin/console doctrine:schema:drop --full-database --force"

phpcs:
	$(EXEC) "tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src"

