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

1. Ejecutar la UAT funcional P0/P1 definida en `docs/uat.md`, sin modificar
   datos reales fuera de una ventana y un conjunto controlado.
2. Confirmar con Identidad que Keycloak autentica y SGU administra de forma
   exclusiva sus roles y permisos; obtener la aprobación técnica.
3. Congelar el candidato, ensayar respaldo/restauración y promover `dev` a
   `main` mediante revisión.
4. Ejecutar el despliegue autorizado, humo posterior y observación reforzada.
5. Tratar rendimiento, módulo piloto e interoperabilidad como evolución
   posterior, según `docs/plan-cierre.md` y `docs/todo-rendimiento.md`.

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
- Se completó la migración masiva de las 830 anotaciones restantes: los controladores contienen ahora 865 atributos `#[Route]` y cero usos de `@Route`. La huella SHA-256 del catálogo JSON de Symfony fue idéntica antes y después (`fd7ecbc70846a60851d77762b3e60705c3d91900252f2bf5409e66d3fff12be2`), por lo que se preservaron rutas, nombres, métodos, valores predeterminados y controladores.
- La validación posterior comprobó sintaxis de todos los controladores, contenedor, 22 archivos YAML, 550 plantillas Twig, 34 pruebas con 159 aserciones y auditoría sin vulnerabilidades. El conversor temporal se retiró del repositorio después de la migración.
- Se actualizó el núcleo completo de Symfony 5.4 a Symfony 6.4.47 LTS y el cargador de rutas quedó configurado explícitamente para atributos. Composer actualizó 65 paquetes, instaló dos y retiró cinco componentes obsoletos, incluido `symfony/security-guard`; los paquetes abandonados bajaron de cinco a cuatro.
- La única incompatibilidad de código encontrada fue la nueva firma de `PasswordUpgraderInterface`, corregida mediante `PasswordAuthenticatedUserInterface`. Tras limpiar las cachés de desarrollo y pruebas quedaron correctos el arranque, el contenedor, Doctrine mapping, YAML, Twig y las 34 pruebas con 159 aserciones.
- Symfony 6.4 hizo visibles deprecaciones concentradas en el mapeo Doctrine/Gedmo por anotaciones y en `SensioFrameworkExtraBundle`. Su retirada requiere migrar entidades y aclarar primero la semántica de las autorizaciones `@IsGranted` con múltiples argumentos; no deben convertirse literalmente porque varios usos cambiarían significado o provocarían errores de tipo.
- Se configuró PostgreSQL para preferir secuencias en la generación automática de identificadores, eliminando 83 avisos repetitivos sin modificar el esquema existente.
- **Cierre de la primera etapa de evolución tecnológica:** quedan consolidados Symfony 6.4 LTS, rutas por atributos, PasswordHasher, compatibilidad de firmas, pruebas limpias de fallos y auditoría sin vulnerabilidades. Las herramientas temporales usadas para evaluar la conversión masiva de entidades fueron retiradas sin modificar ninguna entidad.
- La siguiente etapa tecnológica queda delimitada a 162 entidades con 1.514 anotaciones ORM y 31 Gedmo, más la sustitución de `SensioFrameworkExtraBundle`. Debe ejecutarse con comparación de metadatos/esquema y después de resolver la semántica de las autorizaciones con múltiples roles.
- Quedan como próximos incrementos las 166 autorizaciones todavía gestionadas por `SensioFrameworkExtraBundle`, la migración controlada del mapeo de entidades y la retirada de los cuatro paquetes abandonados restantes. Estos cambios deben mantenerse separados de OIDC y de la autorización dinámica pendiente.
- **Migración tecnológica completada en bloque:** se convirtieron 1.524 anotaciones Doctrine ORM y 31 marcas Gedmo a atributos PHP, se configuró el driver de metadatos por atributos y se actualizó Doctrine ORM a 3.7.5 y DoctrineBundle a 2.19.1. Las 164 clases mapeadas validan correctamente y la base de desarrollo informa `Nothing to update`, por lo que no se requiere cambio de esquema.
- Las 166 autorizaciones de Sensio se sustituyeron por `Symfony\Component\Security\Http\Attribute\IsGranted`. Las listas de roles se expresan explícitamente con `Expression` y conservan la semántica OR verificada del lector anterior, incluidas las reglas de tres y cuatro roles.
- Se retiraron `SensioFrameworkExtraBundle`, Doctrine Annotations, Gedmo, Stof, Doctrine Cache y sus dependencias transitivas abandonadas. Las 31 fechas automáticas usan ahora el atributo local `Timestampable` y un listener Doctrine cubierto por una prueba específica.
- Se migraron los comandos a `#[AsCommand]`, se completaron las firmas `void` pendientes y se actualizaron configuraciones e inyecciones obsoletas. El catálogo de rutas mantiene exactamente la misma huella SHA-256 (`db9158353408ced5f0ed83bc73920397ff3b4e21d2a5321a7b3441466729bf2b`) y Composer no reporta vulnerabilidades ni paquetes abandonados.
- La regresión final de esta etapa queda en 35 pruebas y 162 aserciones, sin fallos ni deprecaciones. La herramienta temporal de conversión fue retirada. La autorización dinámica de roles de Keycloak continúa como bloqueo funcional independiente para producción.
- Se implementó la autorización dinámica de `ROLE_USER` y `ROLE_ADMIN` sin persistir los roles de Keycloak en la entidad `User`. Los roles institucionales viven en la sesión autenticada, sustituyen únicamente sus equivalentes locales y sobreviven a la recarga de usuario mediante un proveedor específico; los demás permisos funcionales locales se conservan.
- Las pruebas funcionales comprueban concesión de `ROLE_ADMIN`, acceso administrativo, 403 con solo `ROLE_USER`, retirada del privilegio en una sesión nueva y ausencia de cambios persistentes. La regresión completa queda en 38 pruebas y 183 aserciones correctas. Falta repetir el recorrido con roles reales en Keycloak y recibir aprobación de Identidad antes de habilitar OIDC en producción.

