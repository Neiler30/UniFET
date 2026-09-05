# <div align="center">uniFET</div>



<div align="center">
<img width="733" height="271" alt="ChatGPT Image 5 sept 2026, 03_51_59 p m" src="https://github.com/user-attachments/assets/98aab065-7bb4-49c6-9979-20d5b20ff25b" />


### Sistema Inteligente de Agenda y Gestión de Quehaceres

**Grupo VEAM — Institución Universitaria del Caribe**

*Ciénaga, Magdalena, Colombia*

</div>

---

## 📌 Sobre el proyecto

**uniFET** es una plataforma web orientada a la **gestión inteligente de agendas académicas, disponibilidad horaria, actividades especiales y quehaceres** del Grupo VEAM de la Institución Universitaria del Caribe.

El sistema nace para resolver una necesidad concreta:

> **Encontrar espacios de tiempo disponibles en común entre estudiantes y profesores para programar actividades académicas, talleres, tutorías, semilleros y eventos VEAM sin generar colisiones con los horarios académicos existentes.**

Para lograrlo, uniFET utiliza una arquitectura híbrida en la que la información de disponibilidad puede provenir inicialmente de formularios y archivos, y posteriormente integrarse con los sistemas institucionales de información.

El cálculo de los espacios disponibles se apoya en **FET-CL (Free Timetabling Software)**, utilizado como motor heurístico de búsqueda de horarios factibles.

> **uniFET no pretende reemplazar el sistema oficial de horarios de la universidad.**
>
> Su función es utilizar los horarios académicos existentes como restricciones para encontrar espacios comunes donde puedan desarrollarse actividades adicionales del Grupo VEAM.

---

# 🎨 Propuesta visual del sistema

Como parte de la etapa inicial de diseño, se desarrolló una propuesta visual de cómo podría funcionar y verse la plataforma web **uniFET**.

El objetivo de esta propuesta es establecer una referencia inicial para la interfaz del sistema y definir cómo podrían organizarse las funcionalidades dependiendo del rol del usuario.

La propuesta contempla tres vistas principales:

- 👨‍💼 Administrador / Coordinador
- 👨‍🏫 Docente / Profesor
- 🎓 Estudiante

La interfaz mantiene una estructura visual común, pero adapta las funcionalidades y la información mostrada de acuerdo con los permisos de cada rol.

## 🖥️ Wireframe — Vistas principales

<img width="1536" height="1024" alt="ChatGPT Image 5 sept 2026, 16_02_46" src="https://github.com/user-attachments/assets/0e576ccc-a8e7-46f9-829a-ecf45c1696f0" />

### 👨‍💼 Vista Administrador / Coordinador

La vista del administrador está orientada a la **gestión y supervisión general de la plataforma**.

Entre los elementos contemplados se encuentran:

- Dashboard general.
- Gestión de usuarios.
- Gestión de grupos.
- Gestión de agendas.
- Eventos VEAM.
- Gestión de tareas.
- Ejecuciones del motor FET.
- Reportes.
- Configuración.
- Supervisión de actividades.

El administrador tendrá una visión global de la información procesada por el sistema.

### 👨‍🏫 Vista Docente / Profesor

La vista del docente está orientada principalmente a la **gestión de sus actividades académicas y seguimiento de grupos**.

Se contemplan funcionalidades como:

- Mi panel.
- Mi horario.
- Mis grupos.
- Tareas asignadas.
- Registro de notas.
- Bitácora.
- Notificaciones.
- Consulta de actividades VEAM.

### 🎓 Vista Estudiante

La vista del estudiante está enfocada en la **consulta y seguimiento de sus actividades académicas y quehaceres**.

Se contemplan funcionalidades como:

- Mi panel.
- Mi horario.
- Mis tareas.
- Eventos VEAM.
- Calificaciones.
- Notificaciones.
- Fechas de entrega.
- Próximas actividades.

> **Nota:** El wireframe representa una propuesta inicial de diseño. La interfaz podrá modificarse durante el desarrollo del proyecto conforme se definan los requerimientos funcionales, técnicos y de experiencia de usuario.

---

# 🔄 Flujo de trabajo del sistema

Además de la propuesta visual, se plantea un flujo general de funcionamiento que representa cómo la información puede desplazarse desde las diferentes fuentes de datos hasta la generación de espacios disponibles y posteriormente hacia la agenda y gestión de tareas.

