#!/usr/bin/env bash

set -euo pipefail

php --version
composer --version
psql --version
fop -version

composer check-platform-reqs
php bin/console lexik:jwt:generate-keypair --env=test --skip-if-exists
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test --no-interaction

php vendor/bin/phpunit --no-coverage --do-not-cache-result
php bin/console lint:container --env=test
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console doctrine:schema:validate --env=test
composer audit --locked --abandoned=report

php-fpm8.3 -t
nginx -t

echo "Validación Ubuntu completada."