### 2026-10-13

- La prueba real de concesión y retirada de `ROLE_ADMIN` queda aplazada hasta que esté disponible el equipo de Identidad. El issue permanece abierto y OIDC productivo continúa condicionado a esa validación y aprobación.
- Se inició el bloque de UAT que sí puede ejecutar el equipo de SGU. `docs/uat.md` establece condiciones de entrada, evidencias seguras, 20 recorridos priorizados, validación de FOP/Dompdf/XLSX/archivos, controles de integridad y criterios de salida para la transición.
- La validación reproducible de Ubuntu pasó de comprobar únicamente la presencia de Apache FOP a generar un PDF real y validar su firma. Dompdf y XLSX continúan cubiertos con generación real en PHPUnit; las pruebas de archivos permanecen abiertas hasta endurecer las cargas heredadas.
- Se inició el endurecimiento de archivos por el flujo temporal de documentos de planes de estudio: se eliminó el acceso directo a `$_FILES`, se validan carga, tamaño máximo y MIME real, se impiden destinos relativos inseguros y el almacenamiento usa nombres aleatorios. Las pruebas cubren PDF permitido, contenido ejecutable, tamaño superior a 32 MB y escape de directorio.
- Se extendió centralmente la validación a los 37 campos de archivo de Symfony mediante una extensión de `FileType`: límite institucional de 32 MB y lista explícita de documentos e imágenes admitidos. La regla se aplica incluso a formularios heredados que ya declaran otras restricciones.
- El almacenamiento seguro se extendió a RRHH AE2/AE3, Convenios, logos institucionales y los ciclos documentales de solicitudes de Pregrado y Posgrado. Estos flujos ya no usan `$_FILES`, generan nombres internos aleatorios, reemplazan y eliminan ficheros anteriores mediante rutas controladas y mantienen el nombre original fuera de la ruta física. También se corrigieron rutas inconsistentes y un campo ajeno usado al modificar convenios.
- Se completó la migración de cargas activas del módulo Evaluación: cartas, informes de autoevaluación, planes de trabajo, motivos de rechazo y dictámenes de comisión, CTE y JAN. También se migraron los documentos principales, ejecutivos, modificaciones y adjuntos de Planes de Estudio; al eliminar adjuntos o modificaciones ahora se retiran sus archivos físicos de forma controlada. Los formularios que antes procesaban datos inválidos solo por estar enviados exigen ahora validación satisfactoria.
- Se cerró la migración de cargas heredadas en controladores: reapertura y desactivación de programas aprobados, pasaportes, fichas de salida firmadas e informes ejecutivos usan ahora el almacenamiento seguro central. No quedan usos activos de `$_FILES`, `move_uploaded_file` ni llamadas directas a `UploadedFile::move()` en `src/Controller`.
- Se desplegó `96fa418` en la VM SGU mediante release atómico. Antes se generó y verificó una salva PostgreSQL, se aplicó la identidad OIDC con privilegios del propietario de la tabla, se configuró `OIDC_PKCE_METHOD=S256` y se creó almacenamiento compartido persistente para `public/uploads`. Un primer 500 por la variable PKCE ausente activó la reversión prevista; tras respaldar y corregir el entorno, `/healthz` y `/login` respondieron 200 tanto en el backend como por HTTPS.
- Mientras Identidad no esté disponible, la UAT aislada utilizará el login local de contingencia; esto no sustituye las pruebas reales de Keycloak pendientes.
- La prevalidación automática P0 detectó que el login local no rechazaba cuentas con `User.activo=false`; se incorporó un `ActiveUserChecker` común a web y API y una prueba funcional de rechazo.
- El humo de las 16 portadas funcionales reveló errores 500 en Cooperación y Visitas por plantillas laterales ausentes. Se añadieron ambas plantillas y todas las portadas responden correctamente.
- La creación del usuario inactivo expuso una firma incompatible del listener `prePersist` con Doctrine ORM 3; se actualizó a `PrePersistEventArgs`. También se sustituyeron las direcciones de ordenamiento obsoletas ejecutadas por cinco portadas por `SortDirection`.
- La regresión queda en 55 pruebas y 237 aserciones, sin fallos ni deprecaciones. Los casos UAT-001 a UAT-005 tienen prevalidación automática, pero conservan estado pendiente hasta repetirse sobre la copia autorizada; UAT-006 (trazas) sigue sin prevalidación específica.
- Se endureció `TraceService`: ya no depende de `$_SERVER`, obtiene petición, IP y agente desde HttpFoundation, valida todas las referencias antes de persistir y registra fallos técnicos sin incluir cargas sensibles. El ambiente de pruebas usa los identificadores sintéticos correctos para inicio, cierre, autenticación y sesión.
- UAT-006 quedó prevalidado automáticamente: un login y logout generan exactamente dos trazas y la prueba comprueba que no contienen contraseña ni referencias a tokens. La UAT manual sobre la copia autorizada sigue siendo necesaria.

