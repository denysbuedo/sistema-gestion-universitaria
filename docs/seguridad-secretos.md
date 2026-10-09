# Gestión y rotación de secretos

Este documento registra el alcance operativo de la actividad de seguridad sin
incluir valores reales. Los secretos no deben aparecer en Git, issues, correos,
capturas de pantalla ni documentación.

## Inventario

| Secreto o credencial | Uso | Ubicación autorizada | Acción requerida |
|---|---|---|---|
| `APP_SECRET` | Firma y funciones internas de Symfony | Entorno de la VM | Generar uno nuevo por entorno |
| `DATABASE_URL` / `DB_*` | Base principal e integraciones DRI | Entorno de la VM | Rotar usuario y contraseña |
| Clave privada JWT | Emisión de tokens | Archivo protegido fuera de Git | Regenerar el par |
| `JWT_PASSPHRASE` | Protección de la clave JWT | Gestor de secretos o entorno | Generar y rotar |
| `MAPBOX_ACCESS_TOKEN` | Mapas en la interfaz | Entorno de la VM | Revocar el anterior y limitar el nuevo |
| `API_FUC` | Servicio externo FUC | Entorno de la VM | Confirmar si incluye credencial y rotarla |
| `IB_API_RI_URL` | Integración externa | Entorno de la VM | Separar URL de cualquier credencial |
| `MAILER_DSN` | Envío de correo | Entorno de la VM | Rotar credenciales si se utiliza |
| DSN de Messenger/Notifier | Servicios opcionales | Entorno de la VM | Configurar solo cuando estén habilitados |
| `FIXTURE_ADMIN_*` | Usuario inicial de desarrollo | `.env.local` | Usar valores exclusivos y no productivos |

## Resultado del inventario estático

- No hay claves privadas JWT versionadas.
- No se detectaron tokens Mapbox con formato reconocible en el árbol actual.
- Las credenciales de base de datos se reciben mediante variables de entorno.
- Las plantillas reciben el token Mapbox mediante configuración de Twig.
- El usuario fijo de las fixtures se externalizó a `FIXTURE_ADMIN_EMAIL` y
  `FIXTURE_ADMIN_PASSWORD`.
- Los backups y volcados deben permanecer fuera del repositorio.

Este inventario no demuestra que las credenciales históricas sigan siendo
válidas o hayan sido revocadas. Toda credencial presente alguna vez en el
repositorio de origen debe considerarse comprometida.

## Auditoría de dependencias

La primera ejecución de `composer audit --locked` del 2026-10-08 encontró 63
avisos que afectaban a 19 paquetes. Se actualizaron de forma controlada las
versiones compatibles con la rama actual y se ejecutó nuevamente toda la suite.
El resultado final de la auditoría es **cero avisos de seguridad conocidos**.

La aplicación continúa en Symfony 5.4. El salto a Symfony 6.4 sigue siendo una
actividad independiente debido a sus cambios de compatibilidad y deprecaciones.

Paquetes abandonados detectados:

- `behat/transliterator`, sin sustituto sugerido.
- `doctrine/annotations`, sin sustituto directo sugerido.
- `doctrine/cache`, sin sustituto directo sugerido.
- `sensio/framework-extra-bundle`, cuyas funciones deben migrarse a Symfony.
- `symfony/security-guard`, que debe migrarse al sistema de autenticadores.

No se añaden excepciones de auditoría ni se silencian avisos para dar por
cumplido el control.

## Rotación requerida

La rotación debe realizarla una persona autorizada en cada servicio. Registrar
únicamente responsable, fecha y resultado; nunca el valor generado.

- [ ] Crear un usuario de base de datos exclusivo para Akademos.
- [ ] Aplicar privilegios mínimos y retirar el usuario anterior.
- [ ] Regenerar `APP_SECRET` para desarrollo, pruebas y producción.
- [ ] Regenerar el par JWT y su passphrase.
- [ ] Revocar el token Mapbox anterior y crear uno restringido por origen.
- [ ] Rotar credenciales de FUC, DRI y demás APIs externas.
- [ ] Rotar credenciales de correo, mensajería y notificaciones si están activas.
- [ ] Configurar todos los valores en el entorno de la VM o gestor aprobado.
- [ ] Reiniciar la aplicación y validar autenticación web y JWT.
- [ ] Ejecutar un escaneo final del repositorio y del artefacto desplegable.
- [x] Actualizar los paquetes afectados y conseguir que `composer audit` no
  informe vulnerabilidades conocidas.

## Reglas de despliegue

- Cada entorno debe usar secretos diferentes.
- La aplicación no debe depender de `.env.local` en producción.
- Las claves JWT deben ser legibles solamente por la cuenta del servicio.
- El usuario de base de datos no debe ser propietario ni superusuario.
- Los tokens externos deben limitarse por permisos, origen y vigencia cuando el
  proveedor lo permita.
- La rotación debe disponer de un procedimiento de reversión que no restaure
  secretos comprometidos.

## Comprobaciones sin revelar valores

Antes de un despliegue se debe verificar que las variables requeridas existen,
que las claves JWT son legibles y que la conexión funciona. Las herramientas de
diagnóstico no deben imprimir el entorno completo ni DSN con credenciales.
