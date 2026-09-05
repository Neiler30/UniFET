<img width="733" height="271" alt="ChatGPT Image 5 sept 2026, 03_51_59 p m" src="https://github.com/user-attachments/assets/2b7f79f7-bd51-42eb-877c-6b47cf36a3d9" />

### Sistema Inteligente de Agenda y Gestión de Quehaceres

**Grupo VEAM — Institución Universitaria del Caribe (Unicaribe)**

---

## 📌 Descripción

**UniFET** es una plataforma web orientada a la gestión inteligente de agendas académicas, disponibilidad horaria, actividades especiales y quehaceres del **Grupo VEAM de la Institución Universitaria del Caribe (Unicaribe)**.

El sistema nace para resolver una necesidad concreta: **encontrar espacios de tiempo disponibles en común entre estudiantes y profesores para programar actividades académicas, talleres, tutorías, semilleros y eventos VEAM sin generar colisiones con los horarios académicos existentes.**

Para lograrlo, UniFET utiliza una arquitectura híbrida en la que la información de disponibilidad puede provenir inicialmente de formularios y archivos, y posteriormente integrarse con los sistemas institucionales de información.

El cálculo de los espacios disponibles se apoya en **FET-CL (Free Timetabling Software)**, utilizado como motor heurístico de búsqueda de horarios factibles.

> **UniFET no pretende reemplazar el sistema oficial de horarios de la universidad.**
>
> Su función es utilizar los horarios académicos existentes como restricciones para encontrar espacios comunes donde puedan desarrollarse actividades adicionales del Grupo VEAM.

---

# 🎯 Problema

La programación de actividades adicionales dentro de un entorno universitario puede requerir cruzar simultáneamente:

* Horarios de múltiples estudiantes.
* Horarios de profesores.
* Grupos académicos.
* Asignaturas.
* Actividades prácticas.
* Tutorías.
* Talleres.
* Semilleros.
* Eventos especiales.
* Restricciones de disponibilidad.

Realizar este cruce manualmente puede resultar complejo y propenso a errores.

UniFET propone centralizar esta información y utilizar un motor de búsqueda heurística para identificar automáticamente espacios donde los participantes requeridos estén disponibles.

---

# 💡 Objetivo general

Desarrollar una plataforma web que permita **gestionar, centralizar y analizar la disponibilidad académica de estudiantes y profesores**, utilizando FET-CL como motor de búsqueda de espacios horarios comunes y vinculando posteriormente dichos espacios con actividades y tareas académicas.

---

# 🎯 Objetivos específicos

* Centralizar información de disponibilidad académica.
* Permitir el registro manual de horarios ocupados.
* Permitir la importación de información mediante archivos.
* Diseñar una arquitectura preparada para futuras integraciones con AcademuSoft.
* Transformar la información almacenada en restricciones compatibles con FET-CL.
* Ejecutar FET-CL para encontrar espacios horarios factibles.
* Almacenar los resultados generados por el motor.
* Visualizar horarios y actividades según el rol del usuario.
* Gestionar tareas, quehaceres y fechas de entrega.
* Relacionar actividades VEAM con estudiantes, profesores, grupos y asignaturas.
* Mantener una arquitectura modular y escalable.

---

# 🧠 Concepto central

La lógica principal de UniFET puede resumirse de la siguiente manera:

```text
HORARIOS ACADÉMICOS EXISTENTES
            │
            ▼
   ┌───────────────────┐
   │  UniFET / PHP     │
   │ Normalización     │
   └─────────┬─────────┘
             │
             ▼
     RESTRICCIONES FET
             │
             ▼
        ┌─────────┐
        │ FET-CL  │
        └────┬────┘
             │
             ▼
    ESPACIOS DISPONIBLES
             │
             ▼
     ACTIVIDAD VEAM
             │
             ▼
      TAREAS / QUEHACERES
```

El sistema utiliza una lógica de **"horario inverso"**:

