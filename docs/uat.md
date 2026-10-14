# Plan de pruebas de aceptación (UAT)

## Objetivo

Validar que la versión modernizada de SGU conserva el comportamiento funcional
del sistema actualmente en uso antes de promover `dev` y cambiar a los usuarios
a la nueva VM.

La UAT se ejecutará sobre una copia autorizada y reciente de la base real. No se
utilizará la base de producción ni se enviarán notificaciones a destinatarios
reales.

## Condiciones de entrada

- [ ] Copia de la base identificada con fecha, origen y responsable.
- [ ] Restauración realizada en un entorno aislado.
- [ ] Correo, Mattermost y demás salidas externas desactivadas o redirigidas.
- [ ] Secretos exclusivos del ambiente de pruebas.
- [ ] `dev` desplegada desde un commit identificado.
- [ ] `php bin/console doctrine:schema:validate` correcto.
- [ ] Suite PHPUnit y auditoría de Composer correctas.
- [ ] Respaldo del entorno UAT disponible antes de realizar cambios funcionales.

La prueba real de concesión y retirada de roles Keycloak se mantiene aplazada
hasta disponer del equipo de Identidad. No impide ejecutar los casos funcionales
con el acceso local de contingencia en el entorno aislado.

## Evidencia mínima por caso

Registrar en cada prueba:

- identificador del caso y módulo;
- fecha, responsable y commit probado;
- rol funcional utilizado;
- datos de referencia anonimizados;
- resultado esperado y resultado obtenido;
- evidencia sin contraseñas, tokens, cookies ni datos personales innecesarios;
- incidencia asociada, si corresponde.

Estados permitidos: `Pendiente`, `Correcto`, `Falló`, `Bloqueado` y `No aplica`.

## Prevalidación automatizada

El 13 de octubre de 2026 se ejecutó sobre la base sintética de pruebas:

- UAT-001: arranque de Symfony y acceso al dashboard;
- UAT-002: login local, persistencia de sesión y logout;
- UAT-003: rechazo de contraseña incorrecta y de usuario inactivo;
- UAT-004: respuesta 403 para un usuario sin privilegios administrativos;
- UAT-005: apertura de las 16 portadas funcionales con un administrador.
- UAT-006: creación de trazas de inicio y cierre de sesión sin contraseñas ni
  tokens en la carga registrada.

La suite resultante contiene 64 pruebas y 274 aserciones. Esta prevalidación no
cambia los estados del registro UAT: los recorridos deben repetirse sobre la
copia autorizada y con evidencia funcional.

## Verificación técnica del entorno desplegado — 2026-10-14

Se ejecutó un bloque de comprobaciones no destructivas sobre el release
`96fa418` y desde un puesto externo. No se realizaron altas, modificaciones ni
bajas sobre los datos reales, por lo que esta evidencia no sustituye los
recorridos funcionales manuales.

- `/healthz` respondió cinco veces con HTTP 200, entre 0,29 y 0,32 s.
- `/login` respondió cinco veces con HTTP 200, entre 0,30 y 0,39 s.
- Desde la propia VM, `/healthz` respondió en menos de 1 ms y `/login` en
  aproximadamente 20 ms, a través de Nginx y PHP-FPM.
- Nginx, PHP 8.3-FPM y PostgreSQL estaban activos, sin presión observable de
  CPU, memoria, swap o disco.
- Las dos migraciones disponibles constan como ejecutadas; no hay migraciones
  nuevas pendientes.
- Doctrine informó mapeo correcto y esquema sincronizado con las entidades.
- PostgreSQL no informó índices inválidos ni restricciones pendientes de
  validación. La base ocupaba aproximadamente 27 MB.
- No se encontraron advertencias de Nginx/PHP-FPM en `journald` ni entradas
  recientes en el registro de errores de Nginx.
- `public/uploads` apunta al almacenamiento compartido persistente y este tiene
  propietario `www-data:www-data` y permisos restringidos.
- La auditoría de Composer no encontró vulnerabilidades conocidas.
- En desarrollo quedaron correctos el contenedor, 20 archivos YAML, 552
  plantillas Twig, el mapeo y el esquema de pruebas, además de las 64 pruebas
  con 274 aserciones.

Permanece pendiente comprobar mediante un recorrido funcional controlado la
creación y limpieza de `var/tmp/export/`; el directorio todavía no existía en
el release porque no se generó un reporte durante esta verificación de solo
lectura.

## Matriz priorizada

### P0 — habilitación y seguridad