### 2026-10-14

- Se completó una verificación no destructiva del entorno desplegado: salud y
  login respondieron HTTP 200, Nginx/PHP-FPM/PostgreSQL estaban activos, las
  migraciones estaban al día y Doctrine confirmó el esquema sincronizado.
- PostgreSQL no informó índices inválidos ni restricciones sin validar; no se
  encontraron errores recientes de Nginx ni advertencias de los servicios.
- La regresión creció a 64 pruebas y 274 aserciones, con contenedor, YAML, Twig,
  mapeo, esquema y auditoría de dependencias correctos.
- Se documentó el diagnóstico de rendimiento. El cuello de botella comprobado
  está principalmente en recursos frontend grandes y sin caché/compresión; las
  estadísticas síncronas de las portadas son otro riesgo confirmado.
- `docs/plan-cierre.md` concentra los tres bloques restantes: UAT funcional,
  aprobación de Identidad y promoción/transición controlada.
- La UAT detectó un 500 en programas aprobados de Pregrado causado por dos
  controladores marcados accidentalmente como entidades Doctrine. El hotfix
  `6df51ad` retiró esos atributos, cubrió las tres rutas afectadas y se desplegó
  con salva previa; el usuario confirmó la corrección en producción.
- La regresión posterior quedó en 67 pruebas y 283 aserciones, sin
  vulnerabilidades conocidas. Symfony se actualizó a 6.4.48 y Doctrine
  Fixtures a 4.3.1 dentro de sus líneas compatibles.
- Se completaron los recorridos manuales P1 y P2 de consulta: los once flujos
  académicos principales y las diez rutas complementarias cargaron
  correctamente con la copia real y sin operaciones de escritura.