> En lugar de preguntarle a FET "¿cómo puedo construir el horario?", UniFET le proporciona las ocupaciones existentes y le pregunta indirectamente "¿en qué espacios puedo colocar esta nueva actividad sin generar conflictos?".

---

# ⚙️ Arquitectura general

UniFET está diseñado bajo una arquitectura modular y desacoplada.

```text
                         ┌─────────────────────┐
                         │      USUARIOS       │
                         │                     │
                         │ Estudiantes         │
                         │ Profesores          │
                         │ Administradores     │
                         └──────────┬──────────┘
                                    │
                                    ▼
                         ┌─────────────────────┐
                         │     INTERFAZ WEB    │
                         │      HTML/CSS       │
                         └──────────┬──────────┘
                                    │
                                    ▼
                         ┌─────────────────────┐
                         │     BACKEND PHP     │
                         │                     │
                         │ Lógica de negocio   │
                         │ Autenticación       │
                         │ Importaciones       │
                         │ Generación FET      │
                         │ Procesamiento XML   │
                         └──────────┬──────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    │                               │
                    ▼                               ▼
          ┌───────────────────┐           ┌─────────────────┐
          │   BASE DE DATOS   │           │     FET-CL      │
          │                   │           │                 │
          │ MySQL/PostgreSQL  │           │ Motor heurístico│
          └───────────────────┘           └────────┬────────┘
                                                   │
                                                   ▼
                                           RESULTADO XML
                                                   │
                                                   ▼
                                          ┌────────────────┐
                                          │ PHP procesa    │
                                          │ resultado      │
                                          └───────┬────────┘
                                                  │
                                                  ▼
                                           BASE DE DATOS
                                                  │
                                                  ▼
                                      ┌────────────────────┐
                                      │ Agenda / Eventos   │
                                      │ Tareas / Alertas   │
                                      └────────────────────┘
```

---

# 🔄 Arquitectura híbrida de ingesta

Uno de los principios fundamentales de UniFET es que **la fuente de los datos no debe estar acoplada al funcionamiento del sistema**.

La información puede ingresar mediante diferentes mecanismos.

```text
                  ┌─────────────────┐
                  │     MANUAL      │
                  │ Formulario Web  │
                  └────────┬────────┘
                           │
                           │
                  ┌────────▼────────┐
                  │     ARCHIVO      │
                  │ CSV / XML / etc. │
                  └────────┬────────┘
                           │
                           │
                  ┌────────▼─────────┐
                  │    AcademuSoft   │
                  │ API / SQL        │
                  │     FUTURO       │
                  └────────┬─────────┘
                           │
                           ▼
                 ┌────────────────────┐
                 │ CONTROLADOR DE     │
                 │ INGESTA UNIFICADO  │
                 └─────────┬──────────┘
                           │
                           ▼
                 ┌────────────────────┐
                 │     BASE DE DATOS  │
                 │                    │
                 │  horarios_base     │
                 └─────────┬──────────┘
                           │
                           ▼
                       MOTOR FET
```

Esto permite que el sistema pueda comenzar funcionando sin depender de permisos de integración con los sistemas institucionales.

Posteriormente, cuando exista acceso autorizado a AcademuSoft, se podrá desarrollar un nuevo módulo de importación sin modificar el núcleo del sistema.

---

# 🧩 Motor FET

UniFET integra **FET-CL** como componente especializado para el cálculo de horarios.

FET recibe un archivo `.fet` generado dinámicamente por PHP.

```text
Base de datos
     │
     ▼
PHP consulta ocupaciones
     │
     ▼
Generación de input.fet
     │
     ▼
FET-CL
     │
     ▼
Cálculo heurístico
     │
     ▼
output.fet
     │
     ▼
PHP
     │
     ▼
Base de datos
```

### Entrada

PHP transforma las ocupaciones existentes en restricciones de tiempo.

