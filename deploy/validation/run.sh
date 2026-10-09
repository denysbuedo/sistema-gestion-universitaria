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
audit_report="$(mktemp)"
composer audit --locked --format=json > "${audit_report}" || true
cat "${audit_report}"
php -r '$audit = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); exit(empty($audit["advisories"]) ? 0 : 1);' "${audit_report}"
rm -f "${audit_report}"

php-fpm8.3 -t
nginx -t

echo "Validación Ubuntu completada."
