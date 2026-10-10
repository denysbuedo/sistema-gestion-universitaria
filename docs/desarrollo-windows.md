# Entorno de desarrollo en Windows

Esta guía describe la línea base actualmente validada para ejecutar Akademos en
un equipo Windows sin Docker. Los valores reales de credenciales y los backups
de bases de datos deben permanecer fuera del repositorio.

## Requisitos

- Windows 10 u 11 de 64 bits.
- PHP 8.1 de 64 bits para reproducir el estado actual del legado.
- Composer 2.
- PostgreSQL.
- Symfony CLI, o el servidor integrado de PHP para desarrollo.
- Java 11 o superior para las funciones basadas en Apache FOP.
- Git.

Extensiones PHP requeridas para la instalación actual:

```text
ctype
curl
dom
fileinfo
gd
iconv
intl
mbstring
openssl
pdo
pdo_pgsql
sodium
xml
xmlreader
xmlwriter
zip
```

El monolito necesita más memoria que la configuración predeterminada de algunas
distribuciones de PHP. Para desarrollo se ha validado:

```ini
memory_limit = 512M
```

PHP 8.1 es solamente la línea base reproducible. La versión objetivo moderna se
definirá y validará dentro de la iniciativa de actualización tecnológica.

## Configuración local

Instalar las dependencias bloqueadas:

```powershell
composer install
```

Crear un archivo `.env` mínimo, ignorado por Git:

```dotenv
APP_ENV=dev
```

Copiar `.env.example` como `.env.local` y sustituir exclusivamente los valores
locales. La conexión principal debe utilizar PostgreSQL:

```dotenv
DATABASE_URL="postgresql://usuario:clave@127.0.0.1:5432/gestion_universitaria"
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=gestion_universitaria
DB_USER=usuario
DB_PASS=clave
```

Definir también credenciales exclusivas para el administrador que crean las
fixtures. No reutilizar cuentas ni contraseñas de otros entornos:

```dotenv
FIXTURE_ADMIN_EMAIL=admin@example.invalid
FIXTURE_ADMIN_PASSWORD=una-clave-local-distinta
```

No ejecutar `composer dump-env` durante el desarrollo cotidiano. Los archivos
`.env.local` y `.env.local.php` son locales y están excluidos del repositorio.

## Claves JWT

Crear `config/jwt/` si no existe y generar un par exclusivo para desarrollo:

```powershell
php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

En Windows puede ser necesario definir `OPENSSL_CONF` con la ruta del archivo
`openssl.cnf` de la instalación de PHP. Las claves generadas están ignoradas por
Git y nunca deben reutilizar las claves de producción.

## Base de datos

La aplicación usa PostgreSQL. Para desarrollo existen dos alternativas:

1. Restaurar una copia no productiva, autorizada y debidamente anonimizada.
2. Crear una base vacía, aplicar las migraciones y cargar las fixtures.

La segunda alternativa permite recorrer la aplicación con datos sintéticos:

```powershell
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

La estrategia de instalaciones nuevas, adopción sobre bases existentes y
rollback está documentada en `docs/migraciones.md`.

Los backups reales pueden contener datos personales, hashes de contraseñas,
trazas y otra información restringida. Deben almacenarse fuera del repositorio,
con acceso limitado, y no utilizarse en equipos o ambientes no autorizados.

## Validación

Comprobar la configuración antes de iniciar el servidor:

```powershell
php bin/console about
php bin/console lint:container
php bin/console doctrine:schema:validate
```

`doctrine:schema:validate` debe informar mapeos correctos y esquema
sincronizado antes de iniciar el servidor.

## Ejecución

Con Symfony CLI:

```powershell
symfony server:start --no-tls --port=8000
```

Alternativamente:

```powershell
php -S 127.0.0.1:8000 -t public
```

La aplicación queda disponible en:

```text
http://127.0.0.1:8000
```

Para detener Symfony CLI:

```powershell
symfony server:stop
```

## Reglas operativas

- Desarrollar sobre `dev`; `main` representa la línea estable.
- No versionar `.env`, `.env.local`, claves JWT, backups ni credenciales.
- No usar una base de producción como base de desarrollo cotidiana.
- No ejecutar `doctrine:schema:update --force` en producción.
- Crear pruebas antes de refactorizaciones o actualizaciones amplias.
- Versionar cada cambio de esquema mediante migraciones revisadas.
