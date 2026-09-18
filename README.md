# PROMPT MAESTRO — Robustecer UniFET (estructura + contenido real por módulo)

> Pega esto a Codex en la conversación donde ya viene trabajando el
> proyecto. No es "desde cero": reutilizá todo lo que ya funciona (login,
> Auth, Router, dashboard, CRUDs existentes). Este prompt cubre DOS cosas
> a la vez: (1) la reestructuración del sidebar por tarea con tabs, y
> (2) qué contenido exacto debe tener cada pantalla, según la
> documentación funcional del proyecto y el esquema real de
> `schema.sql`.

---

## PARTE 1 — Arquitectura de información del sidebar (Administrador)

Reemplazá el sidebar actual (un link por tabla) por 7 secciones
agrupadas por tarea, cada una con pestañas internas:

```
Dashboard
Institución        → tabs: Facultades | Programas
Espacios           → tabs: Sedes | Bloques | Espacios
Académico          → tabs: Docentes | Asignaturas | Periodos | Oferta académica | Restricciones
Horarios           → tabs: General | Generar con FET | Propuestas FET | Auditoría
Reportes
Importar/Exportar
Sistema            → tabs: Personalización | Usuarios
```

No reescribas la lógica de los CRUDs que ya funcionan (Facultades,
Programas, Sedes, Docentes, Asignaturas, Periodos) — solo reorganizalos
bajo este esquema de rutas con prefijo por sección (`admin.institucion.*`,
`admin.espacios.*`, `admin.academico.*`, `admin.horarios.*`,
`admin.sistema.*`) y un partial compartido de tabs
(`app/views/shared/tabs.php`) que reciba `[etiqueta, ruta, activa]` y
dibuje la franja de pestañas arriba de cada pantalla. El ítem del sidebar
queda marcado "activo" mientras estés en cualquier tab de su sección, no
solo en la ruta exacta.

El sidebar del **Líder de Programa** se reorganiza igual, pero con su
propio set de secciones (ya construido, no lo toques salvo que falte
"Horarios" con sus tabs: General | Generar con FET | Propuestas FET |
Auditoría, igual que en Admin pero filtrado siempre por su/sus
programa(s) vía `programa_lider`).

## PARTE 2 — Contenido exacto de cada módulo

Para cada CRUD, la **tabla de listado** debe mostrar las columnas
indicadas, con filtros arriba cuando se menciona, botón "+ Nuevo", y
acciones Editar/Desactivar (nunca DELETE físico, siempre `estado =
'INACTIVO'`). El **formulario** crea/edita exactamente los campos
listados. Todo contra las tablas reales de `unifet.sql` — si un
campo no existe en el schema, no lo inventes, avisame.

### Institución → Facultades
- Tabla: Código, Nombre, Estado, Acciones.
- Formulario: código, nombre, estado (activo/inactivo).
- Regla: código único por institución (ya lo garantiza la BD con
  `uq_facultad_codigo`, pero mostrá el error de forma legible si falla).

### Institución → Programas
- Tabla: Código, Nombre, Facultad, Estado, Acciones. Filtro por Facultad.
- Formulario: código, nombre, **select de Facultad** (obligatorio),
  estado.

### Espacios → Sedes
- Tabla: Código, Nombre, Dirección, Estado, Acciones.
- Formulario: código, nombre, dirección, estado.

### Espacios → Bloques
- Tabla: Código, Nombre, Sede, Estado, Acciones. Filtro por Sede.
- Formulario: código, nombre, **select de Sede**, estado.

### Espacios → Espacios
- Tabla: Código, Nombre, Bloque, Sede (derivada del bloque), Tipo,
  Capacidad, Estado, Acciones. Filtros: Sede, Bloque, Tipo, Capacidad
  mínima.
- Formulario: código, nombre, **select de Bloque** (que a su vez filtra
  por Sede elegida antes, en cascada), tipo (`AULA`, `LABORATORIO`,
  `SALA`, `AUDITORIO`, `OTRO`), capacidad, características (texto libre
  o checkboxes: proyector, aire acondicionado, PCs — guardarlo como JSON
  en la columna `caracteristicas`), estado (`DISPONIBLE`,
  `MANTENIMIENTO`, `INACTIVO`).

### Académico → Docentes
- Tabla: Código, Nombre completo, Correo, Estado, Acciones.
- Formulario: código, nombre, apellido, correo, estado, y DOS
  multi-select en la misma pantalla:
  - **Programas a los que pertenece** (tabla `docente_programa`)
  - **Asignaturas que puede impartir** (tabla `docente_asignatura`) — esto
    define la compatibilidad docente-asignatura que después limita a
    quién se puede asignar una clase.

### Académico → Asignaturas
- Tabla: Código, Nombre, Créditos, Tipo, Estado, Acciones.
- Formulario: código, nombre, créditos (numérico), tipo (texto libre),
  estado, y multi-select de **Programas a los que pertenece** (tabla
  `asignatura_programa`).

