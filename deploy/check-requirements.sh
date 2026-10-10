#!/usr/bin/env bash

set -euo pipefail

APP_DIR="${1:-/var/www/akademos/current}"

required_commands=(php composer psql nginx java fop)
required_variables=(APP_ENV APP_SECRET DATABASE_URL JWT_SECRET_KEY JWT_PUBLIC_KEY JWT_PASSPHRASE FOP_EXECUTABLE)

for command_name in "${required_commands[@]}"; do
    command -v "$command_name" >/dev/null || {
        echo "Falta el comando requerido: $command_name" >&2
        exit 1
    }
done

for variable_name in "${required_variables[@]}"; do
    if [[ -z "${!variable_name:-}" ]]; then
        echo "Falta la variable requerida: $variable_name" >&2
        exit 1
    fi
done

[[ "$APP_ENV" == "prod" ]] || {
    echo "APP_ENV debe ser prod." >&2
    exit 1
}

[[ -r "$JWT_SECRET_KEY" ]] || {
    echo "La clave JWT privada no es legible." >&2
    exit 1
}

[[ -r "$JWT_PUBLIC_KEY" ]] || {
    echo "La clave JWT pública no es legible." >&2
    exit 1
}

cd "$APP_DIR"

composer check-platform-reqs --no-dev
php bin/console about --env=prod
php bin/console lint:container --env=prod
php bin/console doctrine:query:sql 'SELECT current_database(), current_user' --env=prod
fop -version

echo "Comprobaciones de despliegue completadas."
