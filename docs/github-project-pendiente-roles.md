# Tarjeta pendiente para GitHub Project

## Título

`[BLOQUEANTE] Autorizar ROLE_USER y ROLE_ADMIN desde Keycloak sin persistirlos en User`

## Estado sugerido

Pendiente / prioridad crítica / bloquea producción OIDC.

## Descripción

El flujo OIDC de `sgu-dev` funciona para login, TOTP, renovación, revocación y
logout. Falta desacoplar los roles institucionales del campo persistente de la
entidad local `User`. Dos aproximaciones experimentales rompieron la
persistencia de la sesión Symfony y fueron revertidas al estado estable.

## Criterios de aceptación

- `ROLE_USER` inicia sesión y recibe 403 en rutas administrativas.
- `ROLE_USER` + `ROLE_ADMIN` accede a rutas administrativas.
- Retirar `ROLE_ADMIN` y crear una sesión nueva elimina el privilegio.
- Ningún cambio de rol OIDC modifica permanentemente la entidad local `User`.
- La sesión autenticada persiste correctamente después del callback y entre
  peticiones.
- Se conservan los permisos funcionales locales acordados.
- Existen pruebas automáticas para concesión, retirada, serialización de sesión
  y autorización HTTP antes de repetir la prueba real con Identidad.

## Restricción

No activar OIDC en producción ni eliminar el acceso local de contingencia hasta
cerrar esta tarjeta y obtener aprobación técnica de Identidad.
