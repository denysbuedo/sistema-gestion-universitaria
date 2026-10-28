# Integración de notificaciones con Interoperabilidad

SGU utiliza OAuth 2.0 Client Credentials para autenticarse como cliente confidencial ante el realm `interoperabilidad`.

## Configuración

La configuración se inyecta mediante variables de entorno. El secreto del cliente no debe almacenarse en Git, archivos `.env` del proyecto ni registros de aplicación.

El envío permanecerá deshabilitado (`INTEROP_NOTIFICATIONS_ENABLED=0`) hasta que Interoperabilidad entregue:

- URL exacta del endpoint de notificaciones;
- contrato JSON y campos obligatorios;
- reglas de identificación de destinatarios;
- semántica de idempotencia;
- códigos de respuesta y política de reintentos.

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
