# Plan de pruebas de aceptación (UAT)

## Objetivo

Validar que la versión modernizada de SGU conserva el comportamiento funcional
del sistema actualmente en uso antes de promover `dev` y cambiar a los usuarios
a la nueva VM.

La UAT se ejecutará sobre una copia autorizada y reciente de la base real. No se
utilizará la base de producción ni se enviarán notificaciones a destinatarios
reales.

## Condiciones de entrada

- [x] Copia de la base identificada con fecha y origen; responsable técnico del
  ensayo registrado en la operación institucional.
- [x] Restauración realizada en la base aislada
  `akademos_rehearsal_20261022`, sin cambiar la aplicación activa.
- [ ] Correo, Mattermost y demás salidas externas desactivadas o redirigidas.
- [ ] Secretos exclusivos del ambiente de pruebas.
- [x] Candidato `e1747fc` preparado desde `dev` sin activarlo.
- [x] `php bin/console doctrine:schema:validate` correcto sobre la restauración.
- [x] Suite PHPUnit y auditoría de Composer correctas.
- [ ] Respaldo del entorno UAT disponible antes de realizar cambios funcionales.

No se requiere conceder ni retirar roles de SGU en Keycloak. Identidad se limita
a autenticar; los casos de autorización se ejecutan con roles administrados en
SGU. Permanece pendiente la aprobación de esta frontera por Identidad.

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

### Verificación manual de portadas — 2026-10-14

Un usuario autorizado recorrió las 16 portadas funcionales del entorno
desplegado. Todas abrieron sin error 500, sin redirección inesperada al login y
con menús, datos, estadísticas y gráficos cargados correctamente según los
permisos de su sesión. Con esta evidencia quedan correctos UAT-001 y UAT-005.

### Verificación manual de listados y detalles — 2026-10-14

El usuario autorizado comprobó listados, filtros, búsquedas y detalles en
Administración, Estructura, Institución, Personal, Configuración, Pregrado,
Posgrado, Evaluación y RRHH AE2/AE3. Los diez recorridos cargaron datos sin
errores 500 ni pérdida de la sesión y no se ejecutaron operaciones de escritura.

Esta evidencia cierra UAT-101 a UAT-104. La comprobación de Idiomas aporta
evidencia a UAT-105, que permanece pendiente hasta validar al menos otro
catálogo utilizado por formularios. Los listados principales aportan evidencia
parcial a UAT-201 a UAT-204; todavía deben recorrerse planes, documentos,
comisiones, dictámenes y reportes para cerrarlos.

### Incidencia resuelta durante UAT — 2026-10-14

La ruta de programas aprobados de Pregrado devolvió HTTP 500. Dos controladores
habían conservado por error atributos `ORM\Entity` y `ORM\Table`, lo que hacía
que Symfony los excluyera del contenedor de servicios. Se retiraron esos
atributos, se añadieron pruebas funcionales para las tres rutas afectadas y se
actualizó Doctrine Fixtures para eliminar la deprecación descubierta durante la
regresión.

El hotfix `6df51ad` quedó desplegado como release
`20261014135400-6df51ad`, precedido por una salva verificada. La regresión pasó
con 67 pruebas y 283 aserciones. El usuario repitió el recorrido sobre
`/pregrado/solicitud_programa_academico_aprobado/` y confirmó que la página
cargó correctamente.

### Cierre de recorridos P1 — 2026-10-14

Tras aplicar el hotfix, el usuario autorizado completó los recorridos de planes
de estudio, documentos y programas aprobados de Pregrado; formación académica,
grados científicos y comisiones de Posgrado; comisiones, plan anual e informes
de autoevaluación; consolidados AE2/AE3; y el catálogo de cursos académicos.
Las once rutas cargaron correctamente con los datos y permisos de su sesión.

Con esta evidencia quedan correctos UAT-105 y UAT-201 a UAT-204. No se
realizaron operaciones de escritura durante estos recorridos.

### Cierre de recorridos P2 — 2026-10-14

El usuario autorizado recorrió diez listados y detalles de Convenios y
Cooperación, Trámites, Economía, Informatización, Visitas y Notificaciones. Las
rutas mostraron correctamente sus datos y no se enviaron avisos externos.

Durante el recorrido se detectó que el acceso directo a
`/notificaciones/usuario` mostraba únicamente el fragmento destinado al
desplegable AJAX, sin la estructura ni los estilos globales. El controlador
redirige ahora los accesos normales a la vista completa de notificaciones y
conserva el fragmento para peticiones AJAX. La plantilla completa hereda además
los recursos CSS y JavaScript de la plantilla base. El release
`20261014182200-1b75dae` se desplegó con salva previa y el usuario confirmó la
presentación correcta.

Con esta evidencia quedan correctos UAT-301 a UAT-305. La regresión asociada
quedó en 68 pruebas y 292 aserciones, sin deprecaciones ni vulnerabilidades
conocidas.

### Validación manual de trazas — 2026-10-14

La primera comprobación no mostró eventos porque los identificadores de inicio,
cierre, objeto de autenticación y tipo de sesión estaban configurados como
`0` en el entorno desplegado. Tras alinearlos con los catálogos de la copia
autorizada, se detectó además que el interruptor de trazas ocultaba una
excepción del repositorio y respondía como si hubiera guardado el cambio.

