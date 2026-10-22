# Plan de cierre de la transición

Estado al 14 de octubre de 2026. Este plan distingue lo necesario para poner la
versión modernizada en servicio de la evolución posterior del producto.

## Situación actual

La preparación técnica está esencialmente terminada: Symfony 6.4 LTS,
Doctrine ORM 3, PHP 8.3, migraciones versionadas, validación continua,
autenticación OIDC con PKCE, seguridad de archivos, despliegue atómico en
Ubuntu, almacenamiento persistente y rollback documentado. La aplicación está
desplegada y la comprobación técnica de producción es correcta.

Como estimación de planificación, el cierre global está aproximadamente al
90 %. El porcentaje no representa líneas de código: el 10 % restante contiene
validaciones humanas y de coordinación que pueden bloquear la transición.

## Bloque A — UAT funcional y datos

Objetivo: comprobar con responsables funcionales que la versión conserva los
procesos y resultados del sistema de referencia.

- [x] Prevalidación automática y regresión: 68 pruebas y 301 aserciones.
- [x] Salud pública, servicios, migraciones, esquema y almacenamiento de cargas.
- [x] Ejecutar recorridos manuales P1 de datos maestros y procesos académicos.
- [x] Ejecutar recorridos manuales P2 de procesos complementarios.
- [ ] Completar recorridos manuales P0 de sesión, rechazo de acceso, permisos y
  trazas.
- [ ] Comparar una muestra de conteos, consultas y reportes con el sistema de
  referencia.
- [x] Comparar conteos exactos de las 164 tablas operativas e identificar el
  delta desde la copia: 32 AE2 y 27 AE3 adicionales en el origen.
- [x] Validar descarga de documentos reales autorizados.
- [x] Realizar la copia inicial íntegra de `public/uploads` desde la instalación operativa al
  almacenamiento persistente `/var/www/akademos/shared/uploads` de la nueva VM.
- [x] Comparar cantidad, tamaño e integridad de la copia inicial.
- [x] Revalidar los 2.003 archivos por SHA-256 contra la VM operativa, sin
  faltantes ni diferencias de contenido.
- [ ] Ejecutar una sincronización incremental final antes del cambio de usuarios.
- [ ] Generar en UAT un PDF FOP, un PDF Dompdf y un XLSX, y comprobar contenido.
- [ ] Confirmar creación y limpieza de `var/tmp/export/`.
- [ ] Registrar y corregir defectos bloqueantes; repetir la regresión completa.
- [ ] Obtener aprobación funcional y técnica.

Este bloque requiere un entorno UAT aislado para operaciones de escritura. En
la instalación con datos reales solo se harán consultas hasta contar con una
ventana y datos controlados expresamente autorizados.

## Bloque B — Identidad institucional

Objetivo: cerrar la condición pendiente con el equipo de Identidad.

- [x] Login Keycloak → LDAP → contraseña → TOTP.
- [x] PKCE S256, state, nonce, renovación, revocación y logout.
- [x] SGU conserva exclusivamente sus roles y permisos locales.
- [x] Un usuario básico local recibe 403 y un administrador local conserva acceso.
- [x] Los claims de roles de Keycloak no conceden privilegios en SGU.
- [ ] Confirmar con Identidad la frontera: Keycloak autentica; SGU autoriza.
- [ ] Recibir aprobación de Identidad y documentar la evidencia sin tokens.

Hasta terminar este bloque no se elimina el acceso local de contingencia ni se
declara OIDC productivo aprobado.

## Bloque C — Promoción y transición

Objetivo: convertir el estado validado de `dev` en una versión estable.

- [x] Preparar en la VM nueva el candidato `e1747fc` y validar dependencias,
  caché, contenedor, requisitos y esquema sin activarlo.
- [ ] Congelar el commit candidato definitivo y comprobar ambos pipelines.
- [ ] Crear y revisar el pull request de `dev` hacia `main`.
- [x] Obtener un respaldo reciente y ejecutar un ensayo documentado de
  restauración, migración, aislamiento y reversibilidad.
- [x] Documentar en `docs/procedimiento-corte.md` la restauración en una base
  nueva, adopción de línea base, migración OIDC, activación y reversión.
- [ ] Confirmar que la sincronización final de documentos, imágenes y adjuntos
  concluyó sin archivos faltantes ni enlaces 404.
- [ ] Desplegar el commit aprobado durante una ventana autorizada.
- [ ] Ejecutar humo posterior al despliegue y revisar errores 4xx/5xx.
- [ ] Comunicar la transición, responsables y procedimiento de incidencia.
- [ ] Mantener observación reforzada durante la ventana acordada.

## Trabajo posterior que no bloquea el cierre

- Optimización de rendimiento registrada en `docs/todo-rendimiento.md`.
- Extracción gradual de lógica del primer módulo piloto.
- Sustitución progresiva de dependencias frontend y mejora de accesibilidad.
- Contrato de solo lectura para la futura plataforma de interoperabilidad.
- Evolución de métricas, auditoría y alta disponibilidad institucional.

## Tiempo restante estimado

Si Identidad está disponible, las verificaciones restantes no descubren
defectos bloqueantes y existe una ventana de cambio, los tres bloques de cierre
pueden completarse en una a tres jornadas concentradas. Si aparecen defectos funcionales o no hay
disponibilidad de los responsables, el tiempo dependerá de esas correcciones y
aprobaciones; el trabajo técnico ya realizado no necesita repetirse.
