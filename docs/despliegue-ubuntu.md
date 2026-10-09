# Despliegue de Akademos en Ubuntu

## Alcance y plataforma objetivo

Esta guía define la línea base para un entorno no productivo y, después de su
validación, para producción. Windows se utiliza solamente para desarrollo.

- Ubuntu Server 24.04 LTS, arquitectura `amd64`.
- Nginx y un pool dedicado de PHP 8.3-FPM.
- PostgreSQL en un servidor administrado o nodo separado; el cliente se instala
  en el servidor de aplicación.
- Composer 2.
- Apache FOP y Java para los reportes heredados.
- Código desplegado en `/var/www/akademos/releases/<identificador>` y enlace
  `/var/www/akademos/current` a la versión activa.

Ubuntu 24.04 recibe mantenimiento estándar hasta mayo de 2029. PHP 8.3 es la
versión nativa de esa edición. La aplicación está validada actualmente con PHP
8.1; por eso PHP 8.3 debe pasar esta misma suite en el piloto antes de promoverse.

## Paquetes del sistema

```bash
sudo apt update
sudo apt install --no-install-recommends \
  nginx postgresql-client fop default-jre-headless unzip ca-certificates git \
  php8.3-cli php8.3-fpm php8.3-pgsql php8.3-xml php8.3-gd php8.3-intl \
  php8.3-curl php8.3-zip php8.3-mbstring php8.3-bcmath
```

Composer debe instalarse mediante el procedimiento oficial y verificando la
firma del instalador. No ejecutar Composer como `root` dentro del proyecto.

## Cuenta y estructura

```bash
sudo adduser --system --group --home /var/www/akademos akademos
sudo install -d -o akademos -g www-data -m 0750 /var/www/akademos/releases
sudo install -d -o root -g www-data -m 0750 /etc/akademos/jwt
sudo install -d -o root -g www-data -m 0750 /etc/akademos
```

El código pertenece a `akademos:www-data`. Solo `var/` necesita escritura por
el proceso web. No dar permisos de escritura a `config/`, `src/` ni `vendor/`.

## Secretos y configuración

1. Copiar `deploy/akademos.env.example` a `/etc/akademos/akademos.env`.
2. Sustituir todos los valores de ejemplo y ajustar los IDs funcionales con el
   responsable de Akademos.
3. Proteger el archivo con `root:www-data` y modo `0640`.
4. Guardar las claves JWT en `/etc/akademos/jwt`, con clave privada `0640` y
   clave pública `0644` como máximo.
5. No ejecutar fixtures en producción.

El `DATABASE_URL` debe usar un usuario propio de la aplicación, sin privilegios
de superusuario ni creación de bases. La versión declarada, si se añade a la
URL, debe coincidir exactamente con el servidor PostgreSQL real.

## Preparación de una versión

Ejemplo con el identificador de un commit:

```bash
release_id=848e05d
release_dir="/var/www/akademos/releases/$release_id"

sudo -u akademos git clone --branch dev --single-branch \
  https://github.com/denysbuedo/sistema-gestion-universitaria.git "$release_dir"
cd "$release_dir"
sudo -u akademos git checkout "$release_id"

set -a
source /etc/akademos/akademos.env
set +a

sudo -u akademos --preserve-env composer install \
  --no-dev --classmap-authoritative --no-interaction
sudo install -d -o www-data -g www-data -m 0770 "$release_dir/var"
sudo -u www-data --preserve-env php bin/console cache:clear --env=prod --no-debug
sudo -u www-data --preserve-env php bin/console cache:warmup --env=prod --no-debug
```

No ejecutar `doctrine:schema:update --force`. Hasta establecer la línea base de
migraciones, validar el esquema contra una copia no productiva:

```bash
sudo -u www-data --preserve-env php bin/console doctrine:schema:validate --env=prod
```

## PHP-FPM, systemd y Nginx

```bash
sudo cp deploy/php-fpm/akademos.conf /etc/php/8.3/fpm/pool.d/akademos.conf
sudo install -d /etc/systemd/system/php8.3-fpm.service.d
sudo cp deploy/systemd/php8.3-fpm-akademos.conf \
  /etc/systemd/system/php8.3-fpm.service.d/akademos.conf
sudo cp deploy/nginx/akademos.conf /etc/nginx/sites-available/akademos
sudo ln -s /etc/nginx/sites-available/akademos /etc/nginx/sites-enabled/akademos

sudo systemctl daemon-reload
sudo php-fpm8.3 -t
sudo nginx -t
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

La plantilla Nginx escucha HTTP para facilitar el piloto interno. Producción
debe publicar exclusivamente HTTPS, ya sea terminándolo en Nginx o en el proxy
institucional. No habilitar tráfico de usuarios antes de configurar TLS y las
cabeceras del proxy de confianza.

## Activación y reversión

Realizar primero backup y una prueba de restauración. La activación cambia un
enlace simbólico, lo que permite volver a la versión anterior:

```bash
previous_release="$(readlink -f /var/www/akademos/current || true)"
sudo ln -sfn "$release_dir" /var/www/akademos/current
sudo systemctl reload php8.3-fpm nginx
```

Si falla la comprobación posterior:

```bash
sudo ln -sfn "$previous_release" /var/www/akademos/current
sudo systemctl reload php8.3-fpm nginx
```

Una reversión de código no revierte automáticamente cambios de base de datos.
Cada migración futura debe definir su compatibilidad y procedimiento de retorno.

## Validación

Con el entorno exportado, ejecutar:

```bash
sudo -u www-data --preserve-env bash deploy/check-requirements.sh \
  /var/www/akademos/current
```

Luego verificar desde otra máquina:

- inicio de sesión web y cierre de sesión;
- autenticación JWT y endpoint protegido;
- descarga de un reporte PDF mediante FOP;
- exportación XLSX;
- permisos, logs y ausencia de secretos en respuestas;
- restauración de un backup en el entorno no productivo.

## Operación pendiente de acuerdo institucional

- dominio, certificado y terminación TLS;
- ubicación y versión exacta de PostgreSQL;
- retención, cifrado y destino de backups;
- comandos periódicos autorizados y sus horarios;
- integración de logs con el sistema institucional;
- monitoreo de disponibilidad, disco, PHP-FPM y base de datos;
- ventana de mantenimiento y responsables de aprobación/reversión.

No se programan tareas periódicas hasta revisar individualmente los comandos de
sincronización existentes y acordar su idempotencia, credenciales y frecuencia.

## Validación reproducible con Docker

Desde el entorno de desarrollo puede ejecutarse la misma base Ubuntu 24.04 con
una base PostgreSQL efímera. No utiliza ni modifica las bases locales o reales:

```powershell
docker compose -f deploy/validation/compose.yaml up \
  --build --abort-on-container-exit --exit-code-from app
docker compose -f deploy/validation/compose.yaml down --volumes
```

La imagen valida PHP 8.3, extensiones, Composer, FOP, esquema, fixtures, pruebas,
Nginx y PHP-FPM. El almacenamiento PostgreSQL es `tmpfs` y desaparece al detener
el entorno.

El mismo procedimiento se ejecuta en GitHub Actions mediante
`.github/workflows/ubuntu-validation.yml` en cada actualización de `dev` y en
los pull requests dirigidos a esa rama.
