# Estrategia de migraciones

Akademos utiliza migraciones Doctrine versionadas como única vía autorizada
para cambiar el esquema. `doctrine:schema:update --force` y
`doctrine:schema:create` no forman parte del despliegue.

## Línea base

`DoctrineMigrations\Version20261010004147` representa el esquema inicial
validado por los metadatos Doctrine: 164 tablas en 13 esquemas funcionales,
además de las dos tablas del esquema `public`.

El DDL se comparó, sin restaurar ni leer filas, con el respaldo autorizado del
29 de septiembre de 2026. Ambos inventarios contienen las mismas 164 tablas;
la única diferencia nominal es que el respaldo califica explícitamente con
`public.` las dos tablas públicas.

La validación automatizada crea un PostgreSQL vacío, aplica la línea base,
ejecuta su reversión, vuelve a aplicarla y comprueba que el esquema coincide
con los metadatos. El rollback de la línea base elimina todo el esquema y solo
se permite en una base efímera o durante un ensayo de restauración.

## Instalación nueva

Después de crear una base vacía y configurar `DATABASE_URL`:

```bash
php bin/console doctrine:migrations:migrate --env=prod --no-interaction
php bin/console doctrine:schema:validate --env=prod
php bin/console doctrine:migrations:status --env=prod
```

Las fixtures son datos sintéticos de desarrollo y nunca deben cargarse en
producción.

## Adopción en una instalación existente

No se debe ejecutar la migración inicial sobre una base que ya contiene las
tablas de Akademos. El procedimiento requiere una ventana autorizada:

1. Crear un respaldo y demostrar que puede restaurarse.
2. Comparar una copia no productiva con los metadatos mediante
   `doctrine:schema:validate` y resolver cualquier diferencia.
3. Confirmar que las 164 tablas esperadas están presentes.
4. Registrar la línea base como ejecutada, sin ejecutar su SQL:

```bash
php bin/console doctrine:migrations:version \
  'DoctrineMigrations\Version20261010004147' --add --no-interaction
php bin/console doctrine:migrations:status
```

5. Repetir primero el despliegue completo sobre la copia y documentar el
   resultado antes de autorizar producción.

## Cambios posteriores

Cada cambio de entidades debe incluir una migración pequeña y revisada:

```bash
php bin/console doctrine:migrations:diff --formatted
php bin/console doctrine:migrations:migrate --dry-run
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:schema:validate
```

Revisar siempre el SQL generado, especialmente operaciones `DROP`, cambios de
tipo, valores nulos, índices y claves foráneas. Una reversión de código no
revierte automáticamente datos ni esquema: cada cambio debe declarar su plan
de rollback o restauración antes de desplegarse.