## 📊 Diagrama del flujo de trabajo

<img width="1519" height="476" alt="image" src="https://github.com/user-attachments/assets/b2665caa-bd40-486e-96cc-eb3db9382e86" />


El flujo conceptual contempla las siguientes etapas:

```text
FUENTES DE DATOS
      │
      ▼
INGESTA Y NORMALIZACIÓN
      │
      ▼
BASE DE DATOS
      │
      ▼
GENERACIÓN DE RESTRICCIONES
      │
      ▼
FET-CL
      │
      ▼
RESULTADOS
      │
      ▼
PARSEO Y PROCESAMIENTO
      │
      ▼
BASE DE DATOS
      │
      ▼
AGENDA VEAM
      │
      ▼
TAREAS / QUEHACERES


# 🔄 Descripción del flujo

El funcionamiento de **uniFET** se plantea como un proceso que inicia con la obtención de información sobre horarios y disponibilidad, continúa con su normalización y transformación en restricciones compatibles con FET-CL, y finaliza con la generación de espacios disponibles que pueden utilizarse para programar actividades y gestionar tareas.

El flujo general es:

```text
FUENTES DE DATOS
      │
      ▼
INGESTA Y NORMALIZACIÓN
      │
      ▼
ALMACENAMIENTO UNIFICADO
      │
      ▼
GENERACIÓN DE RESTRICCIONES FET
      │
      ▼
EJECUCIÓN DE FET-CL
      │
      ▼
PROCESAMIENTO DEL RESULTADO
      │
      ▼
GENERACIÓN DE ACTIVIDADES
      │
      ▼
GESTIÓN DE TAREAS Y QUEHACERES
```

## 1. Ingesta de información

La información puede ingresar al sistema mediante diferentes fuentes:

```text
┌───────────────────────┐
│        MANUAL         │
│    Formulario Web     │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│        ARCHIVOS       │
│     CSV / XML / etc.  │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│      ACADEMUSOFT      │
│       API / SQL       │
│        FUTURO         │
└───────────┬───────────┘
            │
            ▼
       CONTROLADOR
        DE INGESTA
```

El objetivo es que la fuente original de los datos no determine cómo funciona el resto del sistema.

---

# 🗄️ Almacenamiento unificado

Después de la ingesta, la información es normalizada y almacenada en una estructura común dentro de la base de datos.

Entre los datos que pueden ser gestionados se encuentran:

- Estudiantes.
- Profesores.
- Usuarios.
- Grupos.
- Asignaturas.
- Horarios.
- Ocupaciones.
- Eventos.
- Actividades.
- Asignaciones.
- Tareas.
- Seguimiento de tareas.

Una de las estructuras conceptuales principales será:

```text
horarios_base
```

Esta estructura permitirá centralizar las ocupaciones provenientes de diferentes fuentes.

La ventaja de este enfoque es que el motor FET no necesita conocer si una ocupación fue registrada manualmente, importada desde un archivo o posteriormente obtenida desde AcademuSoft.

```text
MANUAL ────────┐
               │
ARCHIVO ───────┼──► NORMALIZACIÓN ──► BASE DE DATOS
               │
ACADEMUSOFT ───┘
```

---

# ⚙️ Generación de restricciones FET

Una vez que la información se encuentra normalizada, el backend desarrollado en PHP consulta las ocupaciones existentes y las transforma en restricciones que puedan ser utilizadas por FET-CL.

El proceso conceptual es:

```text
BASE DE DATOS
      │
      ▼
CONSULTA DE OCUPACIONES
      │
      ▼
PROCESAMIENTO PHP
      │
      ▼
GENERACIÓN DE RESTRICCIONES
      │
      ▼
input.fet
```

Por ejemplo:

```text
Estudiante A
Lunes 08:00 - 10:00 → OCUPADO

Estudiante B
Lunes 09:00 - 11:00 → OCUPADO

Profesor X
Lunes 08:00 - 12:00 → OCUPADO
```

Estas ocupaciones son utilizadas como restricciones para evitar que una nueva actividad sea ubicada en un espacio donde exista una colisión.

El archivo generado será utilizado como entrada para FET-CL.

---

# 🧩 Ejecución de FET-CL

**FET-CL** funciona como el motor heurístico encargado de procesar las restricciones y buscar una solución de horario factible.

El flujo es:

```text
BASE DE DATOS
      │
      ▼
PHP
      │
      ▼
