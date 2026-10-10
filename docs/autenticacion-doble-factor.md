# Autenticación de doble factor

Akademos adoptará TOTP mediante aplicaciones autenticadoras como segundo
factor del acceso web. No se utilizará SMS: añade dependencia del operador,
coste operativo y exposición a ataques de intercambio de SIM.

## Política de implantación

1. Piloto opt-in en desarrollo con cuentas administrativas de prueba.
2. Enrolamiento con confirmación de un primer código TOTP.
3. Generación de códigos de recuperación de un solo uso.
4. Obligatoriedad para administradores después de validar soporte y
   recuperación de cuentas.
5. Extensión gradual a los demás perfiles mediante una política institucional.

La instalación del proveedor TOTP no activa automáticamente 2FA para ningún
usuario. Una cuenta solo debe exigir el segundo factor después de completar y
confirmar el enrolamiento.

## Protección de secretos

La semilla TOTP permite generar códigos válidos y no debe almacenarse en texto
claro. Antes de persistirla se implementará cifrado autenticado en la capa de
aplicación, con una clave separada de la base de datos y suministrada mediante
el almacén institucional de secretos. Los códigos de recuperación se guardarán
como hashes de un solo sentido y cada código se invalidará al utilizarse.

Nunca registrar semillas, códigos TOTP, códigos de recuperación ni códigos QR
en logs, trazas, incidencias o correo electrónico.

## Alcance

El segundo factor protege el firewall web interactivo. La API JWT requiere una
política diferente para clientes máquina a máquina; no debe simular TOTP ni
compartir credenciales de usuarios. La futura interoperabilidad utilizará
autenticación propia de cliente, permisos de solo lectura y credenciales
rotables.

## Recuperación

- El usuario conserva códigos de recuperación una sola vez durante el alta.
- Soporte no puede consultar la semilla ni los códigos originales.
- El restablecimiento exige identidad verificada, autorización registrada y
  revocación de la semilla anterior.
- Cada alta, uso de recuperación, desactivación y restablecimiento debe quedar
  auditado sin incluir material secreto.
