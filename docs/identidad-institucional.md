# Integración con la identidad institucional

Akademos debe delegar autenticación, segundo factor y recuperación de
credenciales en el Keycloak institucional publicado en
`https://identidad.reduniv.edu.cu`. La aplicación no implementará ni almacenará
semillas TOTP, códigos de recuperación o nuevas contraseñas.

## Decisión arquitectónica

- Aplicación web con backend: OpenID Connect Authorization Code Flow y cliente
  confidencial.
- PKCE obligatorio con `S256`, incluso para los clientes confidenciales.
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
| Realm | `reduniv-institucional` |
| Client ID | `sgu-dev`, `sgu-test` y `sgu` según ambiente |

URLs que deben confirmarse antes de solicitar el alta:

| Ambiente | Inicio | Redirect URI | Post-logout | Origen web |
|---|---|---|---|---|
| Desarrollo local | `http://127.0.0.1:8000/` | `http://127.0.0.1:8000/oidc/check` | `http://127.0.0.1:8000/` | `http://127.0.0.1:8000` |
| Pruebas | Pendiente de DNS/HTTPS | Pendiente exacta | Pendiente exacta | Pendiente exacto |
| Producción | `https://sgu.mes.gob.cu/` | `https://sgu.mes.gob.cu/oidc/check` | `https://sgu.mes.gob.cu/` | `https://sgu.mes.gob.cu` |

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
La clave de enlace local será la pareja `issuer + sub`; `sub` no se considerará
global fuera de su emisor.

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

## Estado del piloto de desarrollo

> **BLOQUEANTE PARA PRODUCCIÓN — AUTORIZACIÓN POR ROLES NO RESUELTA**
>
> El login, TOTP, renovación, revocación y logout OIDC están validados, pero la
> aplicación todavía no aplica de forma segura y dinámica `ROLE_USER` y
> `ROLE_ADMIN` desde el token de Keycloak. La retirada de `ROLE_ADMIN` debe
> reflejarse en una nueva sesión sin modificar permanentemente la entidad local
> `User` y sin romper la persistencia de la sesión Symfony. SGU no debe activar
> OIDC en producción ni retirar el login de contingencia hasta resolver y
> aprobar este punto.

Validado el 10 de octubre de 2026:

- [x] Redirección desde SGU al cliente `sgu-dev`.
- [x] Authorization Code Flow con PKCE S256, `state` y `nonce` de un solo uso.
- [x] Autenticación Keycloak → LDAP → contraseña → TOTP.
- [x] Callback exacto en `http://127.0.0.1:8000/oidc/check`.
- [x] Vinculación controlada mediante `issuer + sub`, sin enlace automático por correo.
- [x] Recepción y autorización de `ROLE_USER`.
- [x] Firma, emisor, audiencia, expiración, fecha de emisión, nonce y roles cubiertos por pruebas automatizadas.
- [x] Comprobación local de que secretos y tokens no aparecen en los registros.
- [x] Rechazo interactivo de OTP incorrecto (`Invalid authenticator code`) y acceso únicamente tras presentar un TOTP válido.
- [x] Revocación real en Keycloak detectada al renovar: SGU invalidó la sesión local y redirigió al login sin bucles.
- [x] Renovación satisfactoria de una sesión no revocada durante un periodo superior al access token; el usuario continuó trabajando sin reautenticarse.
- [x] Logout central comprobado en navegador, con retorno autorizado y sesión local invalidada.
- [ ] Recuperación institucional del segundo factor.
- [ ] Prueba de `ROLE_ADMIN` con una cuenta autorizada.
- [ ] Aprobación técnica del equipo de Identidad.

La prueba de retirada dinámica de `ROLE_ADMIN` queda pendiente. Un primer
intento de aplicar el rol exclusivamente sobre una copia temporal del usuario
provocó una regresión en la persistencia de la sesión Symfony y fue revertido
al último estado estable. No se repetirá el cambio hasta disponer de pruebas
funcionales específicas para login, recarga de usuario y autorización tras
añadir y retirar roles.

### Criterios para cerrar el bloqueo de roles

- Un token con `ROLE_USER` permite iniciar sesión y trabajar, aunque la cuenta
  local hubiera tenido anteriormente privilegios administrativos.
- Un token con `ROLE_USER` no accede a `/administracion/usuario/` y obtiene 403.
- Un token con `ROLE_USER` y `ROLE_ADMIN` accede a la sección administrativa.
- Retirar `ROLE_ADMIN`, revocar la sesión y autenticarse de nuevo elimina el
  acceso administrativo sin modificar manualmente la BD de SGU.
- Los roles efectivos sobreviven al almacenamiento y recarga de la sesión
  Symfony.
- Los permisos funcionales locales distintos de `ROLE_USER` y `ROLE_ADMIN`
  conservan su comportamiento acordado.
- Las pruebas anteriores se ejecutan automáticamente sin usar Keycloak real ni
  requerir intervención del usuario piloto.
- La solución supera después el recorrido real con Identidad y recibe su
  aprobación técnica.

La aplicación conserva los tokens únicamente en la sesión del backend. Cuando
el access token se aproxima a su expiración, usa el refresh token contra el
token endpoint, valida criptográficamente el nuevo access token y reemplaza el
refresh token si Keycloak lo rota. Si la renovación falla por expiración o
revocación, invalida la sesión local y obliga a autenticarse nuevamente.