El endpoint se corrigió para persistir mediante el `EntityManager` soportado y
devolver un error HTTP real si falla. Se activó la configuración solicitada por
el usuario, se desplegó el commit `9eca9af` con salva previa y la regresión pasó
con 69 pruebas y 303 aserciones. El usuario repitió cierre, inicio y consulta
del listado, y confirmó la aparición de las trazas. Con esta evidencia queda
correcto UAT-006.

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

La base restaurada conservaba referencias a documentos e imágenes, pero el
directorio persistente de la nueva VM estaba vacío. El 21 de octubre de 2026
se realizó una copia inicial, limitada a aproximadamente 10 MB/s y sin borrar
el origen, desde la instalación operativa hacia
`/var/www/akademos/shared/uploads`. La comparación registró 2.003 archivos,
1.039.182.804 bytes, tres archivos vacíos heredados y ninguna diferencia en la
verificación completa por checksum. Dos documentos de Convenios que antes
devolvían 404 respondieron HTTP 200 desde el backend y por HTTPS externo.

Permanece pendiente la comprobación visual del contenido por el usuario y una
sincronización incremental final, porque la instalación anterior continúa
prestando servicio y puede recibir archivos nuevos antes de la transición.

Ejecutar al menos un caso realista de cada tecnología:

- [x] PDF mediante Apache FOP (prevalidado en la validación Ubuntu).
- [x] PDF mediante Dompdf (prevalidado por PHPUnit).
- [x] Exportación XLSX con PhpSpreadsheet (prevalidado por PHPUnit).
- [x] Descarga y contenido de documentos previamente almacenados, confirmados
      desde el acceso HTTPS de la nueva VM.
- [x] Carga de un archivo permitido dentro del límite configurado (prevalidada en PHPUnit para documentos de planes de estudio).
- [x] Rechazo de tipo MIME o tamaño no permitido (prevalidado en PHPUnit; límite de 32 MB).
- [ ] Confirmación de que los temporales se crean en `var/tmp/export/` y se
      eliminan según el procedimiento operativo.

Verificar que nombre, tipo MIME, codificación, contenido, totales y filtros
coinciden con el sistema de referencia.

## Integridad y comparación

- [x] Comparar conteos exactos de tablas entre la instalación operativa y la
      nueva copia. Se identificó el delta que deberá capturar el volcado final.
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
| UAT-001 | Correcto | Usuario autorizado SGU | 2026-10-14 | Salud HTTP 200 y portadas autenticadas correctas |
| UAT-002 | Pendiente |  |  |  |
| UAT-003 | Pendiente |  |  |  |
| UAT-004 | Pendiente |  |  |  |
| UAT-005 | Correcto | Usuario autorizado SGU | 2026-10-14 | Las 16 portadas cargaron datos, estadísticas, gráficos y menús sin errores |
| UAT-006 | Correcto | Usuario autorizado SGU | 2026-10-14 | Inicio y cierre visibles; configuración corregida en `9eca9af` |
| UAT-101 | Correcto | Usuario autorizado SGU | 2026-10-14 | Listado, búsqueda y detalle de Estructura correctos |
| UAT-102 | Correcto | Usuario autorizado SGU | 2026-10-14 | Listado, búsqueda y detalle de Institución correctos |
| UAT-103 | Correcto | Usuario autorizado SGU | 2026-10-14 | Listado, búsqueda y detalle de Personal correctos |
| UAT-104 | Correcto | Usuario autorizado SGU | 2026-10-14 | Listado, búsqueda y detalle de Administración correctos |
| UAT-105 | Correcto | Usuario autorizado SGU | 2026-10-14 | Idiomas y curso académico correctos |
| UAT-201 | Correcto | Usuario autorizado SGU | 2026-10-14 | Solicitudes, programas aprobados, planes y documentos de Pregrado correctos |
| UAT-202 | Correcto | Usuario autorizado SGU | 2026-10-14 | Solicitudes, programas, grados científicos y comisiones de Posgrado correctos |
| UAT-203 | Correcto | Usuario autorizado SGU | 2026-10-14 | Solicitudes, comisiones, plan anual e informes de Evaluación correctos |
| UAT-204 | Correcto | Usuario autorizado SGU | 2026-10-14 | Listados, detalles y consolidados AE2/AE3 correctos |
| UAT-301 | Correcto | Usuario autorizado SGU | 2026-10-14 | Convenios, cooperación y catálogos relacionados correctos |
| UAT-302 | Correcto | Usuario autorizado SGU | 2026-10-14 | Fichas, pasaportes y planes de misión consultados correctamente |
| UAT-303 | Correcto | Usuario autorizado SGU | 2026-10-14 | Conceptos de gasto y obsequios consultados correctamente |
| UAT-304 | Correcto | Usuario autorizado SGU | 2026-10-14 | Sistemas, servicios y conectividad consultados correctamente |
| UAT-305 | Correcto | Usuario autorizado SGU | 2026-10-14 | Visitas y notificaciones correctas; incidencia visual resuelta en `1b75dae` |

