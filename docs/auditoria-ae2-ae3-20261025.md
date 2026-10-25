# Auditoría de consistencia AE2/AE3

Fecha de corte: 25 de octubre de 2026  
Fuente: base de datos de SGU en producción  
Carácter: diagnóstico histórico no bloqueante

## Alcance

Esta auditoría compara los partes AE3 existentes con el AE2 de la misma entidad, año y mes. No modifica, invalida ni bloquea registros históricos. Los documentos firmados y los datos existentes permanecen íntegros.

## Resultado

| Comprobación | Cantidad |
|---|---:|
| AE3 existentes | 275 |
| AE3 con AE2 del mismo período | 266 |
| AE3 sin AE2 del mismo período | 9 |
| Diferencias en total de plantilla cubierta | 47 |
| Diferencias en profesores a tiempo completo | 155 |
| AE3 con suma incorrecta en total de cuadros | 1 |
| AE3 con suma incorrecta en total de técnicos | 1 |
| AE3 con suma incorrecta en total general | 1 |

## Criterio adoptado

El AE2 es la fuente mensual oficial. Para los nuevos registros, el AE3 trimestral debe corresponder al AE2 de marzo, junio, septiembre o diciembre de la misma entidad y año.

Las validaciones se aplican únicamente al registro de nuevos partes después del despliegue. No se aplican con carácter retroactivo y, en esta primera etapa, tampoco impiden modificar registros históricos.

## Tratamiento recomendado de las diferencias históricas

1. Conservar el dato y el documento firmado originales.
2. Revisar las diferencias con cada entidad sin corregir automáticamente.
3. Si procede una rectificación, efectuarla mediante el procedimiento institucional y conservar evidencia de la modificación.
4. Utilizar este corte como línea base para medir la calidad de los nuevos envíos.
