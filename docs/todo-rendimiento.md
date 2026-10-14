# TODO de rendimiento

Diagnóstico inicial realizado el 14 de octubre de 2026 sobre el entorno de
producción y el código de la rama `dev`. Este documento registra trabajo
pendiente; durante el análisis no se modificó la configuración ni el
comportamiento de la aplicación.

## Conclusión

No hay evidencia de que la lentitud sea causada por falta de hardware o por la
red entre HAProxy y SGU. La VM estaba prácticamente ociosa, con memoria y disco
disponibles, y las rutas públicas respondieron en aproximadamente 0,3-0,4 s
desde un equipo externo.

El principal cuello de botella comprobado está en el navegador: las plantillas
generales cargan demasiados recursos, varios son muy grandes, y los CSS/JS no
se entregan comprimidos ni con una política explícita de caché. Las páginas
autenticadas también presentan patrones de consulta que pueden degradarse, pero
faltan métricas por ruta y por consulta para atribuirles tiempos concretos.

## Evidencia obtenida

- VM: 4 CPU, 15 GiB RAM, unos 740 MiB usados, swap sin uso y carga cercana a
  cero. Disco al 17 %. No se justifica ampliar hardware con la evidencia actual.
- Desde el puesto de desarrollo, `/healthz` y `/login` tuvieron TTFB/tiempo total
  de aproximadamente 0,29-0,39 s.
- `style_login.css` pesa aproximadamente 1,20 MiB y `adminlte.min.css`, 1,33 MiB.
  Ambos se entregaron sin `Content-Encoding`, `Cache-Control` ni `Expires`.
- `base.html.twig` referencia 35 recursos locales (aprox. 3,43 MiB),
  `baseAdmin.html.twig` 63 (aprox. 5,55 MiB) y `baseAdminRI.html.twig` 57
  (aprox. 5,28 MiB), antes de contar procesamiento y solicitudes externas.
- Se cargan globalmente bibliotecas pesadas como CKEditor, FullCalendar,
  Highcharts, Leaflet y módulos de DataTables aunque la página no siempre las
  utiliza.
- El login y algunas bases dependen de Google Fonts, cdnjs y unpkg. Su latencia
  o bloqueo puede retrasar el renderizado en la red institucional.
- PostgreSQL ocupa unos 27 MiB y no muestra presión de recursos. Sin embargo,
  no están habilitados `pg_stat_statements`, `track_io_timing` ni el registro de
  consultas lentas.
- Nginx no registra `$request_time` ni `$upstream_response_time`; PHP-FPM no
  expone estado ni tiene `slowlog`. Actualmente no se puede obtener un p95 por
  ruta ni saber qué ejecución PHP consume el tiempo.
- Hay numerosas acciones que usan `findAll()` o `findBy()` sin límite ni
  paginación. La tabla de notificaciones ya contiene más de 3.600 filas.
- La relación rol-funcionalidades está configurada con `fetch: 'EAGER'`, lo que
  puede ampliar el trabajo de Doctrine al hidratar usuarios y permisos.
- El suscriptor OIDC se ejecuta en las peticiones principales, pero solo llama
  al servidor de Identidad cuando el token está a menos de 60 segundos de
  expirar. No explica una lentitud constante.

## Trabajo priorizado

### P0 - Medir antes de optimizar el backend

- [ ] Añadir a un formato de log de Nginx el tiempo total y el tiempo del
  upstream; construir p50, p95 y p99 por ruta sin registrar tokens ni datos
  sensibles.
- [ ] Habilitar de forma restringida el estado y `slowlog` de PHP-FPM, con un
  umbral inicial de 1 s.
- [ ] Activar observación controlada de PostgreSQL mediante
  `pg_stat_statements` o registro temporal de consultas lentas y conservar una
  línea base de las pantallas más utilizadas.
- [ ] Capturar una navegación autenticada con DevTools/HAR, incluyendo primera
  visita y visita con caché, para separar red, backend, descarga, parseo y
  ejecución JavaScript.

### P0 - Reducir el coste del frontend

- [ ] Comprimir CSS, JavaScript, JSON, SVG y fuentes en Nginx; evaluar Brotli en
  HAProxy/Nginx si está disponible.
- [ ] Definir versionado de activos y caché segura. No usar `immutable` de larga
  duración hasta que los nombres o URLs cambien con el contenido.
- [ ] Separar los paquetes comunes de los recursos específicos de cada página;
  cargar CKEditor, FullCalendar, Highcharts, Leaflet y extensiones de DataTables
  únicamente donde se usan.
- [ ] Servir localmente fuentes e iconos necesarios y eliminar dependencias de
  Google Fonts, cdnjs y unpkg del camino crítico.
- [ ] Excluir del despliegue mapas de fuentes y copias de bundles que no se usan
  en producción.

### P1 - Consultas y permisos

- [ ] Paginar listados, empezando por trazas, notificaciones, convenios,
  usuarios, pasaportes, fichas y catálogos que cargan colecciones completas.
- [ ] Sustituir `count(findAll())` por consultas `COUNT` y seleccionar solo las
  columnas requeridas en listados y exportaciones.
- [ ] Perfilar la hidratación de usuario/rol/funcionalidades; evaluar carga
  diferida o una caché de permisos con invalidación explícita.
- [ ] Revisar índices únicamente a partir de las consultas lentas y sus planes
  `EXPLAIN (ANALYZE, BUFFERS)`, evitando crear índices indiscriminadamente.
- [ ] Comprobar consultas repetidas por fila (N+1), en especial en listados con
  relaciones de persona, estructura, rol y estado.

### P2 - Ajustes posteriores

- [ ] Con despliegues atómicos y limpieza explícita de caché, evaluar
  `opcache.validate_timestamps=0` en producción.
- [ ] Revisar estadísticas de OPcache antes de aumentar sus 128 MiB actuales.
- [ ] Ejecutar una prueba de carga representativa y ajustar PHP-FPM solo si se
  demuestra saturación; `pm.max_children=20` no es un problema observado hoy.

## Criterios de cierre propuestos

- TTFB p95 del backend inferior a 500 ms en las rutas habituales, salvo tareas
  justificadamente pesadas.
- Primera carga de una pantalla administrativa por debajo de 1,5 s en la red
  institucional de referencia, y navegación repetida por debajo de 0,8 s.
- Recursos estáticos comprimidos y reutilizados por caché en visitas repetidas.
- Plantilla administrativa común por debajo de 1,5 MiB y 25 solicitudes antes
  de incorporar los recursos propios de la pantalla.
- Ninguna consulta habitual supera 500 ms y no existen consultas N+1 en los
  recorridos críticos acordados.