- Se corrigió el acceso directo a Notificaciones, que exponía un fragmento AJAX
  sin estilos. Los accesos normales redirigen ahora a la vista completa y la
  plantilla hereda los recursos globales; el release
  `20261014182200-1b75dae` quedó activo tras una salva verificada.
- La regresión queda en 68 pruebas y 292 aserciones. UAT-101 a UAT-105,
  UAT-201 a UAT-204 y UAT-301 a UAT-305 están correctos. Permanecen por cerrar
  las comprobaciones manuales P0, archivos/reportes, integridad comparativa y
  la aprobación de la integración por el equipo de Identidad.
- La validación manual de UAT-006 detectó que los identificadores de los
  catálogos de trazas estaban en cero y que el interruptor ocultaba un fallo al
  persistir. Se alineó el entorno con la copia autorizada, se corrigió el
  repositorio y el endpoint dejó de convertir excepciones en respuestas de
  éxito. El usuario confirmó las trazas nuevas de sesión tras desplegar
  `9eca9af`; la regresión quedó en 69 pruebas y 303 aserciones.
- **PENDIENTE DE TRANSICIÓN:** el respaldo PostgreSQL no incluía los archivos
  físicos. `/var/www/akademos/shared/uploads` está vacío, aunque la base
  conserva referencias a documentos de Convenios, fotos y otros adjuntos. Se
  debe copiar íntegramente `public/uploads` desde la instalación operativa,
  comprobar cantidad, tamaño e integridad y realizar una sincronización final
  antes de habilitar a los usuarios en la nueva VM. Los 404 de archivos no se
  resolverán corrigiendo URLs individuales.
- El 21 de octubre de 2026 se realizó la copia inicial de `public/uploads`
  desde la VM operativa, sin detener servicios, sin borrar el origen y con un
  límite aproximado de 10 MB/s. El destino persistente contiene 2.003 archivos
  y 1.039.182.804 bytes; la verificación por checksum no encontró diferencias.
  Dos PDF de Convenios respondieron HTTP 200 interna y externamente. Falta la
  validación visual y una sincronización incremental final durante la ventana
  de transición, ya que el servidor anterior continúa activo.

### 2026-10-21

- Se fijó la frontera definitiva de seguridad: Keycloak autentica mediante
  LDAP, contraseña y TOTP; SGU administra exclusivamente todos sus roles y
  permisos funcionales. La pareja `issuer + sub` sigue siendo el enlace estable.
- Se eliminó del usuario, proveedor y autenticador la sustitución de
  `ROLE_USER`/`ROLE_ADMIN` por claims OIDC. SGU tampoco solicita el scope
  `roles` ni exige roles en el token. Un claim administrativo inesperado no se
  incorpora a la identidad autorizada por SGU.
- La prueba pendiente con Identidad ya no consiste en conceder y retirar
  `ROLE_ADMIN`: debe confirmar esta separación de responsabilidades y aprobar
  el recorrido institucional de autenticación.
- La regresión completa queda en 68 pruebas y 301 aserciones; contenedor, YAML
  y 552 plantillas Twig validan correctamente. Se actualizó
  `symfony/expression-language` de 6.4.45 a 6.4.48 por CVE-2026-109449 y la
  auditoría de Composer no informa vulnerabilidades conocidas.
- `dev` se publicó en `e1747fc` y se preparó, sin activar, el release
  `/var/www/akademos/releases/20261021-e1747fc` en la VM nueva. Dependencias,
  caché de producción, contenedor, plataforma y esquema validan correctamente;
  el enlace activo continúa en `20261015011500-3bb11f9`.
- El almacenamiento compartido conserva 2.003 archivos y 1.039.182.804 bytes.
  Se corrigió el procedimiento para instalar el `.env` operativo no versionado
  y asignar `var/` a `www-data` antes de limpiar/calentar caché.
- No se aceptó sin verificación la nueva huella presentada por la VM operativa
  anterior (`10.46.6.9`). Antes del inventario diferencial, Infraestructura debe
  confirmar `SHA256:I6htL3NSG4SIdorPRkCpch4QCW/MCB64PMykroSfZBs`.
