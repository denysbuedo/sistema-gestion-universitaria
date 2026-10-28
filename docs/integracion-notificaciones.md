# Integración de notificaciones con Interoperabilidad

SGU utiliza OAuth 2.0 Client Credentials para autenticarse como cliente confidencial ante el realm `interoperabilidad`.

## Configuración

La configuración se inyecta mediante variables de entorno. El secreto del cliente no debe almacenarse en Git, archivos `.env` del proyecto ni registros de aplicación.

Endpoint de envío:

```text
POST https://api.reduniv.edu.cu/api/v1/notifications/email
```

Contrato JSON confirmado:

```json
{
  "recipient": "persona@dominio.cu",
  "subject": "Asunto",
  "text_body": "Contenido en texto plano"
}
```

La API respondió `202 accepted_by_relay` en la prueba controlada. SGU no debe reintentar automáticamente ante timeout, HTTP 500 o HTTP 502 porque la entrega pudo ser aceptada antes de perderse la respuesta.

El envío permanecerá deshabilitado (`INTEROP_NOTIFICATIONS_ENABLED=0`) hasta que la VM de SGU tenga conectividad HTTPS operativa con el endpoint de token y la API.

Cuando está habilitado, cada notificación interna persistida por SGU genera un único correo al email del usuario destinatario. El correo se intenta después de confirmar la escritura en la base de datos. Un fallo del servicio externo no revierte la operación funcional y no genera reintentos automáticos.

## Diagnóstico de autenticación

```bash
php bin/console app:interop:notifications:check
```

El comando solicita un token, valida la audiencia y el rol requeridos y solo informa el resultado. Nunca imprime el token ni el secreto.

## Seguridad

- Los tokens se mantienen exclusivamente en memoria y se renuevan antes de expirar.
- No se registran secretos, tokens ni respuestas completas de autenticación.
- El cliente rechaza tokens que no contengan la audiencia y el rol configurados.
- Los errores expuestos están sanitizados y no contienen credenciales.
- El cliente HTTP realiza un único intento por llamada; no existe política automática de reintentos.
