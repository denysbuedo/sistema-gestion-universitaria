# Análisis inicial — Transformación Digital

Fecha: 28 de octubre de 2026

## Alcance revisado

- 17 controladores y 106 rutas.
- 69 plantillas: portada, listados, altas, modificaciones, detalles y operaciones especiales.
- Áreas operativas: sistemas informáticos, trámites y servicios, conectividad, centros de datos, telefonía móvil y Nauta Hogar.
- Catálogos: marcas, modelos, públicos objetivo, sistemas operativos, alcances, tipos de sistemas, visibilidad, servicios contratados y procesos.

## Ajuste visual aplicado

- Denominación visible cambiada de **Informatización** a **Transformación Digital** en portada, selector superior y tarjetas de acceso del inicio.
- Lenguaje visual institucional aplicado transversalmente a las pantallas del módulo: fondo, tarjetas, encabezados, tablas, formularios, botones, menús y ventanas modales.
- Portada ajustada a bordes discretos, azul institucional y ausencia de sombras o movimientos decorativos.
- Se conservaron las rutas, permisos, namespaces y esquema `informatizacion` para no introducir una migración técnica sin valor funcional.

## Hallazgos para las rondas funcionales

1. El menú mezcla activos, servicios y catálogos; conviene reorganizarlo por **Infraestructura**, **Servicios digitales**, **Telefonía y conectividad** y **Configuración**.
2. El acceso a Nauta Hogar está condicionado exteriormente por un permiso de Evaluación (`ROLE_EVALAUCION_GEST_CATEGORIA_ACREDITACION`), aunque dentro se comprueba el permiso correcto. Debe corregirse en la ronda de permisos.
3. Servicios contratados y Procesos se muestran bajo comprobaciones de Visibilidad; necesitan permisos propios o una regla explícita documentada.
4. Hay dos flujos de alta de Nauta Hogar (`new` y `newV2`) y deben consolidarse.
5. Los detalles se presentan como bloques de texto en negrita; deben convertirse en fichas legibles y consistentes.
6. Los listados repiten el patrón heredado de filtros y acciones; hay que revisar entidad, alcance por universidad, exportación y menú de opciones en cada uno.
7. Telefonía móvil posee operaciones adicionales —responsables y recargas— que requieren una revisión específica de trazabilidad, vigencia y permisos.
8. La terminología necesita normalización: “Centro de Datos”, “Centro de Datos Virtual”, “trámites o servicios”, “alcances” y singular/plural no se usan de forma uniforme.
9. La portada calcula indicadores y gráficos al cargar. Debemos medir consultas, tiempo de respuesta y filtrado por entidad antes de decidir optimizaciones.
10. La búsqueda global heredada fija 500 px en escritorio; el estilo común limita el desbordamiento, pero conviene eliminar esa medida fija al revisar la respuesta móvil.

## Orden sugerido de trabajo

1. Portada y reglas de visibilidad por entidad.
2. Sistemas informáticos.
3. Trámites y servicios.
4. Conectividad y centros de datos.
5. Telefonía móvil y Nauta Hogar.
6. Catálogos, permisos, exportaciones y auditoría.