input.fet
      │
      ▼
┌─────────────┐
│   FET-CL    │
│             │
│ Motor       │
│ heurístico  │
└──────┬──────┘
       │
       ▼
output.fet
```

FET-CL analiza las restricciones suministradas y genera un resultado que posteriormente será procesado por uniFET.

El objetivo dentro de uniFET no es reemplazar la programación académica existente, sino utilizar la información existente para encontrar espacios donde puedan ubicarse nuevas actividades sin generar conflictos.

---

# 📤 Procesamiento del resultado

Una vez finalizada la ejecución de FET-CL, el archivo de salida es procesado nuevamente por PHP.

```text
output.fet
     │
     ▼
PHP
     │
     ▼
LECTURA Y PARSEO
     │
     ▼
ESPACIOS ENCONTRADOS
     │
     ▼
BASE DE DATOS
```

El resultado procesado puede contener información sobre los espacios horarios identificados como factibles.

Estos resultados pueden almacenarse en la base de datos para posteriormente ser consultados desde la interfaz web.

El procesamiento permite separar el funcionamiento del motor FET de la presentación de la información al usuario.

---

# 📅 Generación de actividades

Los espacios encontrados pueden utilizarse para planificar actividades del Grupo VEAM.

Entre las actividades contempladas se encuentran:

- Talleres.
- Tutorías.
- Semilleros.
- Actividades prácticas.
- Reuniones.
- Eventos académicos.
- Actividades especiales.

El proceso conceptual sería:

```text
ESPACIO DISPONIBLE
       │
       ▼
SELECCIÓN / ASIGNACIÓN
       │
       ▼
ACTIVIDAD VEAM
       │
       ├── Fecha
       ├── Hora
       ├── Lugar
       ├── Profesor responsable
       ├── Grupo
       └── Participantes
```

La actividad queda posteriormente disponible dentro de la agenda correspondiente.

---

# ✅ Gestión de tareas y quehaceres

Una actividad puede tener asociadas diferentes tareas o quehaceres.

Por ejemplo:

```text
TALLER ARDUINO
      │
      ├── Actividad programada
      │
      ├── Profesor responsable
      │
      ├── Grupo participante
      │
      └── Tareas
            │
            ├── Informe
            ├── Ejercicios
            └── Entrega final
```

Las tareas pueden incluir:

- Descripción.
- Responsable.
- Fecha de asignación.
- Fecha de entrega.
- Estado.
- Participantes.
- Seguimiento.
- Notificaciones.

De esta forma, uniFET no se limita a encontrar espacios horarios, sino que permite gestionar el ciclo posterior de las actividades.

---

# 🎯 Problema

La programación de actividades adicionales dentro de un entorno universitario puede requerir cruzar simultáneamente:

- Horarios de múltiples estudiantes.
- Horarios de profesores.
- Grupos académicos.
- Asignaturas.
- Actividades prácticas.
- Tutorías.
- Talleres.
- Semilleros.
- Eventos especiales.
- Restricciones de disponibilidad.

Realizar este cruce manualmente puede resultar complejo y propenso a errores.

Además, la información puede encontrarse distribuida entre diferentes fuentes, lo que dificulta mantener una visión unificada de la disponibilidad.

**uniFET** propone centralizar esta información y utilizar un motor de búsqueda heurística para identificar espacios donde los participantes requeridos estén disponibles.

---

# 💡 Objetivo general

Desarrollar una plataforma web que permita **gestionar, centralizar y analizar la disponibilidad académica de estudiantes y profesores**, utilizando FET-CL como motor de búsqueda de espacios horarios comunes y vinculando posteriormente dichos espacios con actividades y tareas académicas.

---

# 🎯 Objetivos específicos

- Centralizar información de disponibilidad académica.
- Permitir el registro manual de horarios ocupados.
- Permitir la importación de información mediante archivos.
- Diseñar una arquitectura preparada para futuras integraciones con AcademuSoft.
- Transformar la información almacenada en restricciones compatibles con FET-CL.
- Ejecutar FET-CL para encontrar espacios horarios factibles.
- Procesar y almacenar los resultados generados por el motor.
- Visualizar horarios y actividades según el rol del usuario.
- Gestionar tareas, quehaceres y fechas de entrega.
- Relacionar actividades VEAM con estudiantes, profesores, grupos y asignaturas.
- Mantener una arquitectura modular y escalable.

---

# 🧠 Concepto central

La lógica principal de uniFET puede resumirse de la siguiente manera:

```text
HORARIOS ACADÉMICOS EXISTENTES
            │
            ▼
   ┌───────────────────┐
   │      uniFET       │
   │       PHP         │
   │   Normalización   │
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

