# Procedimiento de corte hacia la nueva VM

## Principio de seguridad

La instalación anterior continúa siendo la fuente de verdad hasta que comience
la ventana de corte. No se sobrescribirá la base validada de la VM nueva: el
volcado final se restaurará en una base nueva, lo que permite volver a la base
y al release anteriores sin reconstruirlos bajo presión.

No ejecutar este procedimiento sin responsables funcional, técnico y de
infraestructura presentes, una ventana comunicada y un criterio explícito de
abortar o continuar.

## Estado comprobado antes del corte

- Fuente operativa: VM `10.46.6.9`, Ubuntu 20.04, PHP 7.4 y Symfony 5.4.
- Destino: VM `10.46.6.10`, Ubuntu, PHP 8.3 y Symfony 6.4.
- Release candidato preparado: `20261021-e1747fc`; no está activo.
- Release activo en destino: `20261015011500-3bb11f9`.
- Archivos: 2.003 en cada servidor y coincidencia total por SHA-256.
- Datos: 161 tablas coinciden en cantidad. La fuente tiene 32 filas AE2 y 27
  filas AE3 adicionales; el destino tiene cuatro trazas de UAT adicionales.
- El destino contiene además `doctrine_migration_versions`, como corresponde a
  la instalación modernizada.

Estos valores son una referencia, no una expectativa fija: deben calcularse
otra vez dentro de la ventana.

## Ensayo previo obligatorio

1. Generar un `pg_dump` en formato custom de la base operativa sin detenerla.
2. Calcular y registrar SHA-256, tamaño, fecha y versiones de cliente/servidor.
3. Restaurarlo en una base temporal aislada de la VM nueva.
4. Registrar la migración inicial de línea base como ejecutada, sin ejecutar su
   SQL, porque las tablas ya existen.
5. Ejecutar la migración incremental que añade `oidc_issuer`, `oidc_subject` y
   su índice único.
6. Validar mapeo, esquema, conteos, inicio de sesión, permisos, reportes y
   descarga de archivos contra esa base temporal.
7. Eliminar la base temporal solamente después de documentar el resultado y
   confirmar que el respaldo puede restaurarse de nuevo.

Las credenciales se leen de la configuración protegida de cada servidor. No se
incluyen en comandos guardados, capturas, bitácoras ni archivos del repositorio.

## Secuencia de la ventana de corte

### 1. Congelar escrituras

1. Comunicar el inicio de la ventana.
2. Impedir nuevas sesiones y operaciones de escritura en la instalación
   anterior mediante la página de mantenimiento aprobada.
3. Confirmar que no quedan peticiones o trabajos activos.
4. Registrar la hora exacta del congelamiento.

Si no puede garantizarse el congelamiento, abortar: una copia tomada mientras
continúan las escrituras puede dejar fuera datos o archivos relacionados.

### 2. Captura final

1. Generar y verificar el volcado final de PostgreSQL en formato custom.
2. Ejecutar la sincronización incremental de `public/uploads` sin borrar el
   origen.
3. Comparar cantidad, bytes de archivos y SHA-256 entre origen y destino.
4. Repetir los conteos exactos de las 164 tablas operativas.

### 3. Restauración segura

1. Crear una base nueva con nombre fechado; no reutilizar ni vaciar la base
   actualmente validada.
2. Restaurar el volcado final con propietario de aplicación y permisos
   restringidos.
3. Adoptar la migración inicial de línea base sin ejecutar su SQL.
4. Ejecutar las migraciones incrementales pendientes.
5. Ejecutar `doctrine:schema:validate` y comparar nuevamente los conteos.
6. Apuntar el entorno del release candidato a la nueva base mediante el gestor
   de configuración protegido, sin imprimir la URL.

### 4. Activación

1. Guardar el destino de los enlaces `current` y la base anterior.
2. Cambiar atómicamente `current` al release candidato.
3. Recargar PHP-FPM y Nginx.
4. Verificar `/healthz`, `/login`, dashboard, usuario básico, administrador,
   trazas, un PDF, un XLSX y un documento real.
5. Revisar Nginx, PHP-FPM y logs de aplicación buscando nuevos 4xx/5xx o
   excepciones.
6. Autorizar la reapertura solo si todas las comprobaciones son correctas.

## Reversión

Revertir inmediatamente si hay corrupción, pérdida de datos, error de esquema,
fallo sostenido de autenticación o un proceso crítico inutilizable:

1. Mantener cerradas las escrituras en la VM nueva.
2. Restaurar el enlace al release anterior y su configuración de base previa.
3. Recargar PHP-FPM y Nginx y comprobar salud.
4. Reabrir la instalación anterior, que no se desmantela durante la ventana.
5. Conservar volcados y registros para analizar el fallo; no intentar mezclar
   manualmente escrituras de ambos sistemas.

## Cierre

- Conservar la VM anterior en modo de reversión durante el periodo acordado.
- Mantener observación reforzada de errores, sesiones, disco y base de datos.
- Cambiar las contraseñas temporales indicadas en `acceso.md`.
- Retirar copias temporales solo después de aprobar la transición y conforme a
  la política de retención institucional.