| Caso | Validación | Resultado esperado |
|---|---|---|
| UAT-001 | Arranque, `/healthz` y dashboard | Sin errores 500; estado saludable |
| UAT-002 | Login local de contingencia y logout | Sesión creada e invalidada correctamente |
| UAT-003 | Usuario inactivo y credenciales incorrectas | Acceso rechazado sin revelar información |
| UAT-004 | Usuario básico intenta administrar usuarios | Respuesta 403 |
| UAT-005 | Navegación por módulos autorizados | Menús y portadas coinciden con sus permisos |
| UAT-006 | Trazas de inicio, cambios y cierre | Eventos registrados sin secretos ni tokens |

### P1 — datos maestros y estructura

| Caso | Módulo | Recorrido mínimo |
|---|---|---|
| UAT-101 | Estructura | Listar, buscar, consultar detalle y editar un registro controlado |
| UAT-102 | Institución | Consultar institución, dependencias y clasificaciones |
| UAT-103 | Personal | Buscar persona, consultar ficha y comprobar relaciones organizativas |
| UAT-104 | Administración | Usuarios, roles, módulos y funcionalidades |
| UAT-105 | Configuración | Idiomas y catálogos utilizados por los formularios |

### P1 — procesos académicos principales

| Caso | Módulo | Recorrido mínimo |
|---|---|---|
| UAT-201 | Pregrado | Consultar programa, solicitud, plan de estudio y documentos |
| UAT-202 | Posgrado | Consultar solicitud, programa, comisión y dictamen |
| UAT-203 | Evaluación | Consultar solicitud, comisión, plan e informe de autoevaluación |
| UAT-204 | RRHH | Consultar y generar operaciones AE2/AE3 representativas |

### P2 — procesos complementarios

| Caso | Módulo | Recorrido mínimo |
|---|---|---|
| UAT-301 | Convenio/Cooperación | Consultar convenio y sus catálogos relacionados |
| UAT-302 | Trámites | Consultar ficha de salida, pasaporte y plan de misión |
| UAT-303 | Economía | Consultar conceptos de gasto y obsequios |
| UAT-304 | Informatización | Consultar sistemas, servicios y conectividad |
| UAT-305 | Visitas y notificaciones | Consultar flujos sin enviar avisos externos reales |

## Reportes, exportaciones y archivos

Ejecutar al menos un caso realista de cada tecnología:

- [x] PDF mediante Apache FOP (prevalidado en la validación Ubuntu).
- [x] PDF mediante Dompdf (prevalidado por PHPUnit).
- [x] Exportación XLSX con PhpSpreadsheet (prevalidado por PHPUnit).
- [ ] Descarga de un documento previamente almacenado.
- [x] Carga de un archivo permitido dentro del límite configurado (prevalidada en PHPUnit para documentos de planes de estudio).
- [x] Rechazo de tipo MIME o tamaño no permitido (prevalidado en PHPUnit; límite de 32 MB).
- [ ] Confirmación de que los temporales se crean en `var/tmp/export/` y se
      eliminan según el procedimiento operativo.

Verificar que nombre, tipo MIME, codificación, contenido, totales y filtros
coinciden con el sistema de referencia.

## Integridad y comparación

- [ ] Comparar conteos por módulo antes y después de la UAT.
- [ ] Confirmar que consultas y reportes críticos devuelven los mismos resultados
      que el sistema actualmente operativo para una muestra acordada.
- [ ] Confirmar que no aparecieron migraciones de esquema no planificadas.
- [ ] Revisar errores 4xx/5xx y excepciones en los registros del periodo.
- [ ] Restaurar o descartar los cambios introducidos por los casos de prueba.

## Criterios de salida

- Todos los casos P0 y P1 están `Correcto` o tienen una excepción formalmente
  aceptada.
- No existen defectos bloqueantes ni pérdida o corrupción de datos.
- Reportes, exportaciones y archivos fueron validados en Ubuntu.
- La suite automatizada vuelve a quedar en verde después de las correcciones.
- Responsables funcional y técnico aprueban el resultado.
- Existen copia verificada, procedimiento de despliegue y plan de reversa antes
  de establecer una fecha de transición.

## Registro resumido

| Caso | Estado | Responsable | Fecha | Evidencia/incidencia |
|---|---|---|---|---|
| UAT-001 | Pendiente |  |  |  |
| UAT-002 | Pendiente |  |  |  |
| UAT-003 | Pendiente |  |  |  |
| UAT-004 | Pendiente |  |  |  |
| UAT-005 | Pendiente |  |  |  |
| UAT-006 | Pendiente |  |  |  |
| UAT-101–105 | Pendiente |  |  |  |
| UAT-201–204 | Pendiente |  |  |  |
| UAT-301–305 | Pendiente |  |  |  |