> En lugar de preguntarle a FET "¿cómo puedo construir el horario?", uniFET le proporciona las ocupaciones existentes y busca espacios donde pueda colocarse una nueva actividad sin generar conflictos.

Este concepto permite que la plataforma se enfoque en el problema específico de encontrar espacios disponibles dentro de una agenda académica ya existente.

---

# ⚙️ Arquitectura general

uniFET está diseñado bajo una arquitectura modular y desacoplada.

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

Los principales componentes son:

### Interfaz web

Permite que los usuarios interactúen con la plataforma según su rol.

### Backend PHP

Contiene la lógica de negocio, autenticación, procesamiento de información, generación de archivos FET y lectura de resultados.

### Base de datos

Centraliza la información utilizada por el sistema.

### FET-CL

Actúa como motor especializado para el procesamiento de las restricciones horarias.

---

# 🔄 Arquitectura híbrida de ingesta

Uno de los principios fundamentales de uniFET es que **la fuente de los datos no debe estar acoplada al funcionamiento del sistema**.

La información puede ingresar mediante diferentes mecanismos:

```text
                  ┌─────────────────┐
                  │     MANUAL      │
                  │ Formulario Web  │
                  └────────┬────────┘
                           │
                  ┌────────▼────────┐
                  │     ARCHIVO     │
                  │  CSV / XML / etc│
                  └────────┬────────┘
                           │
                  ┌────────▼─────────┐
                  │    AcademuSoft   │
                  │    API / SQL     │
                  │      FUTURO      │
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
                 │    BASE DE DATOS   │
                 │                    │
                 │   horarios_base    │
                 └─────────┬──────────┘
                           │
                           ▼
                       MOTOR FET
```

Esto permite que el sistema pueda comenzar funcionando sin depender de permisos de integración con los sistemas institucionales.

Posteriormente, cuando exista acceso autorizado a AcademuSoft, se podrá desarrollar un nuevo módulo de importación sin modificar el núcleo del sistema.

---

# 🧩 Motor FET

uniFET integra **FET-CL** como componente especializado para el cálculo de horarios.

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

El resultado de FET se procesa nuevamente mediante PHP para convertirlo en información utilizable por uniFET.

---

# 👥 Roles del sistema

uniFET contempla inicialmente tres roles principales.

## 👨‍💼 Administrador / Coordinador

Responsable de la administración general del sistema.

Funciones previstas:

- Gestionar usuarios.
- Gestionar grupos.
- Gestionar asignaturas.
- Administrar agendas.
- Importar información.
- Configurar restricciones.
- Ejecutar el motor FET.
- Consultar resultados.
- Gestionar actividades VEAM.
- Supervisar tareas.
- Consultar registros y logs.

---

## 👨‍🏫 Docente / Profesor

Orientado a la gestión académica y seguimiento de grupos.

Funciones previstas:

- Consultar horario.
- Consultar grupos.
- Consultar estudiantes.
- Crear actividades.
- Asignar tareas.
- Registrar seguimiento.
- Registrar calificaciones cuando corresponda.
- Consultar bitácoras.

---

## 🎓 Estudiante

Orientado al seguimiento personal.

Funciones previstas:

- Consultar horario.
- Consultar actividades VEAM.
- Consultar tareas.
- Consultar fechas de entrega.
- Marcar tareas como realizadas.
- Consultar alertas.
- Consultar actividades asignadas.

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
          ┌───────────────────┐
          │ TAREAS_ESTUDIANTES│
          └───────────────────┘
