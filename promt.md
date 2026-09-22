# PROMPT DE CONTINUACIÓN — Líderes con contraseña temporal (+ bases escalables)

> Pega esto a Codex en la conversación donde ya viene trabajando el
> proyecto. Adjuntá `migracion_password_temporal.sql` (ya probada contra
> una copia local). Este prompt tiene 3 fases: la Fase A se construye
> completa ahora; las Fases B y C quedan preparadas pero con su
> funcionalidad real apagada/simulada, para poder activarlas más
> adelante sin rediseñar nada.

---

## Fase A — El Administrador crea al Líder con contraseña temporal (construir completo)

1. **Migración**: corré `migracion_password_temporal.sql`. Agrega
   `password_temporal` (booleano) a `usuario`, y un tercer valor
   `PENDIENTE` al enum `estado` (para la Fase C, no se usa todavía en
   esta fase).

2. **Formulario de creación de Usuario (Sistema → Usuarios)**: cuando el
   Administrador crea un Líder, el campo de contraseña **no lo escribe a
   mano** — el sistema genera una contraseña temporal aleatoria (ej. 10
   caracteres, letras+números) al guardar, la hashea con
   `password_hash()`, guarda `password_temporal = 1`, y muestra esa
   contraseña generada **una sola vez**, en una pantalla/modal de
   confirmación post-guardado (tipo "Usuario creado. Contraseña temporal:
   `Xk9mPz2Qrt` — copiala y compartila con el líder, no se va a volver a
   mostrar"). Agregá un botón "Copiar" (JS simple con
   `navigator.clipboard`).

3. **Login con contraseña temporal**: en `AuthController`, después de un
   login exitoso, revisá `password_temporal`. Si es `1`, en vez de
   redirigir directo al dashboard, guardá una bandera en sesión
   (`$_SESSION['debe_cambiar_password'] = true`) y redirigí igual al
   dashboard, pero...

4. **Modal obligatorio de cambio de contraseña**: en el layout compartido
   del panel del Líder (y también Admin, por si algún admin también
   queda con contraseña temporal), si `$_SESSION['debe_cambiar_password']`
   es verdadero, mostrar un modal que **no se puede cerrar sin completar
   la acción** (sin botón de "cerrar" ni click afuera para descartarlo):
   título "Por seguridad, cambiá tu contraseña", campos "Nueva
   contraseña" + "Confirmar nueva contraseña", validación de que
   coincidan y tengan un mínimo razonable de caracteres. Al confirmar:
   actualiza `password_hash`, pone `password_temporal = 0`, limpia la
   bandera de sesión, y recién ahí el usuario puede navegar normalmente
   (podés recargar la página actual después de cerrar el modal).

5. Reforzá también a nivel de **backend**, no solo con el modal de UI:
   cualquier ruta del panel (excepto la de cambiar contraseña en sí)
   debe rechazar la petición o redirigir de vuelta si
   `debe_cambiar_password` sigue en `true` — el modal es la experiencia
   visual, pero el bloqueo real tiene que existir también del lado
   servidor.

## Fase B — Envío de credenciales por correo (dejar preparado, NO implementar el envío real)

No integres un servicio real de correo todavía. Sí dejá la arquitectura
lista para no tener que rediseñar nada cuando se agregue:

- Creá `app/services/NotificacionService.php` con un método
  `enviarCredencialesLider(array $usuario, string $passwordTemporal): void`
  que, por ahora, solo registre en un log simple (`error_log(...)` o un
  archivo `storage/logs/notificaciones.log`) el contenido que se
  "enviaría" — algo como `"[SIMULADO] Correo a {$usuario['correo']}:
  contraseña temporal {$passwordTemporal}"`.
- Llamá a este método desde el flujo de creación de Usuario de la Fase A
  (después de generar la contraseña), aunque por ahora no mande nada
  real. Dejá un comentario `// TODO: integrar PHPMailer o similar para
  envío real por SMTP`.
- La pantalla de "contraseña generada" (punto 2 de la Fase A) se sigue
  mostrando igual mientras tanto — el correo es un canal adicional
  futuro, no un reemplazo de mostrarla en pantalla.

## Fase C — Autorregistro de Líderes con aprobación del Admin (dejar preparado, funcionalidad simulada/detrás de flag)

Como todavía no está confirmado si el profesor va a pedir esto, construilo
pero **detrás de una constante de configuración** fácil de prender/apagar:

```php
// app/config/features.php (archivo nuevo)
const AUTORREGISTRO_LIDERES_HABILITADO = false;
```

