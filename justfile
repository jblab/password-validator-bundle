# This file is part of the Jblab PasswordValidatorBundle package.
# Copyright (c) 2023-2025 Jblab <https://jblab.io/>
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

set quiet
set ignore-comments

[doc("Display this help and exit")]
help:
    echo >&2 "\nJblab Password Validator Bundle\n\nUsage:\n just RECIPE [PARAMETERS]\n"
    just --list --unsorted
    echo >&2 ""

[doc("Run project's tests suits on different PHP versions")]
test: (_test "8.1") (_test "8.2") (_test "8.3") (_test "8.4") (_test "8.5")

[doc("Run PHPStan on the project")]
phpstan: (_run "8.1" "vendor/bin/simple-phpunit --version && composer tools:upgrade:phpstan && composer tools:run:phpstan")

[doc("Run PHP Parallel Lint on the project")]
php-lint: (_run "8.1" "composer tools:upgrade:php-lint && composer tools:run:php-lint")

[doc("Run PHP CodeSniffer on the project")]
phpcs: (_run "8.1" "composer tools:upgrade:phpcs && composer tools:run:phpcs")

[doc("Run PHPStan, PHP Parallel Lint and PHP CodeSniffer on the project")]
run: (_run "8.1" "vendor/bin/simple-phpunit --version && composer tools:upgrade && composer tools:run")

# ----------------------------------------------------------------------------------------------------------------------
# Helpers
# ----------------------------------------------------------------------------------------------------------------------

[private]
_build +version:
    docker build --tag jblab-password-validator-bundle:{{version}} --target tests .

[private]
_run version command: (_build version)
    docker run --rm jblab-password-validator-bundle:{{version}} bash -c "{{command}}"

[private]
_test version: && (_build version) (_run version "vendor/bin/simple-phpunit")
    echo >&2 "Running tests on PHP {{version}}..."