```

El esquema definitivo será documentado en `database/`.

---

# 🛠️ Stack tecnológico

| Componente | Tecnología |
|---|---|
| Backend | PHP |
| Base de datos | MySQL / PostgreSQL |
| Frontend | HTML5 + CSS3 + JavaScript |
| Motor de horarios | FET-CL |
| Formato de intercambio FET | XML |
| Acceso a BD | PDO |
| Control de versiones | Git + GitHub |
| Integración futura | AcademuSoft API / SQL |
| Arquitectura | Modular / desacoplada |

---

# 🚧 Estado actual del proyecto

**Estado:** 🟡 En desarrollo inicial

## Fase 1 — Análisis y diseño

- [x] Definición del problema.
- [x] Definición del objetivo.
- [x] Definición de la arquitectura híbrida.
- [x] Definición del papel de FET-CL.
- [x] Definición inicial de roles.
- [x] Propuesta inicial de interfaz.
- [x] Propuesta inicial del flujo del sistema.
- [ ] Modelo relacional definitivo.
- [ ] Diccionario de datos.
- [ ] Requerimientos funcionales.
- [ ] Requerimientos no funcionales.

## Fase 2 — Base de datos

- [ ] `CREATE TABLE`.
- [ ] Relaciones.
- [ ] Índices.
- [ ] Datos iniciales.
- [ ] Migraciones.

## Fase 3 — Backend

- [ ] Configuración PHP.
- [ ] Conexión PDO.
- [ ] Autenticación.
- [ ] Gestión de usuarios.
- [ ] Gestión académica.
- [ ] Controlador de ingesta.

## Fase 4 — Motor FET

- [ ] Generador `.fet`.
- [ ] Integración con FET-CL.
- [ ] Ejecución desde PHP.
- [ ] Parser XML.
- [ ] Gestión de resultados.
- [ ] Logs de ejecución.

## Fase 5 — Agenda VEAM

- [ ] Eventos.
- [ ] Actividades.
- [ ] Asignaciones.
- [ ] Calendario.
- [ ] Visualización por usuario.

## Fase 6 — Quehaceres

- [ ] Creación de tareas.
- [ ] Asignación.
- [ ] Estados.
- [ ] Fechas de entrega.
- [ ] Alertas.
- [ ] Seguimiento.

## Fase 7 — Integración futura

- [ ] Investigación de mecanismos de integración con AcademuSoft.
- [ ] Definición de API/vistas autorizadas.
- [ ] Módulo de importación.
- [ ] Sincronización.
- [ ] Auditoría de datos.

---

# 🔐 Principios de desarrollo

## 1. Desacoplamiento

El motor FET no debe depender directamente de la interfaz web ni de la fuente de datos.

## 2. Fuente de datos intercambiable

La información puede proceder de:

```text
MANUAL
   ↓
ARCHIVO
   ↓
ACADEMUSOFT
```

sin cambiar el núcleo del sistema.

## 3. Integridad de datos

Las relaciones entre usuarios, estudiantes, profesores, grupos, asignaturas, horarios, eventos y tareas deberán mantenerse mediante claves primarias y foráneas.

## 4. Seguridad

Las operaciones sobre la base de datos deberán utilizar consultas parametrizadas mediante PDO.

Las credenciales y configuraciones sensibles no deben almacenarse directamente en el repositorio.

## 5. Modularidad

Cada componente debe poder evolucionar independientemente.

## 6. Trazabilidad

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

- Planteamiento del problema.
- Requerimientos.
- Casos de uso.
- Arquitectura.
- Modelo de datos.
- Diccionario de datos.
- Integración FET.
- API.
- Manual de instalación.
- Manual de usuario.
- Decisiones técnicas.

Las decisiones importantes de arquitectura deberán quedar documentadas para que cualquier integrante del grupo pueda comprender el razonamiento detrás de ellas.

---

# 🤝 Trabajo colaborativo

uniFET es un proyecto desarrollado mediante control de versiones con Git y GitHub.

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
                    uniFET
                      │
          ┌───────────┴───────────┐
          │                       │
     DOCUMENTACIÓN           ARQUITECTURA
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

# 🏫 Contexto institucional

**uniFET** está concebido como una solución tecnológica para apoyar las actividades del **Grupo VEAM de la Institución Universitaria del Caribe, Ciénaga, Magdalena, Colombia**.

El sistema se plantea inicialmente como un proyecto modular y experimental, con capacidad de evolucionar hacia una plataforma institucional siempre que se establezcan los mecanismos de autorización, seguridad, interoperabilidad y gobierno de datos correspondientes.

---

# 📜 Licencia

La licencia definitiva del proyecto será definida por el equipo de trabajo de acuerdo con las políticas institucionales y los componentes de software utilizados.

---

# 🚀 uniFET

<div align="center">

### **Encontrar el espacio. Organizar la actividad. Gestionar el quehacer.**

**Grupo VEAM — Institución Universitaria del Caribe**

*Proyecto en desarrollo.*

</div>
