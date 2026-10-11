# Tarjeta pendiente para GitHub Project

## Título

`[BLOQUEANTE] Autorizar ROLE_USER y ROLE_ADMIN desde Keycloak sin persistirlos en User`

## Estado

En progreso / implementación local completada / pendiente de validación real y
aprobación de Identidad.

## Descripción

Los roles institucionales quedaron desacoplados del campo persistente de la
entidad local `User`. Un proveedor de usuarios conserva los roles de sesión al
refrescar la identidad y sustituye dinámicamente solo `ROLE_USER` y
`ROLE_ADMIN`, sin retirar permisos funcionales locales.

## Criterios de aceptación

- [x] `ROLE_USER` inicia sesión y recibe 403 en rutas administrativas.
- [x] `ROLE_USER` + `ROLE_ADMIN` accede a rutas administrativas.
- [x] Retirar `ROLE_ADMIN` y crear una sesión nueva elimina el privilegio.
- [x] Ningún cambio de rol OIDC modifica permanentemente la entidad local `User`.
- [x] La sesión autenticada persiste correctamente después del callback y entre
  peticiones.
- [x] Se conservan los permisos funcionales locales distintos de los roles institucionales.
- [x] Existen pruebas automáticas para concesión, retirada, serialización de sesión
  y autorización HTTP antes de repetir la prueba real con Identidad.
- [ ] Repetir concesión y retirada con Keycloak real.
- [ ] Recibir aprobación técnica de Identidad.

## Restricción

No activar OIDC en producción ni eliminar el acceso local de contingencia hasta
cerrar esta tarjeta y obtener aprobación técnica de Identidad.
