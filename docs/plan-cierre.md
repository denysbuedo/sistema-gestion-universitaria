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
85 %. El porcentaje no representa líneas de código: el 15 % restante contiene
validaciones humanas y de coordinación que pueden bloquear la transición.

## Bloque A — UAT funcional y datos

Objetivo: comprobar con responsables funcionales que la versión conserva los
procesos y resultados del sistema de referencia.

- [x] Prevalidación automática P0: 64 pruebas y 274 aserciones.
- [x] Salud pública, servicios, migraciones, esquema y almacenamiento de cargas.
- [ ] Ejecutar recorridos manuales P0 y P1 de `docs/uat.md`.
- [ ] Comparar una muestra de conteos, consultas y reportes con el sistema de
  referencia.
- [ ] Validar descarga de un documento real autorizado.
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
- [x] Autorización dinámica cubierta automáticamente.
- [ ] Conceder `ROLE_ADMIN` al piloto y revocar su sesión de `sgu-dev`.
- [ ] Confirmar acceso administrativo.
- [ ] Retirar `ROLE_ADMIN`, revocar nuevamente y confirmar respuesta 403.
- [ ] Recibir aprobación de Identidad y documentar la evidencia sin tokens.

Hasta terminar este bloque no se elimina el acceso local de contingencia ni se
declara OIDC productivo aprobado.

## Bloque C — Promoción y transición

Objetivo: convertir el estado validado de `dev` en una versión estable.

- [ ] Congelar el commit candidato y comprobar ambos pipelines.
- [ ] Crear y revisar el pull request de `dev` hacia `main`.
- [ ] Obtener un respaldo reciente y ejecutar un ensayo documentado de
  restauración y rollback.
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

Si Identidad está disponible, la UAT no descubre defectos bloqueantes y existe
una ventana de cambio, los tres bloques de cierre pueden completarse en dos a
cuatro jornadas concentradas. Si aparecen defectos funcionales o no hay
disponibilidad de los responsables, el tiempo dependerá de esas correcciones y
aprobaciones; el trabajo técnico ya realizado no necesita repetirse.