Por ejemplo:

```text
Estudiante A
Lunes 08:00 - 10:00 → OCUPADO

Estudiante B
Lunes 09:00 - 11:00 → OCUPADO

Profesor X
Lunes 08:00 - 12:00 → OCUPADO
```

El motor analiza estas restricciones para encontrar posibles espacios comunes.

### Salida

El resultado de FET se procesa nuevamente mediante PHP para convertirlo en información utilizable por UniFET.

---

# 📅 Flujo completo del sistema

```text
┌────────────────────┐
│ 1. CAPTURA DATOS   │
│                    │
│ Manual / Archivo / │
│ AcademuSoft        │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 2. NORMALIZACIÓN   │
│                    │
│ PHP + BD           │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 3. GENERACIÓN FET  │
│                    │
│ input.fet          │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 4. EJECUCIÓN       │
│                    │
│ FET-CL              │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 5. RESULTADO       │
│                    │
│ output.fet         │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 6. PARSEO PHP      │
│                    │
│ Lectura XML        │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 7. BASE DE DATOS   │
│                    │
│ Eventos / Horarios │
│ / Asignaciones     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 8. AGENDA VEAM     │
│                    │
│ Actividades        │
│ Talleres           │
│ Tutorías           │
│ Semilleros         │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 9. QUEHACERES      │
│                    │
│ Tareas             │
│ Entregas           │
│ Alertas            │
└────────────────────┘
```

---

# 👥 Roles del sistema

UniFET contempla inicialmente tres roles principales.

## 👨‍💼 Administrador / Coordinador

Responsable de la administración general del sistema.

Funciones previstas:

* Gestionar usuarios.
* Gestionar grupos.
* Gestionar asignaturas.
* Administrar agendas.
* Importar información.
* Configurar restricciones.
* Ejecutar el motor FET.
* Consultar resultados.
* Gestionar actividades VEAM.
* Supervisar tareas.
* Consultar registros y logs.

---

## 🎓 Estudiante

Orientado al seguimiento personal.

Funciones previstas:

* Consultar horario.
* Consultar actividades VEAM.
* Consultar tareas.
* Consultar fechas de entrega.
* Marcar tareas como realizadas.
* Consultar alertas.
* Consultar actividades asignadas.

---

## 👨‍🏫 Profesor

Orientado a la gestión académica y seguimiento de grupos.

Funciones previstas:

* Consultar horario.
* Consultar grupos.
* Consultar estudiantes.
* Crear actividades.
* Asignar tareas.
* Registrar seguimiento.
* Registrar calificaciones cuando corresponda.
* Consultar bitácoras.

---

# 🗄️ Modelo conceptual de datos

La base de datos será relacional.

Una representación inicial:

```text
                         ┌──────────────┐
                         │   USUARIOS   │
                         └──────┬───────┘
                                │
                  ┌─────────────┴─────────────┐
                  │                           │
                  ▼                           ▼
           ┌─────────────┐             ┌─────────────┐
           │ ESTUDIANTES │             │ PROFESORES  │
           └──────┬──────┘             └──────┬──────┘
                  │                           │
                  │                           │
                  └──────────┬────────────────┘
                             │
                             ▼
                      ┌─────────────┐
                      │   GRUPOS    │
                      └──────┬──────┘
                             │
                             ▼
                      ┌─────────────┐
                      │ ASIGNATURAS │
                      └─────────────┘

        ┌──────────────────────┐
        │   HORARIOS_BASE      │
        │                      │
        │ manual               │
        │ archivo              │
        │ academusoft          │
        └───────────┬──────────┘
                    │
                    ▼
                MOTOR FET
                    │
                    ▼
        ┌──────────────────────┐
        │   EVENTOS VEAM       │
        └───────────┬──────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │    ASIGNACIONES      │
        └───────────┬──────────┘
                    │
                    ▼
              ┌───────────┐
              │  TAREAS   │
              └─────┬─────┘
                    │
                    ▼
          ┌──────────────────┐
          │ TAREAS_ESTUDIANTES│
          └──────────────────┘
```

