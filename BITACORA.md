# Bitácora del proyecto

Contexto operativo para futuras sesiones de Codex y nuevas incorporaciones. Mantener este archivo breve, actualizado y libre de secretos.

## Identidad

- **Proyecto:** Sistema de Gestión Universitaria.
- **Repositorio:** `https://github.com/denysbuedo/sistema-gestion-universitaria.git`
- **Directorio local:** `D:\01.DEVOP\Proyectos\sistema-gestion-universitaria`
- **Rama de trabajo y predeterminada:** `dev`.
- **Rama estable:** `main`.
- **GitHub Project:** [Evolución del Sistema de Gestión Universitaria](https://github.com/users/denysbuedo/projects/2).

## Origen

El código procede de una copia saneada del proyecto legado `symfony_akademos`. Se creó este repositorio con historial independiente para evolucionar el sistema sin modificar el original.

Commit inicial:

```text
aba37e2 Initial sanitized import
```

No se importó el historial anterior porque contenía secretos versionados.

## Decisiones vigentes

1. El desarrollo se realiza sobre `dev`.
2. `main` representa la línea estable.
3. El despliegue objetivo será en una VM convencional, sin Docker.
4. No reincorporar Dockerfile ni CI orientada a imágenes salvo decisión expresa.
5. Nunca versionar secretos ni claves privadas.
6. Las migraciones de Doctrine sí deben versionarse.
7. Evolucionar progresivamente; no se plantea una reescritura completa.
8. Crear pruebas antes de refactorizaciones amplias.
9. No hacer commit, push o pull request sin autorización explícita del usuario.

## Estado técnico inicial

- Monolito Symfony 5.4.
- Doctrine ORM con anotaciones.
- Twig y formularios Symfony.
- Autenticación web y API JWT.
- PDF mediante Dompdf y Apache FOP.
- Aproximadamente 683 clases PHP, 152 controladores, 165 entidades, 162 repositorios, 139 formularios y 550 plantillas Twig.
- Sin suite de pruebas al momento de la importación.
- Sin migraciones versionadas al momento de la importación.
- La CI anterior solo construía una imagen Docker y no fue importada.
- La imagen anterior usaba PHP 7.4, ya fuera de soporte.

## Saneamiento realizado

- Se excluyó `.env` con credenciales reales.
- Se excluyeron las claves JWT anteriores.
- Se añadió `.env.example` con valores neutros.
- `config/jwt/*.pem` quedó protegido por `.gitignore`.
- `migrations/` dejó de estar ignorado.
- Se excluyeron Dockerfile y CI Docker.
- Se retiró un token Mapbox hardcodeado de cinco ubicaciones.
- Mapbox se configura mediante `MAPBOX_ACCESS_TOKEN` y Twig.

### Seguridad pendiente

Todo secreto presente alguna vez en el repositorio de origen debe considerarse comprometido y rotarse:

- Credenciales de base de datos.
- Par de claves y passphrase JWT.
- Token Mapbox.
- Credenciales de APIs externas.

No escribir valores reales en esta bitácora, issues, commits o conversaciones.

## Hallazgos arquitectónicos

- 146 de 152 controladores referenciaban repositorios.
- Varios controladores contienen lógica de negocio extensa.
- No existe una capa de aplicación consistente, aunque el README original indicaba que los controladores no debían acceder directamente a repositorios.
- Hay clases especialmente grandes en Institución, Posgrado, Pregrado, Trámites y RR. HH.
- Refactorizar por flujos verticales, con pruebas previas y sin cambios masivos simultáneos.

Patrón objetivo gradual:

```text
HTTP / Controller
        ↓
Servicio de aplicación
        ↓
Reglas de dominio
        ↓
Repositorios / Doctrine
```

## Trabajo inicial en GitHub

Issues vinculados al Project:

1. Rotar y externalizar secretos.
2. Documentar y preparar despliegue en VM.
3. Establecer estrategia y línea base de migraciones.
4. Crear pipeline de validación sin Docker.
5. Crear pruebas de humo de autenticación y autorización.
6. Actualizar Symfony 5.4 y preparar PHP moderno.
7. Seleccionar módulo piloto para extraer lógica de negocio.
8. Auditar binarios, frontend y distribución de FOP.

## Integración Akademos–SIGEP

Se realizó un levantamiento estático preliminar del módulo de Posgrado. El documento se mantiene deliberadamente fuera del repositorio:

```text
D:\01.DEVOP\Proyectos\levantamiento-modulo-posgrado-akademos-sigep.md
```

Conclusiones:

- Akademos gestiona solicitud, revisión, dictámenes, votación y aprobación de programas de posgrado.
- No existe una API específica de Posgrado.
- Las APIs actuales son genéricas y no tienen OpenAPI.
- No se encontraron ediciones/cohortes, matrícula, estudiantes, evaluaciones académicas, créditos obtenidos o graduación dentro de Posgrado.
- La frontera propuesta es que Akademos sea maestro del programa aprobado y SIGEP comience en sus ediciones.
- Antes de integrar hacen falta IDs públicos, versiones, estados estables y una regla explícita de autorización para abrir ediciones.

### Riesgo de estados de Posgrado

- Fixtures: IDs 1 a 6 para Nuevo, En revisión, Analizado por comisión, Revisado, Aprobado y Rechazado.
- El controlador y varias consultas usan el ID 7 como aprobado.
- Algunos tableros interpretan otros IDs como aprobados o rechazados.

No diseñar integraciones basadas en esos IDs. Primero verificar producción y adoptar códigos estables.

### Decisión sobre la plataforma de interoperabilidad

- El 2026-10-08 se decidió pausar el desarrollo de la plataforma de interoperabilidad y concentrar los esfuerzos en Akademos, por ser el sistema núcleo del Ministerio.
- Se retoma la hoja de ruta interna de estabilización, seguridad, pruebas, migraciones, despliegue y evolución gradual de Akademos.
- La interoperabilidad queda como requisito arquitectónico futuro, no como iniciativa activa inmediata.
- Las mejoras de Akademos deben favorecer una integración posterior: identificadores públicos estables, códigos funcionales en lugar de IDs internos, fechas/versiones confiables de actualización, contratos de solo lectura y separación de datos sensibles.
- No implementar todavía la API específica de Posgrado ni comprometer una especificación OpenAPI o un entorno piloto hasta que la base técnica y funcional de Akademos esté estabilizada y autorizada.

## Instrucciones para futuras sesiones de Codex

1. Confirmar que el directorio activo sea `D:\01.DEVOP\Proyectos\sistema-gestion-universitaria`.
2. Ejecutar `git status --short --branch`.
3. Confirmar `dev`, salvo instrucción expresa.
4. Leer esta bitácora antes de proponer cambios amplios.
5. Revisar el issue correspondiente antes de implementar una iniciativa.
6. Preservar cambios del usuario y archivos no relacionados.
7. No recuperar secretos desde el repositorio legado.
8. No publicar cambios sin autorización explícita.
9. Guardar aquí decisiones e hitos; dejar el detalle diario en issues y commits.

## Próximo paso recomendado

1. Coordinar con Identidad el registro de Akademos como cliente confidencial OIDC en el realm institucional común.
2. Confirmar responsables, DNS y URLs HTTPS exactas de pruebas y producción para completar el expediente de alta.
3. Implementar Authorization Code Flow detrás de `OIDC_ENABLED=0`, usando `sub` como vínculo estable y conservando temporalmente el login legado.
4. Sustituir la validación JWT local por tokens Bearer de Keycloak y probar audiencia, roles, expiración, revocación y logout.
5. Continuar la actividad 6 y mantener ambos pipelines como barreras obligatorias.

## Historial

### 2026-09-29

- Creación del repositorio nuevo con historial saneado.
- Creación y publicación de `dev` como rama predeterminada.
- Creación del GitHub Project y ocho tareas iniciales.
- Levantamiento preliminar de Posgrado para SIGEP.
- Creación de esta bitácora.

### 2026-10-08

- Se pausó la iniciativa de plataforma de interoperabilidad para priorizar la evolución de Akademos como sistema núcleo del Ministerio.
- La interoperabilidad se conserva como condicionante de diseño futuro, sin alterar la prioridad inmediata de la hoja de ruta existente.
- Se inició la consolidación de la línea base de desarrollo en Windows.
- Se confirmó PostgreSQL como motor de la aplicación y se corrigió su configuración de ejemplo.
- Se documentó el procedimiento local validado y se retiró del README el procedimiento de producción no verificado.
- Se inició la actividad 1 de seguridad con un inventario de secretos y credenciales.
- Se externalizó el usuario administrador de fixtures y se reforzó la exclusión de backups.
- El escaneo del historial saneado no encontró claves privadas ni tokens reconocibles.
- `composer audit --locked` detectó 63 avisos en 19 paquetes y dos paquetes abandonados; la corrección queda como bloqueo de seguridad antes de cerrar la actividad.
- Se creó la base aislada `gestion_universitaria_test`, con configuración y claves JWT exclusivas para pruebas; la suite verifica el nombre de la base antes de cada caso.
- Se incorporaron 9 pruebas funcionales con 48 aserciones para arranque, autenticación web, sesiones, cierre de sesión, JWT y autorización por rol. La batería completa quedó en verde.
- Se corrigió el uso de sesiones nativas creadas manualmente en el autenticador y el servicio de trazas; ambos reutilizan ahora la sesión de la petición.
- Las validaciones de contenedor, YAML y Twig quedaron correctas. La validación Doctrine confirmó que el esquema está sincronizado, pero detectó ocho entidades con inconsistencias de tipos o asociaciones en sus metadatos.
- `composer validate` confirmó uso simple válido y señaló deuda existente: restricción abierta de `dompdf/dompdf` y metadatos de paquete ausentes.
- El procedimiento reproducible quedó documentado en `docs/pruebas.md`. Nunca ejecutar fixtures sobre la copia de la base real.
- Se actualizaron 65 dependencias directas y transitivas dentro de las restricciones compatibles, incluyendo Dompdf 3.1.6, Guzzle 7.15.5, PhpSpreadsheet 1.30.7, PHPUnit 9.6.38, Twig 3.30.0 y los últimos parches disponibles de Symfony 5.4.
- `composer audit --locked` pasó de 63 avisos en 19 paquetes a cero avisos conocidos. La suite permaneció en verde con 9 pruebas y 48 aserciones.
- Composer reporta ahora cinco paquetes abandonados: `behat/transliterator`, `doctrine/annotations`, `doctrine/cache`, `sensio/framework-extra-bundle` y `symfony/security-guard`; su sustitución forma parte de la evolución tecnológica.
- Se detectaron dos clases de exportación omitidas por el autoloader debido a que declaran `App\\Service` dentro de `src/Services`; deben corregirse con pruebas específicas del flujo de exportación.
- Se corrigieron las ocho inconsistencias detectadas por Doctrine: siete entidades alinearon sus tipos PHP con columnas enteras o de texto y la relación `Grupo`–`Estructura` quedó correctamente declarada y sincronizada en ambos sentidos.
- `doctrine:schema:validate` informa ahora mapeos correctos y esquema sincronizado. La suite creció a 11 pruebas y 61 aserciones.
- Se corrigieron los namespaces de `ExportListHeader` y `ExportListMultiple`; `composer dump-autoload --strict-psr` finaliza sin clases omitidas.
- Se añadieron pruebas reales de generación XLSX con PhpSpreadsheet y PDF con Dompdf. La regresión completa quedó en 14 pruebas y 68 aserciones.
- Se inició la actividad 2 con Ubuntu 24.04 LTS, Nginx y PHP 8.3-FPM como plataforma objetivo; Windows queda limitado al desarrollo.
- Se añadieron plantillas de Nginx, pool PHP-FPM, carga de entorno mediante systemd, configuración externa sin secretos y una comprobación operativa de requisitos.
- FOP dejó de depender de `cmd /c` y `fop.bat`: el ejecutable se configura mediante `FOP_EXECUTABLE` y se invoca con argumentos seguros en Windows o Linux.
- Los temporales de exportación se movieron fuera de `public/` hacia `var/tmp/export/`.
- La guía `docs/despliegue-ubuntu.md` define instalación, permisos, versiones inmutables, activación por enlace simbólico, validación y reversión. No autoriza todavía un despliegue productivo ni cambios de esquema.
- Se añadieron pruebas que mantienen sincronizadas las variables del entorno productivo y las plantillas Nginx/PHP-FPM. La suite queda en 16 pruebas y 74 aserciones.
- Se añadió una validación reproducible basada en Ubuntu 24.04 y PostgreSQL efímero para comprobar PHP 8.3, dependencias, esquema, fixtures, pruebas, FOP, Nginx y PHP-FPM sin acceder a datos reales.
- La validación Ubuntu se integró en GitHub Actions para `dev`, con permisos de solo lectura y sin secretos. La ejecución local quedó pendiente porque Docker Desktop entró en modo de solo lectura al agotarse completamente el espacio de `C:`.
- Las comprobaciones locales de la actividad 2 quedaron en 18 pruebas y 81 aserciones; también validan sintaxis de scripts, Compose y las plantillas de despliegue.
- Se adaptó el entorno efímero al diseño de datos de PostgreSQL 18 y se generó `.env` dentro de la imagen exclusivamente desde `.env.example`.
- La auditoría conserva el informe de cinco paquetes abandonados y falla ante avisos de seguridad, sin tratar ambas categorías como equivalentes en Composer 2.7 de Ubuntu.
- La ejecución `37913609338` de GitHub Actions completó correctamente la validación real sobre Ubuntu 24.04: PHP 8.3, PostgreSQL 18 efímero, fixtures, 18 pruebas con 86 aserciones, contenedor, 22 archivos YAML, 550 plantillas Twig, mapeo y esquema Doctrine, auditoría sin vulnerabilidades, PHP-FPM y Nginx.
- Con esta ejecución queda cerrada la preparación técnica reproducible de la actividad 2; el despliegue productivo continúa sujeto a aprovisionamiento, secretos rotados, respaldo y ventana de cambio autorizada.

### 2026-10-10

- Se inició y completó la actividad 3 con una línea base Doctrine versionada para PostgreSQL.
- El inventario DDL del respaldo autorizado se consultó solo en modo lectura, sin restaurar ni inspeccionar filas: contiene 164 tablas, las mismas que la línea base generada desde los metadatos.
- `DoctrineMigrations\\Version20261010004147` crea 164 tablas en 13 esquemas funcionales y `public`; la única diferencia nominal con el respaldo es la calificación explícita `public.` de dos tablas.
- La configuración de migraciones usa `all_or_nothing`, restringe la línea base a PostgreSQL y documenta por separado instalaciones nuevas y adopción segura en bases existentes.
- Se retiró `doctrine:schema:create` de la preparación de pruebas y despliegue; el flujo reproducible usa `doctrine:migrations:migrate`.
- La ejecución `38010418439` de GitHub Actions aplicó la línea base (833 consultas), ejecutó el rollback completo (580 consultas), la reaplicó y validó el esquema contra Doctrine.
- La validación Ubuntu finalizó con 21 pruebas y 124 aserciones, esquema sincronizado y cero vulnerabilidades conocidas.
- La actividad 3 queda cerrada técnicamente. No se ejecutó SQL ni se modificaron datos del respaldo real.
- Se completó la actividad 4 con un flujo `Fast validation` sin Docker para `main` y `dev`: Composer, sintaxis PHP incremental, 13 pruebas unitarias, contenedor, YAML, Twig y auditoría de dependencias.
- La primera ejecución rápida (`38010781870`) terminó correctamente en aproximadamente un minuto y se ejecutó en paralelo con la validación integral (`38010781895`), que también quedó en verde.
- La actividad 5 ya estaba materialmente cubierta por nueve pruebas funcionales repetibles de login web, sesión, logout, JWT, acceso anónimo y permisos por rol; la validación integral volvió a confirmarlas sobre PostgreSQL efímero.
- Se migró el login web al Authenticator Manager moderno de Symfony y se eliminó el almacenamiento de la contraseña en texto claro dentro de la sesión; las trazas se registran después de validar credenciales.
- Tras recibir los lineamientos institucionales, se descartó la implementación local de TOTP y códigos de recuperación. El commit preparatorio se revirtió antes de almacenar semillas o activar usuarios.
- `https://identidad.reduniv.edu.cu` respondió correctamente y redirigió a la consola administrativa de Keycloak.
- La decisión vigente es delegar login, 2FA, recuperación y logout en Keycloak mediante OpenID Connect Authorization Code Flow con cliente confidencial.
- `docs/identidad-institucional.md` contiene el expediente inicial, matriz pendiente, transición desde el legado y criterios de aceptación; la integración queda desactivada con `OIDC_ENABLED=0` hasta recibir realm, cliente, secreto y URLs aprobadas.
- Se implementó y validó en `dev` el cliente `sgu-dev` mediante Authorization Code Flow, PKCE S256 y vinculación estable `issuer + sub`. El recorrido real Keycloak → LDAP → contraseña → TOTP regresó correctamente a SGU con `ROLE_USER`; la suite quedó en 33 casos y 155 aserciones, sin vulnerabilidades conocidas.
- Se comprobó en navegador el logout OIDC de `sgu-dev`: Keycloak aceptó `id_token_hint`, regresó a la URL autorizada y SGU denegó el dashboard hasta una nueva autenticación.
- Se verificó el control negativo del segundo factor: Keycloak rechazó un TOTP incorrecto con `Invalid authenticator code` y solo completó el acceso al presentar un código válido.
- Se revocó de forma controlada la sesión piloto de `sgu-dev`; tras vencer el access token, Keycloak rechazó el refresh token y SGU invalidó correctamente la sesión Symfony, redirigió al login y no entró en un ciclo de renovación.
- Se mantuvo una sesión `sgu-dev` vigente durante más de la vida máxima de cinco minutos del access token; SGU renovó la sesión transparentemente y permitió continuar trabajando sin solicitar nuevamente contraseña o TOTP.
- La prueba dinámica de retirada de `ROLE_ADMIN` reveló una regresión al usar una copia temporal de la entidad `User`: Symfony no conservaba la sesión tras el callback. Se restauró el autenticador al estado estable `a1b84bc`, se limpió caché, se reinició el servidor y el login institucional volvió a validarse. La prueba de roles queda pendiente de una implementación cubierta por pruebas funcionales específicas.
- **PENDIENTE CRÍTICO / BLOQUEANTE DE PRODUCCIÓN:** resolver la autorización dinámica de `ROLE_USER` y `ROLE_ADMIN` desde Keycloak. Debe incluir pruebas automáticas de concesión y retirada, 403 administrativo, persistencia de sesión y convivencia con permisos funcionales locales. No activar OIDC productivo ni retirar el login de contingencia mientras este punto permanezca abierto.
- Se inició la actividad 6 de evolución tecnológica con un inventario de compatibilidad para Symfony 6.4. La primera corrección sustituye `UserPasswordEncoderInterface` y `encodePassword()` por `UserPasswordHasherInterface` y `hashPassword()` en controladores, comandos, fixtures y pruebas, manteniendo compatibilidad con Symfony 5.4.
- La regresión posterior quedó en 34 pruebas y 159 aserciones; el contenedor valida correctamente y `composer audit --locked` no informa vulnerabilidades conocidas.
- Se corrigieron también los tres primeros avisos propios de tipos de retorno en `CustomTwigExtension` y `CustomGrantedVoter`. Al desaparecer estos quedaron visibles seis avisos propios adicionales en fixtures, formularios y la función DQL `DateFormat`, además de seis avisos indirectos de configuración e inyección; se incorporan al inventario de la actividad 6.
- Se añadieron los tipos de retorno compatibles a `AppFixtures`, los formularios administrativos y `DateFormat`. La suite conserva 34 pruebas y 159 aserciones y ya no emite deprecaciones propias; permanecen seis avisos indirectos de Doctrine y de inyección del contenedor completo.
- Se activaron los lazy ghost objects de Doctrine, se eliminaron dependencias no utilizadas de `LoginFormAuthenticator` y `CustomLogoutListener`/`UserSessionInitializer` reciben ahora parámetros explícitos en lugar del contenedor completo. Los avisos indirectos bajaron de seis a dos, ambos concentrados en el servicio legado `HandlerFop`.
- La dependencia heredada de `HandlerFop` sobre el contenedor quedó declarada explícitamente para impedir el autowiring obsoleto. No se reescribió todavía el localizador dinámico usado por los reportes FOP; su sustitución requiere inventariar repositorios, traductor, usuario y servicios opcionales de cada reporte.
- Se añadió el alias explícito requerido por el localizador heredado y la suite quedó libre de avisos de deprecación propios e indirectos: 34 pruebas y 159 aserciones correctas. La modernización interna de los reportes FOP continúa como deuda separada, no como bloqueo para el siguiente incremento de compatibilidad.
- Se inició la migración gradual de rutas a atributos PHP con 11 declaraciones en seis controladores de menú, dashboard y portadas. `debug:router` confirmó los siete nombres públicos, URLs y métodos esperados; quedan 854 anotaciones `@Route` y ya existen 11 atributos `#[Route]`.
- Las anotaciones `@IsGranted` permanecen temporalmente durante Symfony 5.4. Su migración al atributo nativo se realizará al avanzar la base de Symfony, sin mezclarla con la autorización dinámica de Keycloak que continúa bloqueando producción.
- El segundo lote migró 16 declaraciones adicionales en ocho portadas funcionales. Las ocho rutas GET conservaron nombre y URL; el inventario queda en 27 atributos `#[Route]` y 838 anotaciones `@Route` pendientes.
- El tercer lote migró ocho declaraciones en dos endpoints API y dos controladores de Estructura. Se conservaron URLs, nombres, métodos `POST|OPTIONS`/`GET` y el formato JSON; el inventario queda en 35 atributos y 830 anotaciones pendientes.
- Tras los tres lotes, la regresión continúa en 34 pruebas y 159 aserciones, sin avisos de deprecación ni vulnerabilidades conocidas. Composer mantiene el inventario de cinco paquetes abandonados.
- Quedan como próximos incrementos la migración gradual de 865 rutas y 166 autorizaciones desde anotaciones a atributos, y la retirada de cinco paquetes abandonados. Estos cambios deben mantenerse separados de OIDC y de la autorización dinámica pendiente.