1. **Página pública "Solicitar acceso"** (ruta `registro.lider`, sin
   requerir sesión): formulario con nombre, apellido, correo, y el
   programa al que dice pertenecer (select). Si
   `AUTORREGISTRO_LIDERES_HABILITADO` es `false`, esta ruta debe mostrar
   un mensaje simple ("El autorregistro no está habilitado, contactá al
   Administrador") en vez del formulario — así queda armada pero inerte.
2. **Al enviar la solicitud** (cuando el flag esté en `true`): crea un
   usuario con rol `LIDER_PROGRAMA`, `estado = 'PENDIENTE'`, sin
   contraseña utilizable todavía (podés generar una temporal igual, pero
   no se comunica hasta que se apruebe), y una fila pendiente en algún
   registro de "solicitud" — puede ser simplemente el propio `usuario`
   con `estado = 'PENDIENTE'`, no hace falta una tabla nueva.
3. **Cola de aprobación en Sistema → Usuarios**: agregá una sub-vista o
   filtro "Solicitudes pendientes" (usuarios con `estado = 'PENDIENTE'`),
   con botones **Aprobar** (pasa a `ACTIVO`, genera la contraseña
   temporal como en la Fase A y la muestra/loguea igual) y **Rechazar**
   (pasa a `INACTIVO` o elimina el registro, tu criterio, preguntame si
   tenés dudas).
4. El login (`AuthController::intentarLogin`) ya debe tratar
   `estado = 'PENDIENTE'` igual que `INACTIVO` — no permitir acceso,
   mostrando un mensaje distinto ("Tu solicitud está pendiente de
   aprobación") en vez del genérico "correo o contraseña incorrectos".

## Reglas que siguen aplicando

`Auth::requerirRol('ADMINISTRADOR')` en la gestión de usuarios y en la
cola de aprobación. 100% PDO preparado. 100% `htmlspecialchars()`.
SweetAlert2 para las confirmaciones de Aprobar/Rechazar. El modal
obligatorio de cambio de contraseña usa el mismo estilo visual
(`design-tokens.css`, tarjeta centrada) que el resto del sistema.

## Formato de entrega

Empezá por la Fase A completa (es la que se usa ya mismo) y confirmame
que funciona de punta a punta: crear un líder nuevo, loguearte con la
contraseña temporal, ver el modal obligatorio, cambiarla, y confirmar que
ya no vuelve a aparecer en el siguiente login. Después las Fases B y C,
dejándolas preparadas pero sin funcionalidad real activa (correo
simulado, autorregistro detrás del flag en `false`). Decime qué archivos
tocaste en cada fase.

el migracion_password_temporal.sql esta en la carpeta datos nuevos


2. Verificar la disponibilidad cruzada entre líderes (puede que no sea bug)

Contexto: el Administrador creó un horario en el espacio "Aula Multimedia 101" (Lunes 07:00-09:00, estado CERRADO). Al entrar como Líder y consultar disponibilidad, se seleccionó por error un espacio DISTINTO ("Auditorio Los Libertadores"), que lógicamente aparece libre porque no tiene nada asignado ahí. Antes de tocar código, hacé esta prueba puntual:

Iniciá sesión como el líder de prueba.
Andá a Espacios → Disponibilidad.
Seleccioná específicamente "Aula Multimedia 101" (el mismo espacio donde el Admin creó el horario).
Confirmame: ¿la franja de Lunes 07:00-09:00 aparece como "Ocupado" o sigue en "Libre"?

Si aparece "Ocupado" correctamente → no hay bug, era solo que se estaba mirando un espacio distinto. No cambies nada, solo confirmame el resultado.

Si sigue apareciendo "Libre" estando mal → ahí sí hay un bug real en la consulta de disponibilidad. Revisá que la query cruce correctamente contra la tabla horario real, considerando:

Filtrar por id_espacio (el seleccionado), id_periodo (el periodo activo) y dia_semana.
Marcar como "Ocupado" cualquier horario que exista en ese cruce sin importar su estado, EXCEPTO si en algún momento agregamos un estado tipo ELIMINADO/DESCARTADO — un horario en BORRADOR, PROPUESTA, CONFIRMADO, CERRADO o MODIFICADO sigue ocupando el recurso igual, porque la base de datos ya bloquea ese slot exacto con el índice único uq_horario_espacio_slot (no deberían poder existir dos horarios reales solapados ahí de todas formas).
Confirmar que el filtro de franja horaria (07:00-09:00 en tu grilla) realmente compara contra el hora_inicio/hora_fin guardado y no contra un valor mockeado o hardcodeado en la vista.