El esquema definitivo será documentado en `database/`.

---

# 🛠️ Stack tecnológico

| Componente                 | Tecnología                |
| -------------------------- | ------------------------- |
| Backend                    | PHP                       |
| Base de datos              | MySQL / PostgreSQL        |
| Frontend                   | HTML5 + CSS3 + JavaScript |
| Motor de horarios          | FET-CL                    |
| Formato de intercambio FET | XML                       |
| Acceso a BD                | PDO                       |
| Control de versiones       | Git + GitHub              |
| Integración futura         | AcademuSoft API / SQL     |
| Arquitectura               | Modular / desacoplada     |

---

# 📁 Estructura prevista del repositorio

```text
unifet/
│
├── README.md
├── LICENSE
├── .gitignore
│
├── docs/
│   ├── 01-planteamiento/
│   ├── 02-requerimientos/
│   ├── 03-arquitectura/
│   ├── 04-base-de-datos/
│   ├── 05-motor-fet/
│   ├── 06-api/
│   └── 07-manuales/
│
├── database/
│   ├── schema.sql
│   ├── seeds.sql
│   └── migrations/
│
├── src/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── repositories/
│   ├── services/
│   └── modules/
│
├── fet/
│   ├── templates/
│   ├── input/
│   ├── output/
│   └── scripts/
│
├── public/
│
├── tests/
│
└── storage/
    ├── logs/
    └── imports/
```

---

# 🚧 Estado actual del proyecto

**Estado:** 🟡 En desarrollo inicial

### Fase 1 — Análisis y diseño

* [x] Definición del problema.
* [x] Definición del objetivo.
* [x] Definición de la arquitectura híbrida.
* [x] Definición del papel de FET-CL.
* [x] Definición inicial de roles.
* [ ] Modelo relacional definitivo.
* [ ] Diccionario de datos.
* [ ] Requerimientos funcionales.
* [ ] Requerimientos no funcionales.

### Fase 2 — Base de datos

* [ ] `CREATE TABLE`.
* [ ] Relaciones.
* [ ] Índices.
* [ ] Datos iniciales.
* [ ] Migraciones.

### Fase 3 — Backend

* [ ] Configuración PHP.
* [ ] Conexión PDO.
* [ ] Autenticación.
* [ ] Gestión de usuarios.
* [ ] Gestión académica.
* [ ] Controlador de ingesta.

### Fase 4 — Motor FET

* [ ] Generador `.fet`.
* [ ] Integración con FET-CL.
* [ ] Ejecución desde PHP.
* [ ] Parser XML.
* [ ] Gestión de resultados.
* [ ] Logs de ejecución.

### Fase 5 — Agenda VEAM

* [ ] Eventos.
* [ ] Actividades.
* [ ] Asignaciones.
* [ ] Calendario.
* [ ] Visualización por usuario.

### Fase 6 — Quehaceres

* [ ] Creación de tareas.
* [ ] Asignación.
* [ ] Estados.
* [ ] Fechas de entrega.
* [ ] Alertas.
* [ ] Seguimiento.

### Fase 7 — Integración futura

* [ ] Investigación de mecanismos de integración con AcademuSoft.
* [ ] Definición de API/vistas autorizadas.
* [ ] Módulo de importación.
* [ ] Sincronización.
* [ ] Auditoría de datos.

---

# 🔐 Principios de desarrollo

UniFET seguirá los siguientes principios:

### 1. Desacoplamiento

El motor FET no debe depender directamente de la interfaz web ni de la fuente de datos.

### 2. Fuente de datos intercambiable

La información puede proceder de:

```text
MANUAL
   ↓
ARCHIVO
   ↓
ACADEMUSOFT
```

sin cambiar el núcleo del sistema.