### Académico → Periodos
- Tabla: Código (ej. "2026-2"), Nombre, Fecha inicio, Fecha fin, Estado,
  Acciones.
- Formulario: código, nombre, fecha inicio, fecha fin, estado
  (`PLANIFICACION`, `ACTIVO`, `CERRADO`, `HISTORICO`), y un selector
  opcional **"Reutilizar datos de otro periodo"** que, al elegir un
  periodo anterior, copia su `oferta_academica` y `asignacion_docente`
  hacia el nuevo `id_periodo` (usando el campo `id_periodo_base` de
  `periodo_academico` para dejar registrado de cuál se originó). El
  histórico del periodo anterior NUNCA se borra ni se mueve.

### Académico → Oferta académica
- Vista por periodo + programa: tabla de asignaturas ofertadas
  (`oferta_academica`), con botón para agregar una asignatura (de las
  que pertenecen a ese programa vía `asignatura_programa`) al periodo
  activo. Filtros: Periodo, Programa.

### Académico → Restricciones
- Tabla: Tipo, Docente/Espacio afectado, Día, Rango horario, Descripción,
  Estado.
- Formulario: tipo (`DOCENTE`, `ESPACIO`, `HORARIO`, `OTRO`), select
  condicional de Docente o Espacio según el tipo elegido, día de la
  semana, hora inicio/fin, descripción libre, periodo (opcional — NULL
  = restricción general).

### Horarios → General
- Tabla de solo lectura sobre `vista_horario_completo`: Periodo,
  Facultad, Programa, Asignatura, Docente, Sede, Bloque, Espacio, Día,
  Hora, Estado. Filtros por cada una de esas columnas. Esta pantalla NO
  crea/edita horarios todavía (eso va en un módulo aparte más adelante
  para el rol Líder) — es consulta para el Administrador.

### Horarios → Generar con FET / Propuestas FET
- Si ya existe la simulación de FET (genera filas en `propuesta_fet` /
  `propuesta_fet_detalle`), dejala tal cual. Si no existe todavía, dejá
  el tab con estado `.proximamente` — no lo inventes en esta pasada.

### Horarios → Auditoría
- Tabla sobre `vista_auditoria_pendiente`: Fecha, Docente, Asignatura,
  Programa, Espacio, Hora, Resultado, Observación. Filtros: Fecha, Sede,
  Bloque, Espacio, Docente, Programa (sección 29 y 58 del documento
  funcional). Si no está construida todavía, `.proximamente`.

### Reportes
- Pantalla simple con botones de exportación (aunque la exportación real
  a PDF/Excel no esté implementada todavía, dejá los botones con
  `// TODO: exportación real`): Horario por programa, Horario por
  docente, Horario por espacio, Resultados de auditoría.

### Importar/Exportar
- Pantalla de carga de Excel: input de archivo, select de tipo de
  entidad (`DOCENTE`, `ASIGNATURA`, `ESPACIO`, `PROGRAMA`), botón
  "Validar" que muestra una vista previa con errores por fila ANTES de
  confirmar la importación (nunca inserta directo). Si PhpSpreadsheet no
  está instalado todavía, dejá la pantalla con el flujo de UI armado y
  `// TODO: parseo real con PhpSpreadsheet`.

### Sistema → Personalización
- Formulario que edita la fila de `institucion`: nombre, nombre del
  sistema, lema, logo (subida de archivo o URL), favicon, color
  principal/secundario/acento (inputs tipo `color`). Al guardar, estos
  valores son los que se inyectan como `<style>` inline sobreescribiendo
  `design-tokens.css` (si esa inyección todavía no existe en el header
  compartido, agregala ahora).

### Sistema → Usuarios
- Tabla: Nombre, Correo, Rol, Estado, Acciones.
- Formulario: nombre, apellido, correo, contraseña (con
  `password_hash()`), rol (`ADMINISTRADOR` / `LIDER_PROGRAMA`), estado, y
  si el rol es Líder, multi-select de **Programas que administra** (tabla
  `programa_lider`).

## Reglas que siguen aplicando en todo lo que construyas

- `Auth::requerirRol(...)` como primera línea de cada método de
  controlador.
- 100% PDO preparado, 100% `htmlspecialchars()` en la salida.
- Nunca DELETE físico — siempre cambio de `estado`.
- Siempre las variables de `design-tokens.css`, nunca un color a mano.
- Reutilizá `.panel`, `.badge`, `.btn-primario`, tablas y el nuevo
  partial de tabs — no inventes componentes nuevos si ya existe uno
  equivalente.

## Formato de entrega

Trabajá **una sección completa a la vez** (por ejemplo, toda "Espacios"
con sus 3 tabs, antes de pasar a "Académico"), en este orden: Institución
→ Espacios → Académico → Horarios (General) → Sistema → Reportes →
Importar/Exportar. Al terminar cada sección decime: qué archivos tocaste,
qué ruta probar, y si algo que ya andaba dejó de funcionar en el proceso.
Si un campo o regla no está clara en este prompt ni en el schema,
preguntame antes de inventarla.