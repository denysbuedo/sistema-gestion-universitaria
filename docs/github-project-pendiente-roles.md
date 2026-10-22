# Tarjeta pendiente para GitHub Project

## Título

`[BLOQUEANTE] Validar autenticación Keycloak con autorización local en SGU`

## Estado

Implementación local completada / pendiente de aprobación de Identidad.

## Descripción

Keycloak autentica mediante LDAP, contraseña y TOTP. SGU enlaza la identidad
por `issuer + sub` y conserva la responsabilidad exclusiva de todos sus roles
y permisos. Los claims de roles OIDC no participan en la autorización.

## Criterios de aceptación

- [x] Un usuario básico local recibe 403 en rutas administrativas.
- [x] Un administrador local conserva acceso después de refrescar la sesión.
- [x] Un claim OIDC `ROLE_ADMIN` no concede privilegios en SGU.
- [x] No se solicitan roles funcionales en el scope OIDC.
- [x] La sesión autenticada persiste correctamente después del callback y entre
  peticiones.
- [x] Se conservan todos los roles y permisos funcionales locales.
- [x] Existen pruebas automáticas para recarga de sesión y autorización HTTP.
- [ ] Confirmar con Identidad que Keycloak autentica y SGU autoriza.
- [ ] Recibir aprobación técnica de Identidad.

## Restricción

No activar OIDC en producción ni eliminar el acceso local de contingencia hasta
cerrar esta tarjeta y obtener aprobación técnica de Identidad.