### 3. Integridad de datos

Las relaciones entre usuarios, estudiantes, profesores, grupos, asignaturas, horarios, eventos y tareas deberán mantenerse mediante claves primarias y foráneas.

### 4. Seguridad

Las operaciones sobre la base de datos deberán utilizar consultas parametrizadas mediante PDO.

Las credenciales y configuraciones sensibles no deben almacenarse directamente en el repositorio.

### 5. Modularidad

Cada componente debe poder evolucionar independientemente.

### 6. Trazabilidad

Las ejecuciones del motor FET, importaciones y operaciones importantes deberán generar registros que permitan identificar qué ocurrió y cuándo.

---

# 🧪 Estrategia de desarrollo

El proyecto será desarrollado incrementalmente.

```text
DISEÑO
  ↓
BASE DE DATOS
  ↓
BACKEND
  ↓
INGESTA
  ↓
GENERADOR FET
  ↓
FET-CL
  ↓
PARSER
  ↓
AGENDA
  ↓
TAREAS
  ↓
PRUEBAS
  ↓
DESPLIEGUE
```

Cada módulo deberá ser probado antes de integrarse con el siguiente.

---

# 📚 Documentación

La documentación técnica del proyecto estará organizada dentro de `docs/`.

Se recomienda mantener separados:

* Planteamiento del problema.
* Requerimientos.
* Casos de uso.
* Arquitectura.
* Modelo de datos.
* Diccionario de datos.
* Integración FET.
* API.
* Manual de instalación.
* Manual de usuario.
* Decisiones técnicas.

Las decisiones importantes de arquitectura deberán quedar documentadas para que cualquier integrante del grupo pueda comprender el razonamiento detrás de ellas.

---

# 🤝 Trabajo colaborativo

UniFET es un proyecto desarrollado mediante control de versiones con Git y GitHub.

Se recomienda utilizar ramas para nuevas funcionalidades:

```text
main
 │
 ├── develop
 │
 ├── feature/database
 ├── feature/auth
 ├── feature/fet-engine
 ├── feature/tasks
 └── feature/calendar
```

Los cambios importantes deberán integrarse mediante Pull Requests y revisarse antes de incorporarse a la rama principal.

---

# 🗺️ Roadmap

```text
                 UniFET
                    │
        ┌───────────┴───────────┐
        │                       │
   DOCUMENTACIÓN            ARQUITECTURA
        │                       │
        └───────────┬───────────┘
                    │
                    ▼
              BASE DE DATOS
                    │
                    ▼
                BACKEND PHP
                    │
                    ▼
             INGESTA DE DATOS
                    │
                    ▼
              GENERADOR FET
                    │
                    ▼
                FET-CL
                    │
                    ▼
            AGENDA INTELIGENTE
                    │
                    ▼
             GESTIÓN DE TAREAS
                    │
                    ▼
             PRUEBAS / CALIDAD
                    │
                    ▼
                DESPLIEGUE
                    │
                    ▼
        INTEGRACIÓN ACADEMUSOFT
```

---

# 📜 Licencia

La licencia definitiva del proyecto será definida por el equipo de trabajo de acuerdo con las políticas institucionales de Unicaribe y los componentes de software utilizados.

---

# 🏫 Contexto institucional

**UniFET** está concebido como una solución tecnológica para apoyar las actividades del **Grupo VEAM de la Institución Universitaria del Caribe (Unicaribe), Ciénaga, Magdalena, Colombia**.

El sistema se plantea inicialmente como un proyecto modular y experimental, con capacidad de evolucionar hacia una plataforma institucional siempre que se establezcan los mecanismos de autorización, seguridad, interoperabilidad y gobierno de datos correspondientes.

---

## 🚀 UniFET

> **Encontrar el espacio. Organizar la actividad. Gestionar el quehacer.**

**Grupo VEAM — Unicaribe**

*Proyecto en desarrollo.*
