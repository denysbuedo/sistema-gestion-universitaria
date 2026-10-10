# Pruebas automatizadas

La suite usa una base PostgreSQL exclusiva llamada `gestion_universitaria_test`. El código de prueba verifica el nombre de la base antes de ejecutar cada caso y se detiene si apunta a otra base.

## Preparación local

1. Crear `gestion_universitaria_test` con propietario `sgu_dev`.
2. Copiar los valores locales necesarios a `.env.test.local`. Este archivo está ignorado por Git. Como mínimo debe definir `DATABASE_URL`, `DB_USER`, `DB_PASS`, `FIXTURE_ADMIN_EMAIL`, `FIXTURE_ADMIN_PASSWORD` y `JWT_PASSPHRASE`.
3. Generar un par JWT exclusivo para pruebas:

```powershell
php bin/console lexik:jwt:generate-keypair --env=test --skip-if-exists
```

4. Aplicar las migraciones y cargar los datos sintéticos:

```powershell
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

No se debe reutilizar la copia de la base real para esta suite ni ejecutar fixtures sobre ella.

## Ejecución

```powershell
php vendor/bin/phpunit --no-coverage
```

Las pruebas cubren arranque, autenticación web, sesiones, cierre de sesión,
autenticación JWT, control de acceso por rol, consistencia básica de entidades y
generación real de archivos XLSX y PDF.

Validaciones complementarias:

```powershell
php bin/console --env=test lint:container
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console --env=test doctrine:schema:validate
composer validate --no-check-publish
```

Las deprecaciones del legado deben conservarse visibles y tratarse en la actualización tecnológica. Para obtener una salida breve durante el diagnóstico se puede definir temporalmente `SYMFONY_DEPRECATIONS_HELPER=disabled`; esto no corrige ni elimina las deprecaciones.
