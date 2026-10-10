# Integración con la identidad institucional

Akademos debe delegar autenticación, segundo factor y recuperación de
credenciales en el Keycloak institucional publicado en
`https://identidad.reduniv.edu.cu`. La aplicación no implementará ni almacenará
semillas TOTP, códigos de recuperación o nuevas contraseñas.

## Decisión arquitectónica

- Aplicación web con backend: OpenID Connect Authorization Code Flow y cliente
  confidencial.
- Realm: el realm institucional común que indique el equipo de Identidad.
- Segundo factor: flujo central de Keycloak; inicialmente TOTP.
- API: aceptar tokens Bearer emitidos por Keycloak, validando firma, `iss`,
  `aud`, expiración y tipo.
- Integraciones máquina a máquina: Client Credentials y cuentas de servicio.
- Autorización: roles/permisos explícitos; iniciar sesión no concede acceso por
  sí solo.
- Cierre de sesión: invalidar la sesión local y ejecutar el logout OIDC con
  redirección registrada.

No se usarán Implicit Flow, Password Grant, Direct Access Grants ni acceso a la
base de datos interna de Keycloak.

## Información para registrar el cliente

| Campo | Valor o responsable |
|---|---|
| Nombre | Akademos / Sistema de Gestión Universitaria |
| Descripción | Sistema núcleo para gestión universitaria del Ministerio |
| Tipo | Aplicación web con backend y APIs |
| Cliente | Confidencial |
| Flujo | Authorization Code Flow |
| Responsable técnico | Pendiente de confirmar institucionalmente |
| Responsable funcional | Pendiente de confirmar institucionalmente |
| Clasificación | Sistema núcleo; 2FA obligatorio según política del realm |
| Realm | Realm institucional común, pendiente de asignación |
| Client ID solicitado | `akademos` o el identificador que apruebe Identidad |

URLs que deben confirmarse antes de solicitar el alta:

| Ambiente | Inicio | Redirect URI | Post-logout | Origen web |
|---|---|---|---|---|
| Desarrollo local | `http://127.0.0.1:8000/` | `http://127.0.0.1:8000/oidc/check` | `http://127.0.0.1:8000/` | `http://127.0.0.1:8000` |
| Pruebas | Pendiente de DNS/HTTPS | Pendiente exacta | Pendiente exacta | Pendiente exacto |
| Producción | Pendiente de DNS/HTTPS | Pendiente exacta | Pendiente exacta | Pendiente exacto |

No se solicitarán comodines en redirecciones u orígenes. Las credenciales del
cliente se intercambiarán mediante un canal seguro y se almacenarán fuera del
repositorio.

## Roles iniciales

La matriz definitiva debe validarse funcionalmente. Como mínimo deben mapearse:

- `ROLE_ADMIN`: administración funcional y de usuarios autorizados.
- `ROLE_USER`: acceso autenticado básico.
- Authorities actuales asociadas a roles internos: requieren inventario antes
  de decidir si se convierten en roles de cliente Keycloak o permanecen como
  permisos de negocio administrados por Akademos.

El identificador estable de enlace será el claim `sub`, no el correo. El correo
puede cambiar y se utilizará únicamente como atributo informativo/verificado.

## Transición desde el legado

1. Registrar clientes separados para desarrollo, pruebas y producción según la
   decisión del equipo de Identidad.
2. Implementar OIDC detrás de `OIDC_ENABLED=0` y probarlo en desarrollo.
3. Añadir el identificador `sub` a las cuentas locales necesarias para enlazar
   permisos de negocio durante la transición.
4. Probar login, enrolamiento TOTP, ausencia/errores de OTP, recuperación,
   expiración, renovación, revocación, logout y roles.
5. Activar primero en pruebas; conservar un procedimiento de reversión
   temporal y auditado.
6. Desactivar el login web local y la emisión JWT basada en contraseña cuando
   Keycloak sea aprobado para producción.

El endpoint actual `/api/login_check` recibe usuario y contraseña y emite JWT
local. Debe considerarse transitorio y retirarse: no cumple el modelo
institucional para nuevas integraciones.

## Criterios de aceptación

- Descubrimiento OIDC y JWKS desde el issuer aprobado.
- Validación estricta de firma, emisor, audiencia, expiración y tipo de token.
- Estado y nonce verificados en Authorization Code Flow.
- Roles insuficientes producen denegación aunque el usuario esté autenticado.
- Logout local y central comprobados.
- Ninguna contraseña, semilla TOTP, código, secreto de cliente o token aparece
  en logs, trazas o repositorio.
- Todos los casos exigidos por Identidad pasan y existe aprobación técnica.
