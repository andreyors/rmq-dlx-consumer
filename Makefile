.RECIPEPREFIX +=

.SILENT:

COMPOSE ?= docker-compose
EXEC_PHP = $(COMPOSE) exec php php
COMPOSER = $(EXEC_PHP) composer

.DEFAULT_GOAL := help

composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

.PHONY: help
help:
	grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "[32m%-12s[0m %s\n", $$1, $$2}'

.PHONY: start
start: ## Start Docker
	$(COMPOSE) up -d --remove-orphans --build

.PHONY: stop
stop: ## Stop Docker
	$(COMPOSE) down --remove-orphans --volumes

.PHONY: consumer
consumer: ## Start consumer
	$(EXEC_PHP) consumer.php

.PHONY: publisher
publisher: ## Run publisher
    $(EXEC_PHP) publisher.php
