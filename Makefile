# This file is part of the Jblab PasswordValidatorBundle package.
# Copyright (c) 2023-2025 Jblab <https://jblab.io/>
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
SHELL:=/bin/bash
.SILENT:

.PHONY: help tests phpstan php-lint phpcs run-tools

help: ## Display this help and exit
	@egrep -h '\s##\s' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m  %-30s\033[0m %s\n", $$1, $$2}'

tests: ## Run project's tests suits on different PHP versions
	for v in "8.1" "8.2" "8.3" "8.4"; do \
		docker build --tag jblab-password-validator-bundle:$$v  --build-arg PHP_VERSION=$$v --build-arg target=tests .;  \
		docker run --rm jblab-password-validator-bundle:$$v vendor/bin/simple-phpunit; \
	done; \

phpstan: ## Run PHPStan on the project
	docker build --tag jblab-password-validator-bundle:8.1  --build-arg PHP_VERSION=8.1 --build-arg target=tests .;  \
	docker run --rm jblab-password-validator-bundle:8.1 bash -c "vendor/bin/simple-phpunit --version && composer tools:upgrade:phpstan && composer tools:run:phpstan";

php-lint: ## Run PHP Parallel Lint on the project
	docker build --tag jblab-password-validator-bundle:8.1  --build-arg PHP_VERSION=8.1 --build-arg target=tests .;  \
	docker run --rm jblab-password-validator-bundle:8.1 bash -c "composer tools:upgrade:php-lint && composer tools:run:php-lint";

phpcs: ## Run PHP CodeSniffer on the project
	docker build --tag jblab-password-validator-bundle:8.1  --build-arg PHP_VERSION=8.1 --build-arg target=tests .;  \
	docker run --rm jblab-password-validator-bundle:8.1 bash -c "composer tools:upgrade:phpcs && composer tools:run:phpcs";

run-tools: ## Run PHPStan, PHP Parallel Lint and PHP CodeSniffer on the project
	docker build --tag jblab-password-validator-bundle:8.1  --build-arg PHP_VERSION=8.1 --build-arg target=tests .;  \
	docker run --rm jblab-password-validator-bundle:8.1 bash -c "vendor/bin/simple-phpunit --version && composer tools:upgrade && composer tools:run";