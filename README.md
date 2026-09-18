<div align="center">
<img width="790" height="316" alt="649730870-d33908e8-78b5-4b38-b576-fd7946e8f9c7-removebg-preview" src="https://github.com/user-attachments/assets/f5d25c64-0533-4dc3-a7ad-320fb41a1beb"/>


## Sistema de Planificación, Gestión y Auditoría de Horarios Académicos

> **Proyecto académico --- VEAN / Unicaribe**\
> **Visión Empresarial y Aceleración de Negocios**
</div>
------------------------------------------------------------------------

## 1. Descripción del proyecto

**UniFET** es una plataforma web para la planificación, generación,
gestión, control y auditoría de horarios académicos.

El sistema centraliza la información institucional necesaria para
construir horarios completos, incluyendo:

-   Facultades
-   Programas académicos
-   Sedes
-   Bloques
-   Espacios y salones
-   Docentes
-   Asignaturas
-   Créditos académicos
-   Periodos académicos
-   Asignaciones docentes
-   Restricciones de espacio, lugar y tiempo
-   Horarios

UniFET permite que el **Docente Líder del Programa** construya el
horario de su programa de forma manual o solicite a **FET** una
propuesta automática.

FET actúa como motor de generación y optimización. Una propuesta
generada por FET **no se guarda automáticamente**: el líder debe
revisarla y decidir si la acepta, la modifica o la descarta.

Una vez que el líder confirma y cierra el horario de su programa, este
queda bloqueado para él. El **Administrador** mantiene la capacidad de
modificar horarios posteriormente cuando sea necesario.

Además, UniFET incorpora un módulo de **auditoría de clases**, mediante
el cual el líder puede consultar qué clases deberían estar
desarrollándose en una fecha, hora, sede, bloque o espacio determinado y
verificar presencialmente si la clase se está realizando.

El sistema se diseña desde el inicio para ser **personalizable y
reutilizable por diferentes instituciones educativas**, aunque la
primera implementación estará orientada a **Unicaribe**.

------------------------------------------------------------------------

# 2. Problema

La planificación de horarios académicos requiere coordinar
simultáneamente múltiples recursos y restricciones:

-   Docentes
-   Asignaturas
-   Programas
-   Facultades
-   Sedes
-   Bloques
-   Salones y laboratorios
-   Días y horas
-   Disponibilidad de docentes
-   Disponibilidad de espacios
-   Créditos e intensidad académica
-   Compatibilidad entre docentes y asignaturas
-   Distribución de clases

Cuando esta información se gestiona manualmente, aumentan las
posibilidades de:

-   Cruces de horarios
-   Asignación incorrecta de espacios
-   Duplicidad de docentes
-   Conflictos de disponibilidad
-   Uso ineficiente de salones
-   Dificultad para actualizar horarios
-   Falta de trazabilidad
-   Dificultad para verificar que las clases programadas realmente se
    estén realizando

UniFET busca centralizar este proceso y proporcionar una herramienta que
permita construir horarios, optimizarlos mediante FET y posteriormente
controlar su ejecución.

------------------------------------------------------------------------

# 3. Objetivo general

Desarrollar una plataforma web configurable para la planificación,
generación, gestión y auditoría de horarios académicos, centralizando la
información institucional y utilizando FET como motor de generación
automática de propuestas de horarios sujetas a restricciones académicas,
espaciales y temporales.

------------------------------------------------------------------------

# 4. Objetivos específicos

1.  Centralizar la información institucional necesaria para la
    planificación académica.

2.  Gestionar facultades, programas, sedes, bloques y espacios
    académicos.

3.  Registrar y administrar docentes asociados a uno o varios programas
    académicos.

4.  Registrar asignaturas, créditos y relaciones con los programas
    correspondientes.

5.  Permitir la asignación de docentes a las asignaturas que pueden
    impartir.

6.  Permitir la creación y administración de periodos académicos
    reutilizando información previamente registrada.

7.  Permitir la construcción manual de horarios por parte del Docente
    Líder del Programa.

8.  Integrar FET como motor de generación automática de propuestas de
    horarios.

9.  Permitir que las propuestas de FET sean revisadas, modificadas o
    descartadas antes de ser guardadas.

10. Evitar conflictos en la asignación de docentes, espacios, días y
    horas.

11. Controlar la ocupación de los espacios académicos por periodo, fecha
    y rango horario.

12. Impedir que diferentes programas reserven simultáneamente un mismo
    espacio en el mismo periodo, día y horario.

13. Permitir el cierre de horarios por parte del líder, bloqueando
    posteriores modificaciones por su parte.

14. Mantener privilegios administrativos para modificar horarios
    cerrados.

15. Registrar cambios administrativos relevantes para garantizar
    trazabilidad.

16. Implementar un sistema de auditoría que permita verificar la
    ejecución de las clases programadas.

17. Permitir filtros por docente, asignatura, programa, facultad, sede,
    bloque, espacio, periodo, fecha y horario.

18. Permitir la carga de información mediante formularios y archivos
    Excel.

19. Validar los datos importados antes de almacenarlos definitivamente.

20. Diseñar una arquitectura configurable que permita adaptar el sistema
    a diferentes instituciones educativas.

21. Permitir personalizar la identidad visual de cada institución
    mediante logo, lema y colores.

------------------------------------------------------------------------

# 5. Alcance

## 5.1 Incluido

El sistema contempla:

-   Administración institucional
-   Gestión académica
-   Gestión de docentes
-   Gestión de asignaturas
-   Gestión de créditos
-   Gestión de programas
-   Gestión de facultades
-   Gestión de sedes
-   Gestión de bloques
-   Gestión de espacios
-   Gestión de periodos académicos
-   Oferta académica
-   Asignación docente
-   Restricciones
-   Construcción manual de horarios
-   Generación automática mediante FET
-   Revisión de propuestas
-   Validación de conflictos
-   Control de disponibilidad de espacios
-   Cierre de horarios
-   Modificación administrativa
-   Auditoría de clases
-   Reportes
-   Filtros
-   Importación desde Excel
-   Exportación
-   Personalización institucional

## 5.2 Fuera del alcance inicial

No forman parte del alcance inicial, salvo que sean solicitados
posteriormente:

-   Portal para estudiantes
-   Registro de notas
-   Matrícula financiera
-   Gestión de pagos
-   Gestión completa de asistencia estudiantil
-   Nómina docente
-   Gestión de biblioteca
-   Gestión de bienestar universitario
-   Gestión de tareas académicas
-   LMS completo
-   Aplicación móvil nativa

------------------------------------------------------------------------

# 6. Actores del sistema
<div align="center">
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/c8ade79a-ae7d-4876-b581-8d1ee4aa166a" />
</div>
UniFET tendrá únicamente **dos roles de usuario final**.

## 6.1 Administrador

Tiene acceso total al sistema.

Responsabilidades principales:

-   Configurar la institución
-   Administrar la identidad visual
-   Gestionar facultades
-   Gestionar programas
-   Gestionar sedes
-   Gestionar bloques
-   Gestionar espacios
-   Registrar docentes
-   Registrar asignaturas
-   Configurar créditos
-   Gestionar periodos
-   Importar información
-   Administrar asignaciones
-   Gestionar restricciones
-   Revisar horarios
-   Modificar horarios cerrados
-   Consultar auditorías
-   Generar reportes
-   Administrar usuarios

## 6.2 Docente Líder del Programa

Es el responsable de construir y gestionar el horario de un programa
determinado.

No es un docente común como usuario del sistema.

El docente común es un **registro académico** que puede ser asignado a
una clase. El líder sí posee una cuenta de acceso.

El líder puede:

-   Consultar la información de su programa
-   Consultar docentes asociados
-   Consultar asignaturas asociadas
-   Construir horarios
-   Asignar docentes
-   Seleccionar espacios disponibles
-   Definir días y horas
-   Consultar restricciones
-   Solicitar propuestas a FET
-   Revisar propuestas
-   Modificar propuestas antes de confirmarlas
-   Guardar el horario
-   Cerrar el horario
-   Realizar auditorías de clases de su ámbito

El líder **no puede modificar un horario después de cerrarlo**.

------------------------------------------------------------------------

## 7. Modelo de permisos

| Funcionalidad                | Administrador | Líder de Programa        |
|-------------------------------|:--------------:|---------------------------|
| Configuración institucional   | Sí             | No                        |
| Personalización visual        | Sí             | No                        |
| Facultades                    | Sí             | Consulta                  |
| Programas                     | Sí             | Su programa               |
| Sedes                          | Sí             | Consulta                  |
| Bloques                        | Sí             | Consulta                  |
| Espacios                       | Sí             | Consulta                  |
| Docentes                       | Sí             | Consulta                  |
| Asignaturas                    | Sí             | Consulta                  |
| Créditos                       | Sí             | Consulta                  |
| Periodos                       | Sí             | Consulta                  |
| Asignación docente             | Sí             | Sí, dentro de su ámbito   |
| Restricciones                  | Sí             | Consulta / uso            |
| Crear horario manual           | Sí             | Sí                        |
| Generar con FET                | Sí             | Sí                        |
| Modificar propuesta FET        | Sí             | Sí                        |
| Cerrar horario                 | Sí             | Sí                        |
| Modificar horario cerrado      | Sí             | No                        |
| Auditoría                      | Sí             | Sí, dentro de su ámbito   |
| Reportes                       | Sí             | Según permisos            |
| Importar Excel                 | Sí             | No                        |
| Usuarios                       | Sí             | No                        |

# 8. Conceptos fundamentales del dominio
<div align="center">
      <img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/ab03b52e-a923-445f-aafb-2e9a80261ac0" />
</div>

## 8.1 Institución

Representa la organización educativa que utiliza UniFET.

Ejemplo inicial:

**Unicaribe**

La institución posee:

-   Identidad visual
-   Facultades
-   Programas
-   Sedes
-   Configuraciones académicas

La arquitectura debe permitir que posteriormente se configure otra
institución.

------------------------------------------------------------------------

## 8.2 Facultad

Agrupa programas académicos.

Ejemplo:

``` text
Facultad de Ingeniería
```

------------------------------------------------------------------------

## 8.3 Programa

Representa un programa académico.

Ejemplo:

``` text
Ingeniería de Sistemas
```

Un programa pertenece a una facultad.

------------------------------------------------------------------------

## 8.4 Sede

Representa una ubicación física de la institución.

Ejemplo:

``` text
Sede Ciénaga
```

------------------------------------------------------------------------

## 8.5 Bloque

Representa una división física dentro de una sede.

Ejemplo:

``` text
Bloque A
Bloque B
```

------------------------------------------------------------------------

## 8.6 Espacio

Representa un lugar físico donde puede desarrollarse una clase.

Puede ser:

-   Aula
-   Laboratorio
-   Sala
-   Auditorio
-   Otro espacio académico

Un espacio pertenece a un bloque y, por consecuencia, a una sede.

Información esperada:

-   Código
-   Nombre
-   Tipo
-   Capacidad
-   Sede
-   Bloque
-   Estado
-   Características

------------------------------------------------------------------------

## 8.7 Docente

Representa a una persona que puede impartir asignaturas.

Un docente:

-   Puede pertenecer a uno o varios programas.
-   Puede impartir una o varias asignaturas.
-   No necesariamente posee una cuenta en el sistema.
-   Puede ser asignado a diferentes clases.

------------------------------------------------------------------------

## 8.8 Asignatura

Representa una unidad académica.

Ejemplo:

``` text
Programación de Estructuras de Datos
```

Información esperada:

-   Código
-   Nombre
-   Créditos
-   Tipo
-   Estado
-   Programas asociados

Una asignatura puede estar asociada a varios programas.

Ejemplo:

``` text
Competencias en Segundo Idioma
    ├── Ingeniería de Sistemas
    ├── Administración
    └── Otro programa
```

------------------------------------------------------------------------

## 8.9 Crédito académico

Cada asignatura debe registrar la cantidad de créditos correspondiente.

Ejemplo:

``` text
Asignatura: Bases de Datos
Créditos: 3
```

La equivalencia entre créditos e intensidad horaria debe ser
configurable y deberá definirse de acuerdo con las reglas académicas de
la institución.

No se debe asumir una conversión universal entre créditos y horas.

------------------------------------------------------------------------

## 8.10 Periodo académico

Representa el periodo en el que se ejecuta una programación académica.

Ejemplos:

``` text
2026-1
2026-2
2027-1
```

Los periodos reutilizan información maestra existente.

Al crear un nuevo periodo, el sistema debe permitir:

-   Crear un periodo vacío
-   Precargar información existente
-   Reutilizar docentes
-   Reutilizar asignaturas
-   Reutilizar programas
-   Reutilizar espacios
-   Reutilizar configuraciones

Los datos históricos de periodos anteriores deben conservarse.

------------------------------------------------------------------------

# 9. Datos maestros y datos del periodo
<div align="center">
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/6c77cca3-976d-4940-81d3-6b55e1237684" />
</div>

Uno de los principios de diseño del sistema será separar los datos que
son relativamente permanentes de los datos propios de cada periodo.

## Datos maestros

-   Institución
-   Facultad
-   Programa
-   Sede
-   Bloque
-   Espacio
-   Docente
-   Asignatura

## Datos dependientes del periodo

-   Oferta académica
-   Asignaciones docentes
-   Horarios
-   Restricciones particulares
-   Disponibilidad específica
-   Propuestas de FET
-   Auditorías

Esto permite reutilizar información sin duplicar innecesariamente toda
la base de datos.

------------------------------------------------------------------------

# 10. Oferta académica

La oferta académica representa qué asignaturas se encuentran disponibles
para un programa durante un periodo determinado.

Ejemplo:

``` text
Periodo: 2026-2
Programa: Ingeniería de Sistemas

Asignaturas ofertadas:
- Programación
- Bases de Datos
- Estructuras de Datos
- Sistemas Informáticos
```

Una asignatura puede estar disponible para múltiples programas.

------------------------------------------------------------------------

# 11. Relación docente-programa
<div align="center">
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/005b7e05-8287-46b1-b681-ee1c4089a6cb" />
</div>

Un docente puede trabajar con varios programas.

Por lo tanto, la relación no debe modelarse como una simple propiedad
`programa_id` dentro del docente.

Conceptualmente:

``` text
DOCENTE
    ↕
DOCENTE_PROGRAMA
    ↕
PROGRAMA
```

Ejemplo:

``` text
Docente Juan Pérez
    ├── Ingeniería de Sistemas
    ├── Ingeniería Industrial
    └── Administración
```

------------------------------------------------------------------------

# 12. Relación docente-asignatura

También debe existir una relación que permita determinar qué asignaturas
puede impartir un docente.

Conceptualmente:

``` text
DOCENTE
    ↕
DOCENTE_ASIGNATURA
    ↕
ASIGNATURA
```

Esto evita que cualquier docente pueda ser asignado arbitrariamente a
cualquier asignatura.

------------------------------------------------------------------------

# 13. Relación asignatura-programa

Una asignatura puede pertenecer a varios programas.

Conceptualmente:

``` text
ASIGNATURA
    ↕
ASIGNATURA_PROGRAMA
    ↕
PROGRAMA
```

Esto permite representar asignaturas compartidas entre programas.

------------------------------------------------------------------------

# 14. Gestión de horarios

Un horario representa una clase programada.

Una programación de clase debe permitir determinar:

-   Periodo
-   Facultad
-   Programa
-   Asignatura
-   Docente
-   Día
-   Hora de inicio
-   Hora de finalización
-   Sede
-   Bloque
-   Espacio
-   Estado

Ejemplo:

``` text
Asignatura:
Programación de Estructuras de Datos

Docente:
Juan Pérez

Programa:
Ingeniería de Sistemas

Día:
Lunes

Hora:
06:00 - 08:00

Sede:
Ciénaga

Bloque:
A

Espacio:
Laboratorio de Sistemas 1
```

------------------------------------------------------------------------

# 15. Construcción manual del horario
<div align="center">
<img width="1704" height="923" alt="image" src="https://github.com/user-attachments/assets/7810647a-6857-447b-84cf-9bb64e771972" />
</div>

El Líder de Programa podrá construir el horario manualmente.

Flujo:

``` text
Seleccionar asignatura
        ↓
Seleccionar docente compatible
        ↓
Seleccionar día
        ↓
Seleccionar horario
        ↓
Seleccionar sede
        ↓
Seleccionar bloque
        ↓
Seleccionar espacio
        ↓
Validar conflictos
        ↓
Agregar al horario
```

El sistema debe impedir guardar una asignación incompatible con las
reglas básicas.

------------------------------------------------------------------------

# 16. Disponibilidad y bloqueo de espacios
<div align="center">
<img width="1693" height="929" alt="image" src="https://github.com/user-attachments/assets/258aa646-b5eb-427b-891e-9f54a0f89e55" />
</div>

La disponibilidad de un espacio depende de:

``` text
Periodo + Espacio + Día + Rango horario
```

Ejemplo:

``` text
Laboratorio 1
Lunes
06:00 - 08:00
```

Si un programa reserva ese espacio, el recurso debe mostrarse como
ocupado para los demás líderes.

Visualmente:

``` text
Disponible  → normal
Ocupado     → gris / bloqueado
```

El espacio permanece ocupado hasta que la asignación sea modificada o
eliminada por quien tenga permisos.

------------------------------------------------------------------------

# 17. Control de concurrencia

El color gris de la interfaz no es suficiente para garantizar la
integridad.

El backend debe volver a comprobar la disponibilidad al momento de
guardar.

Ejemplo:

``` text
Líder A selecciona Laboratorio 1
Lunes 06:00 - 08:00

Líder B selecciona Laboratorio 1
Lunes 06:00 - 08:00
```

El sistema debe garantizar que solo uno pueda confirmar la asignación.

La validación definitiva debe realizarse en el servidor y en la base de
datos.

------------------------------------------------------------------------

# 18. Generación automática con FET
<div align="center">
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/d9e896fc-8ffe-4755-9962-31b8f57bdfd9" />
</div>
FET será utilizado como motor de generación automática.

UniFET debe preparar los datos necesarios y generar una propuesta.

Entradas potenciales:

-   Docentes
-   Asignaturas
-   Programas
-   Espacios
-   Sedes
-   Bloques
-   Disponibilidad
-   Créditos
-   Horarios permitidos
-   Restricciones
-   Asignaciones docentes

Flujo:

``` text
Datos académicos
        ↓
Restricciones
        ↓
Configuración FET
        ↓
Ejecución FET
        ↓
Resultado
        ↓
Propuesta UniFET
```

------------------------------------------------------------------------

# 19. Las propuestas de FET NO se guardan inmediatamente

Esta es una regla fundamental.

Una propuesta generada automáticamente es temporal.

``` text
GENERAR
   ↓
PROPUESTA
   ↓
REVISAR
   ├── ACEPTAR → Guardar
   ├── MODIFICAR → Editar propuesta
   └── RECHAZAR → Descartar
```

El líder debe decidir si el resultado es adecuado.

------------------------------------------------------------------------

# 20. Modelo híbrido de generación

UniFET utilizará un enfoque híbrido:

### Manual

El líder crea directamente las asignaciones.

### Automático

FET genera una propuesta.

### Híbrido

El líder puede utilizar FET como punto de partida y después ajustar la
propuesta antes de confirmarla.

Ejemplo:

``` text
FET propone:

Lunes 06:00 - 08:00
Laboratorio 1

El líder revisa.

Decide:

Martes 08:00 - 10:00
Laboratorio 2

Confirma.

La versión modificada es la que se guarda.
```

------------------------------------------------------------------------

# 21. Estados del horario
<div align="center">
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/a4d10ddb-3a97-45ba-a32c-784eb156c7b2" />
</div>

Se propone manejar estados como:

### Borrador

El líder está construyendo el horario.

``` text
BORRADOR
```

Puede modificarlo.

### Propuesta

Resultado temporal generado por FET.

``` text
PROPUESTA
```

Todavía no es definitivo.

### Cerrado

El líder confirmó el horario.

``` text
CERRADO
```

El líder ya no puede modificarlo.

### Modificado por administrador

El administrador realizó un cambio posterior.

``` text
MODIFICADO
```

El detalle exacto de estados podrá refinarse durante el diseño técnico.

------------------------------------------------------------------------

# 22. Cierre del horario

Cuando el líder considera terminado el horario:

``` text
BORRADOR
   ↓
VALIDAR
   ↓
CONFIRMAR
   ↓
CERRAR
```

Después del cierre:

-   El líder no puede modificar.
-   Los espacios quedan consolidados.
-   El administrador conserva permisos de modificación.

------------------------------------------------------------------------

# 23. Modificaciones administrativas

El administrador puede modificar horarios cerrados.

Puede cambiar, según las reglas del sistema:

-   Docente
-   Asignatura
-   Día
-   Hora
-   Sede
-   Bloque
-   Espacio

Las modificaciones posteriores al cierre deben conservar trazabilidad.

Ejemplo:

``` text
Cambio realizado:
Laboratorio 1 → Laboratorio 2

Fecha:
10/09/2026

Usuario:
Administrador

Motivo:
Reorganización de espacios
```

El registro exacto de motivos podrá hacerse obligatorio o configurable.

------------------------------------------------------------------------

# 24. Restricciones

Las restricciones determinan qué combinaciones son válidas o deseables.

## Restricciones básicas

El sistema debe evitar, como mínimo:

-   Docente en dos clases simultáneas.
-   Espacio ocupado por dos clases simultáneamente.
-   Asignación de una clase a un espacio no disponible.
-   Asignación de un docente no compatible con la asignatura.
-   Conflictos de horario.
-   Uso de espacios fuera de las condiciones permitidas.

## Restricciones adicionales

Las restricciones académicas específicas de la institución deben ser
levantadas y configuradas antes de definir el modelo definitivo de FET.

No se deben inventar reglas institucionales que no hayan sido
confirmadas.

------------------------------------------------------------------------

# 25. Auditoría de clases

La auditoría permite verificar si las clases programadas realmente se
están desarrollando.

El Líder de Programa puede seleccionar una fecha y utilizar filtros.

Ejemplo:

``` text
Fecha:
10/09/2026

Sede:
Ciénaga

Bloque:
A

Espacio:
Laboratorio de Sistemas 1
```

El sistema consulta el horario definitivo y muestra las clases que
deberían estar ocurriendo.

------------------------------------------------------------------------

# 26. Ejemplo de auditoría

  --------------------------------------------------------------------------------------------------------
  Hora           Docente   Asignatura     Programa   Facultad     Sede      Bloque   Espacio   Estado
  -------------- --------- -------------- ---------- ------------ --------- -------- --------- -----------
  06:00--08:00   Juan      Estructuras de Sistemas   Ingeniería   Ciénaga   A        Lab. 1    Pendiente
                 Pérez     Datos                                                               

  10:00--12:00   María     Sistemas       Sistemas   Ingeniería   Ciénaga   A        Lab. 1    Pendiente
                 Gómez     Informáticos                                                        
  --------------------------------------------------------------------------------------------------------

El líder realiza la verificación presencial.

------------------------------------------------------------------------

# 27. Resultado de la auditoría

Para cada clase se podrá registrar:

``` text
Verificada
```

o

``` text
No verificada
```

En caso de no realizarse la clase, debe existir un campo para
observación.

Ejemplo:

``` text
Estado:
No realizada

Observación:
El docente no se encontraba en el espacio asignado.
```

También debe registrarse:

-   Fecha de auditoría
-   Hora de verificación
-   Usuario que realizó la auditoría
-   Clase verificada
-   Resultado
-   Observación

------------------------------------------------------------------------

# 28. Historial de auditorías

Las auditorías deben conservarse como registros históricos.

Ejemplo:

``` text
AUDITORÍA #001

Fecha de clase:
10/09/2026

Hora programada:
06:00 - 08:00

Hora de verificación:
06:42

Docente:
Juan Pérez

Asignatura:
Estructuras de Datos

Espacio:
Laboratorio de Sistemas 1

Resultado:
Verificada

Auditor:
Líder del programa
```

Esto permitirá generar posteriormente estadísticas y reportes.

------------------------------------------------------------------------

# 29. Filtros

UniFET debe permitir filtros específicos según el módulo.

Filtros generales:

-   Periodo
-   Facultad
-   Programa
-   Docente
-   Asignatura
-   Sede
-   Bloque
-   Espacio
-   Día
-   Hora
-   Estado

Ejemplos:

``` text
Mostrar todas las clases de Juan Pérez.
```

``` text
Mostrar todas las clases de Ingeniería de Sistemas.
```

``` text
Mostrar todo lo programado en Laboratorio de Sistemas 1.
```

``` text
Mostrar todas las clases de la Sede Ciénaga.
```

------------------------------------------------------------------------

# 30. Control de espacios

El módulo de espacios debe permitir conocer:

-   Qué espacio es.
-   En qué sede está.
-   En qué bloque está.
-   Qué tipo de espacio es.
-   Qué capacidad tiene.
-   Qué clase tiene asignada.
-   Qué docente está asociado.
-   Qué asignatura se desarrolla.
-   A qué programa pertenece la clase.
-   A qué facultad pertenece el programa.
-   En qué horario ocurre.

Esto permite responder preguntas como:

> ¿Quién está dando clase en este salón?

> ¿Qué asignatura se está dictando?

> ¿A qué programa pertenece?

> ¿Qué facultad la administra?

> ¿A qué hora?

> ¿En qué sede?

> ¿En qué bloque?

------------------------------------------------------------------------

# 31. Importación de información

La información podrá ingresar por dos medios:

## Formularios

Para registros individuales o ajustes.

## Archivos Excel

Para cargas masivas.

Flujo:

``` text
Archivo Excel
     ↓
Lectura
     ↓
Validación
     ↓
Detección de errores
     ↓
Vista previa
     ↓
Confirmación
     ↓
Base de datos
```

El sistema no debe insertar directamente información de un Excel sin
validarla.

------------------------------------------------------------------------

# 32. Validación de Excel

Ejemplos de errores:

``` text
Fila 32:
Correo inválido.
```

``` text
Fila 87:
Programa inexistente.
```

``` text
Fila 104:
Código de espacio duplicado.
```

La interfaz debe mostrar al usuario los errores encontrados antes de
confirmar la importación.

------------------------------------------------------------------------

# 33. Plantillas Excel

Inicialmente se deberán definir plantillas para información como:

### Docentes

``` text
Código
Nombre
Apellido
Correo
Estado
```

### Asignaturas

``` text
Código
Nombre
Créditos
Tipo
Estado
```

### Espacios

``` text
Código
Nombre
Tipo
Capacidad
Sede
Bloque
Estado
```

### Programas

``` text
Código
Nombre
Facultad
Estado
```

La estructura definitiva debe definirse a partir de los archivos reales
utilizados por la institución.

------------------------------------------------------------------------

# 34. Exportación

UniFET debe permitir exportar información relevante.

Posibles exportaciones:

-   Horario por programa
-   Horario por docente
-   Horario por espacio
-   Horario por sede
-   Horario por bloque
-   Horario general
-   Resultados de auditoría
-   Reportes
-   Datos académicos

Los formatos específicos se definirán durante la implementación.

------------------------------------------------------------------------

# 35. Reutilización entre periodos

La creación de un nuevo periodo debe permitir reutilizar información
existente.

Ejemplo:

``` text
Periodo 2026-1
      ↓
Crear nuevo periodo
      ↓
2026-2
      ↓
Precargar datos existentes
```

El sistema debe evitar que los datos históricos sean destruidos.

La información del periodo anterior debe permanecer disponible para
consulta.

------------------------------------------------------------------------

# 36. Personalización institucional

UniFET no debe estar construido exclusivamente para Unicaribe.

Debe permitir configurar la identidad de la institución.

Desde el panel administrativo se podrá configurar, como mínimo:

## Identidad

-   Nombre de la institución
-   Nombre del sistema
-   Logo
-   Favicon
-   Lema

## Apariencia

-   Color principal
-   Color secundario
-   Color de acento
-   Colores de interfaz

## Recursos visuales

-   Logo principal
-   Logo para documentos
-   Imagen institucional
-   Otros elementos configurables

------------------------------------------------------------------------

# 37. Primera implementación

La primera institución objetivo será:

**Unicaribe --- Institución Universitaria del Caribe**

La configuración inicial deberá poder representar:

-   Su identidad visual
-   Sus facultades
-   Sus programas
-   Sus sedes
-   Sus bloques
-   Sus espacios
-   Sus docentes
-   Sus asignaturas
-   Sus periodos académicos

Sin embargo, estos datos no deben quedar codificados directamente en la
aplicación.

------------------------------------------------------------------------

# 38. Visión de producto

La arquitectura de UniFET debe permitir que el sistema evolucione desde
una solución para una institución específica hacia un producto
configurable.

Conceptualmente:

``` text
                  UNIFET
                    │
          ┌─────────┴─────────┐
          │                   │
      Unicaribe          Institución B
          │                   │
      Configuración       Configuración
          │                   │
       Datos               Datos
          │                   │
          └─────────┬─────────┘
                    ↓
             Mismo núcleo
```

El objetivo es que cada institución pueda configurar su propia
estructura sin modificar el código fuente principal.

------------------------------------------------------------------------

# 39. Arquitectura conceptual
<div align ="center">
<img width="747" height="498" alt="image" src="https://github.com/user-attachments/assets/7ec1894f-40ba-47e5-a175-c7c2cd087f36" />
</div>


``` text
┌──────────────────────────────────────────────┐
│                 INTERFAZ WEB                 │
│                                              │
│  Administrador   │   Líder de Programa      │
└──────────────────────┬───────────────────────┘
                       │
                       ↓
┌──────────────────────────────────────────────┐
│                    API                       │
├──────────────────────────────────────────────┤
│ Autenticación y autorización                 │
│ Gestión institucional                        │
│ Gestión académica                            │
│ Gestión de espacios                          │
│ Gestión de periodos                          │
│ Gestión de horarios                          │
│ Restricciones                                │
│ Integración FET                              │
│ Auditoría                                    │
│ Reportes                                     │
│ Importación / Exportación                    │
│ Configuración institucional                  │
└──────────────────────┬───────────────────────┘
                       │
             ┌─────────┴──────────┐
             ↓                    ↓
      ┌──────────────┐     ┌──────────────┐
      │ Base de datos│     │     FET      │
      └──────────────┘     └──────────────┘
```

------------------------------------------------------------------------

# 40. Arquitectura lógica de generación

``` text
                INFORMACIÓN
                     ↓
        ┌─────────────────────────┐
        │ Validación y normalización│
        └────────────┬────────────┘
                     ↓
              RESTRICCIONES
                     ↓
             CONFIGURACIÓN FET
                     ↓
                   FET
                     ↓
             RESULTADO FET
                     ↓
               PROPUESTA
                     ↓
          REVISIÓN DEL LÍDER
             ┌───────┼───────┐
             ↓       ↓       ↓
          Aceptar  Editar  Rechazar
             │       │       │
             └───┬───┘       │
                 ↓           ↓
             CONFIRMAR    DESCARTAR
                 ↓
               GUARDAR
                 ↓
               CERRAR
```

------------------------------------------------------------------------

# 41. Arquitectura de datos conceptual
<div align= "center">
<img width="747" height="498" alt="image" src="https://github.com/user-attachments/assets/a2254960-e158-483f-80df-81de1525b61d" />
</div>

``` text
INSTITUCIÓN
    │
    ├── FACULTAD
    │      │
    │      └── PROGRAMA
    │
    ├── SEDE
    │      │
    │      └── BLOQUE
    │             │
    │             └── ESPACIO
    │
    ├── DOCENTE
    │      ↕
    │  DOCENTE_PROGRAMA
    │
    ├── ASIGNATURA
    │      ↕
    │  ASIGNATURA_PROGRAMA
    │      ↕
    │  DOCENTE_ASIGNATURA
    │
    └── PERIODO
           │
           └── OFERTA ACADÉMICA
                   │
                   └── HORARIO
                         │
             ┌───────────┼───────────┐
             ↓           ↓           ↓
          DOCENTE    ASIGNATURA    ESPACIO
                                     │
                             SEDE / BLOQUE

HORARIO
   │
   ├── RESTRICCIONES
   │
   ├── PROPUESTAS FET
   │
   └── AUDITORÍAS
```

Este esquema es conceptual. El modelo entidad-relación definitivo debe
realizarse antes de implementar la base de datos.

------------------------------------------------------------------------

# 42. Reglas de negocio principales

## RB-01 --- Roles

El sistema solo tendrá dos roles:

-   Administrador
-   Líder de Programa

## RB-02 --- Docentes

Un docente académico no necesita ser usuario del sistema.

## RB-03 --- Líder

El líder trabaja sobre uno o los programas que tenga autorizados.

## RB-04 --- Docentes compartidos

Un docente puede pertenecer a varios programas.

## RB-05 --- Asignaturas compartidas

Una asignatura puede pertenecer a varios programas.

## RB-06 --- Compatibilidad

Solo deben poder asignarse docentes compatibles con la asignatura según
las relaciones configuradas.

## RB-07 --- Espacios

Un mismo espacio no puede utilizarse simultáneamente para dos clases
dentro del mismo periodo.

## RB-08 --- Disponibilidad visual

Los espacios ocupados deben mostrarse como no disponibles para otros
líderes.

## RB-09 --- Validación definitiva

El backend debe validar nuevamente la disponibilidad antes de confirmar
una asignación.

## RB-10 --- FET

Una propuesta FET no es definitiva hasta que el líder la confirme.

## RB-11 --- Edición de propuesta

El líder puede modificar una propuesta FET antes de guardarla.

## RB-12 --- Cierre

Al cerrar un horario, el líder pierde la capacidad de modificarlo.

## RB-13 --- Administrador

El administrador puede modificar horarios cerrados.

## RB-14 --- Trazabilidad

Los cambios administrativos sobre horarios cerrados deben poder
registrarse.

## RB-15 --- Periodos

Los datos de un periodo anterior no deben eliminarse al crear uno nuevo.

## RB-16 --- Personalización

La identidad visual no debe estar codificada de forma fija para una
única institución.

## RB-17 --- Auditoría

La auditoría debe consultar el horario definitivo y no una propuesta
temporal de FET.

------------------------------------------------------------------------

# 43. Flujo general del sistema
<div align= "center">
<img width="747" height="484" alt="image" src="https://github.com/user-attachments/assets/3d1c0cf8-e8e5-4911-ad23-c542cabf0746" />
</div>

``` text
                 ADMINISTRADOR
                       │
                       ↓
          CONFIGURACIÓN INSTITUCIONAL
                       │
       ┌───────────────┼────────────────┐
       ↓               ↓                ↓
 Institucional      Académico        Espacios
       │               │                │
 Facultades         Docentes          Sedes
 Programas          Asignaturas       Bloques
                    Créditos          Salones
       └───────────────┬────────────────┘
                       ↓
                CREAR PERIODO
                       ↓
              CARGAR / REUTILIZAR
                       ↓
               OFERTA ACADÉMICA
                       ↓
              ASIGNACIÓN DOCENTE
                       ↓
              DEFINIR RESTRICCIONES
                       ↓
              CONSTRUIR HORARIO
                 ┌─────┴─────┐
                 ↓           ↓
              MANUAL       FET
                 │           │
                 │       PROPUESTA
                 │           │
                 │     REVISAR / EDITAR
                 │           │
                 └─────┬─────┘
                       ↓
                  VALIDAR
                       ↓
               CONFIRMAR HORARIO
                       ↓
                    CERRAR
                       ↓
              HORARIO DEFINITIVO
                       │
             ┌─────────┴─────────┐
             ↓                   ↓
           LÍDER               ADMIN
             │                   │
             │                 Puede
             │                modificar
             │                   │
             └─────────┬─────────┘
                       ↓
                    AUDITORÍA
                       ↓
              VERIFICACIÓN DE CLASE
                       ↓
                    REPORTES
```

------------------------------------------------------------------------

# 44. Módulos del sistema

## 1. Gestión institucional

Administración de la información general de la institución.

## 2. Gestión de docentes

Registro y administración de docentes.

## 3. Gestión de asignaturas

Registro, créditos y relaciones académicas.

## 4. Gestión de programas

Administración de programas y sus relaciones.

## 5. Gestión de facultades

Administración de facultades.

## 6. Gestión de sedes

Administración de sedes físicas.

## 7. Gestión de bloques

Administración de bloques por sede.

## 8. Gestión de espacios/salones

Control de aulas, laboratorios y demás espacios.

## 9. Gestión de periodos académicos

Creación, configuración y reutilización de periodos.

## 10. Oferta académica

Definición de asignaturas ofertadas por programa y periodo.

## 11. Asignación docente

Relación entre docente, asignatura, programa y periodo.

## 12. Gestión de restricciones

Configuración y aplicación de restricciones.

## 13. Generación manual de horarios

Construcción directa por parte del líder.

## 14. Generación automática con FET

Generación de propuestas mediante el motor FET.

## 15. Validación de horarios

Detección de conflictos y validaciones.

## 16. Publicación/cierre

Confirmación y bloqueo del horario.

## 17. Auditoría de clases

Verificación presencial de las clases programadas.

## 18. Reportes

Consulta y generación de información consolidada.

## 19. Importación/exportación

Carga masiva y extracción de información.

## 20. Personalización institucional

Configuración de logo, lema, colores e identidad visual.

------------------------------------------------------------------------

# 45. Panel del Administrador
<div align="center">
<img width="1536" height="508" alt="image" src="https://github.com/user-attachments/assets/f7182d11-ac5f-4b12-92b6-7ac754563a44" />
</div>
El administrador tendrá acceso a una vista global.

Secciones propuestas:

``` text
Dashboard
│
├── Institución
├── Facultades
├── Programas
├── Sedes
├── Bloques
├── Espacios
├── Docentes
├── Asignaturas
├── Periodos
├── Oferta académica
├── Asignaciones
├── Restricciones
├── Horarios
├── Auditorías
├── Reportes
├── Importar / Exportar
└── Personalización
```

El dashboard puede mostrar posteriormente:

-   Periodo activo
-   Programas
-   Docentes
-   Espacios
-   Horarios creados
-   Horarios cerrados
-   Clases auditadas
-   Incidencias

------------------------------------------------------------------------

# 46. Panel del Líder de Programa
<div aling ="center">
<img width="1536" height="493" alt="image" src="https://github.com/user-attachments/assets/cd72b9b1-47cf-446e-af48-9befdbf68b44" />
</div>
El líder tendrá una vista enfocada en su ámbito.

``` text
Dashboard
│
├── Mi programa
├── Docentes
├── Asignaturas
├── Oferta académica
├── Espacios
├── Restricciones
├── Crear horario
├── Generar con FET
├── Mi horario
├── Cerrar horario
├── Auditoría
└── Reportes
```

Los datos deben filtrarse automáticamente según el programa que tenga
asignado.

------------------------------------------------------------------------

# 47. Seguridad y autorización

La autorización debe realizarse en backend.

No basta con ocultar botones en la interfaz.

Ejemplo:

``` text
Líder intenta modificar horario cerrado
       ↓
API verifica rol
       ↓
API verifica propiedad/alcance
       ↓
Horario = CERRADO
       ↓
RECHAZAR
```

El administrador sí podrá realizar la operación.

------------------------------------------------------------------------

# 48. Trazabilidad

Las operaciones importantes deberían poder registrarse.

Ejemplos:

-   Creación de horario
-   Cierre de horario
-   Modificación administrativa
-   Importación de datos
-   Eliminación de datos
-   Generación FET
-   Confirmación de propuesta
-   Auditoría de clase

Se recomienda conservar:

-   Usuario
-   Fecha
-   Hora
-   Acción
-   Entidad afectada
-   Valor anterior, cuando aplique
-   Valor nuevo, cuando aplique
-   Motivo, cuando corresponda

------------------------------------------------------------------------

# 49. Tecnologías

La selección final del stack debe validarse con las necesidades del
equipo.

Arquitectura recomendada:

``` text
Frontend
    ↓
API / Backend
    ↓
Base de datos
    ↓
Servicio de integración FET
```

Componentes principales:

-   Frontend web
-   Backend/API REST
-   Base de datos relacional
-   Servicio de procesamiento de archivos
-   Integración FET
-   Sistema de autenticación
-   Sistema de autorización
-   Sistema de auditoría
-   Sistema de configuración institucional

El stack tecnológico definitivo se documentará después de seleccionar
las tecnologías.

------------------------------------------------------------------------

# 50. Integración con FET

La integración debe aislarse del resto de la aplicación.

Conceptualmente:

``` text
UniFET
   │
   ↓
Preparador de datos
   │
   ↓
Generador de configuración FET
   │
   ↓
FET-CL
   │
   ↓
Procesador de resultados
   │
   ↓
Propuesta UniFET
```

Esto permite que la lógica de negocio de UniFET no dependa directamente
de los detalles internos de FET.

------------------------------------------------------------------------

# 51. Principio de separación de responsabilidades

UniFET y FET tendrán responsabilidades diferentes.

### UniFET

Administra:

-   Usuarios
-   Institución
-   Datos académicos
-   Espacios
-   Periodos
-   Asignaciones
-   Restricciones
-   Horarios
-   Auditorías
-   Reportes
-   Personalización

### FET

Se utiliza para:

-   Procesar restricciones compatibles
-   Buscar soluciones
-   Optimizar la distribución
-   Generar propuestas de horarios

FET no será la fuente principal de verdad de los datos institucionales.

La base de datos de UniFET será la fuente principal de información del
sistema.

------------------------------------------------------------------------

# 52. Principio de fuente única de verdad

La información institucional se almacenará en UniFET.

Ejemplo:

``` text
Docente
Asignatura
Programa
Espacio
Periodo
```

se administran en UniFET.

FET recibe una representación de esos datos para realizar su
procesamiento.

El resultado se transforma nuevamente al modelo de UniFET.

------------------------------------------------------------------------

# 53. Manejo de errores

El sistema debe contemplar:

-   Datos incompletos
-   Duplicados
-   Docentes inexistentes
-   Asignaturas inexistentes
-   Espacios inexistentes
-   Programas inexistentes
-   Conflictos de horarios
-   Conflictos de espacios
-   Propuestas FET inválidas
-   Errores de importación
-   Errores de integración con FET

Los errores deben ser comprensibles para el usuario.

------------------------------------------------------------------------

# 54. Requisitos funcionales preliminares

### RF-01

El sistema debe permitir autenticación de usuarios.

### RF-02

El sistema debe manejar los roles Administrador y Líder de Programa.

### RF-03

El sistema debe permitir gestionar instituciones.

### RF-04

El sistema debe permitir gestionar facultades.

### RF-05

El sistema debe permitir gestionar programas.

### RF-06

El sistema debe permitir gestionar sedes.

### RF-07

El sistema debe permitir gestionar bloques.

### RF-08

El sistema debe permitir gestionar espacios.

### RF-09

El sistema debe permitir gestionar docentes.

### RF-10

El sistema debe permitir asociar docentes a múltiples programas.

### RF-11

El sistema debe permitir gestionar asignaturas.

### RF-12

El sistema debe permitir registrar créditos de las asignaturas.

### RF-13

El sistema debe permitir asociar asignaturas a múltiples programas.

### RF-14

El sistema debe permitir asociar docentes con asignaturas.

### RF-15

El sistema debe permitir crear periodos académicos.

### RF-16

El sistema debe permitir reutilizar información de periodos anteriores.

### RF-17

El sistema debe permitir gestionar la oferta académica.

### RF-18

El sistema debe permitir construir horarios manualmente.

### RF-19

El sistema debe validar conflictos durante la construcción del horario.

### RF-20

El sistema debe mostrar espacios ocupados como no disponibles.

### RF-21

El sistema debe verificar disponibilidad en backend antes de guardar.

### RF-22

El sistema debe permitir generar propuestas mediante FET.

### RF-23

El sistema no debe guardar automáticamente las propuestas de FET.

### RF-24

El líder debe poder aceptar una propuesta FET.

### RF-25

El líder debe poder modificar una propuesta antes de confirmarla.

### RF-26

El líder debe poder descartar una propuesta.

### RF-27

El líder debe poder cerrar su horario.

### RF-28

El líder no debe poder modificar un horario cerrado.

### RF-29

El administrador debe poder modificar horarios cerrados.

### RF-30

El sistema debe registrar cambios administrativos.

### RF-31

El sistema debe permitir realizar auditorías.

### RF-32

El sistema debe mostrar las clases programadas para una fecha
determinada.

### RF-33

El sistema debe permitir marcar una clase como verificada.

### RF-34

El sistema debe permitir registrar observaciones.

### RF-35

El sistema debe conservar el historial de auditorías.

### RF-36

El sistema debe permitir filtrar información.

### RF-37

El sistema debe permitir importar información mediante Excel.

### RF-38

El sistema debe validar archivos antes de importarlos.

### RF-39

El sistema debe permitir exportar información.

### RF-40

El sistema debe permitir personalizar la identidad institucional.

------------------------------------------------------------------------

# 55. Requisitos no funcionales preliminares

## RNF-01 --- Seguridad

La información debe estar protegida mediante autenticación y
autorización.

## RNF-02 --- Integridad

La base de datos debe impedir inconsistencias críticas.

## RNF-03 --- Concurrencia

Dos usuarios no deben poder reservar el mismo recurso simultáneamente.

## RNF-04 --- Escalabilidad

La arquitectura debe permitir agregar instituciones y aumentar el
volumen de datos.

## RNF-05 --- Configurabilidad

La identidad visual y parte de las reglas deben poder configurarse.

## RNF-06 --- Usabilidad

La interfaz debe facilitar la creación rápida de horarios.

## RNF-07 --- Trazabilidad

Las operaciones críticas deben poder auditarse.

## RNF-08 --- Mantenibilidad

La integración con FET debe estar desacoplada del resto del sistema.

## RNF-09 --- Importación

Las cargas masivas deben proporcionar validaciones y mensajes de error
claros.

## RNF-10 --- Disponibilidad

El sistema debe poder utilizarse durante los procesos de planificación y
auditoría.

------------------------------------------------------------------------

# 56. Flujo de creación rápida

El profesor planteó una dinámica donde los líderes deben poder escoger
rápidamente los mejores horarios y espacios.

Por ello, la interfaz debe priorizar velocidad.

Un flujo manual ideal sería:

``` text
Asignatura
    ↓
Docente
    ↓
Día
    ↓
Hora
    ↓
Espacios disponibles
    ↓
Confirmar
```

Los espacios ocupados deben identificarse inmediatamente.

El sistema debe minimizar pasos innecesarios.

------------------------------------------------------------------------

# 57. Visualización de disponibilidad

Una posible representación:

``` text
                LUNES

              06-08   08-10   10-12   12-14

Lab 1           ⬜      ⬜      🟥      ⬜
Lab 2           ⬜      🟥      ⬜      ⬜
Aula 101        🟥      ⬜      ⬜      🟥
Aula 102        ⬜      ⬜      ⬜      ⬜
```

Leyenda:

``` text
⬜ Disponible
🟥 Ocupado
```

El diseño visual definitivo será definido durante la etapa de UX/UI.

------------------------------------------------------------------------

# 58. Auditoría orientada a espacios

La auditoría puede comenzar desde diferentes puntos:

### Por fecha

``` text
10/09/2026
```

### Por espacio

``` text
Laboratorio 1
```

### Por docente

``` text
Juan Pérez
```

### Por programa

``` text
Ingeniería de Sistemas
```

### Por sede

``` text
Ciénaga
```

Esto permite que UniFET funcione como herramienta de control académico y
no solamente como generador de horarios.

------------------------------------------------------------------------

# 59. Ejemplo de caso completo

Supongamos:

``` text
Institución:
Unicaribe

Facultad:
Ingeniería

Programa:
Ingeniería de Sistemas

Periodo:
2026-2

Asignatura:
Programación de Estructuras de Datos

Créditos:
3

Docente:
Juan Pérez
```

El líder puede:

``` text
1. Seleccionar asignatura.
2. Seleccionar docente.
3. Seleccionar día y hora.
4. Consultar espacios.
5. Ver disponibilidad.
6. Seleccionar Laboratorio 1.
7. Guardar.
```

El sistema registra:

``` text
Lunes
06:00 - 08:00
Programación de Estructuras de Datos
Juan Pérez
Ingeniería de Sistemas
Ingeniería
Sede Ciénaga
Bloque A
Laboratorio 1
Periodo 2026-2
```

Otro líder intenta usar:

``` text
Laboratorio 1
Lunes
06:00 - 08:00
```

El sistema lo muestra como ocupado y no permite reservarlo.

------------------------------------------------------------------------

# 60. Caso completo con FET

El líder selecciona:

``` text
Generar horario automáticamente
```

UniFET prepara los datos:

``` text
Docentes
Asignaturas
Programas
Espacios
Disponibilidad
Restricciones
```

FET genera:

``` text
PROPUESTA #001
```

El líder la revisa.

Si no le gusta:

``` text
Descartar
```

Puede modificar restricciones y volver a generar.

Si le gusta:

``` text
Aceptar
```

Si desea ajustar:

``` text
Editar propuesta
```

Finalmente:

``` text
Confirmar
↓
Cerrar
```

------------------------------------------------------------------------

# 61. Caso completo de auditoría

Horario definitivo:

``` text
Laboratorio 1
Jueves
10:00 - 12:00

Asignatura:
Sistemas Informáticos

Docente:
María Gómez
```

El líder realiza auditoría:

``` text
Fecha:
Jueves 10/09/2026

Espacio:
Laboratorio 1
```

El sistema muestra:

``` text
10:00 - 12:00
Sistemas Informáticos
María Gómez

[ VERIFICAR ]
[ NO REALIZADA ]
```

El líder entra al laboratorio.

Si encuentra la clase:

``` text
VERIFICADA
```

Si no:

``` text
NO REALIZADA

Observación:
No se encontró al docente en el espacio.
```

------------------------------------------------------------------------

# 62. Roadmap propuesto
<div align ="center">
<img width="1774" height="887" alt="image" src="https://github.com/user-attachments/assets/561a92b7-a653-4705-883b-b41f48749f75" />
</div>

## Fase 1 --- Análisis

-   Validar requisitos
-   Confirmar reglas académicas
-   Confirmar restricciones
-   Definir estructura de Excel
-   Confirmar estructura institucional
-   Definir alcance de FET

## Fase 2 --- Diseño

-   Arquitectura
-   Modelo de datos
-   ERD
-   Casos de uso
-   API
-   Wireframes
-   Sistema de permisos
-   Diseño de integración FET

## Fase 3 --- Base del sistema

-   Proyecto
-   Autenticación
-   Roles
-   Institución
-   Configuración
-   Base de datos

## Fase 4 --- Gestión académica

-   Facultades
-   Programas
-   Docentes
-   Asignaturas
-   Créditos
-   Relaciones

## Fase 5 --- Espacios

-   Sedes
-   Bloques
-   Espacios
-   Disponibilidad

## Fase 6 --- Periodos

-   Creación
-   Reutilización
-   Oferta académica
-   Asignaciones

## Fase 7 --- Horarios

-   Construcción manual
-   Validación
-   Bloqueo de recursos
-   Cierre

## Fase 8 --- FET

-   Generación de datos
-   Configuración
-   Ejecución
-   Procesamiento
-   Propuestas
-   Aceptación/rechazo
-   Edición

## Fase 9 --- Auditoría

-   Consulta
-   Verificación
-   Observaciones
-   Historial
-   Reportes

## Fase 10 --- Producto configurable

-   Logo
-   Colores
-   Lema
-   Configuración institucional

## Fase 11 --- Pruebas

-   Pruebas unitarias
-   Integración
-   Concurrencia
-   FET
-   Importación
-   Seguridad
-   Auditoría
-   Pruebas de usuario

------------------------------------------------------------------------

# 63. Pendientes por confirmar

Antes de cerrar definitivamente el diseño técnico, deben confirmarse:

1.  Reglas exactas de créditos e intensidad horaria.
2.  Restricciones académicas específicas.
3.  Restricciones de docentes.
4.  Restricciones de espacios.
5.  Disponibilidad de docentes.
6.  Disponibilidad de espacios.
7.  Estructura real de los archivos Excel.
8.  Formato de los horarios utilizados actualmente.
9.  Significado exacto de códigos como `G1`, `G2`, `G3`, `G1-L`, etc.
10. Si una asignatura puede tener varias sesiones semanales.
11. Duración estándar de las sesiones.
12. Horarios disponibles por sede.
13. Si un líder puede administrar uno o varios programas.
14. Si el líder debe poder asignar docentes o únicamente seleccionar
    docentes previamente asignados por el administrador.
15. Qué restricciones deben enviarse exactamente a FET.
16. Cómo se debe manejar una modificación administrativa después del
    cierre.
17. Si el sistema debe publicar horarios inmediatamente después del
    cierre o requiere una etapa adicional de publicación.
18. Formatos de exportación requeridos.
19. Reglas específicas para auditoría.
20. Política de conservación histórica de periodos y auditorías.

------------------------------------------------------------------------

# 64. Decisiones confirmadas

A partir de la reunión con el profesor, quedan establecidas las
siguientes decisiones:

-   UniFET será un sistema de planificación, gestión y auditoría de
    horarios.
-   El sistema tendrá dos roles: Administrador y Líder de Programa.
-   El docente común no es usuario final.
-   El administrador registra docentes.
-   Un docente puede pertenecer a varios programas.
-   Una asignatura puede pertenecer a varios programas.
-   Las asignaturas tienen créditos.
-   El líder crea el horario de su programa.
-   El líder puede crear horarios manualmente.
-   El líder puede utilizar FET para generar una propuesta.
-   Las propuestas de FET no se guardan automáticamente.
-   El líder puede aceptar, modificar o descartar una propuesta antes de
    confirmarla.
-   Una vez cerrado el horario, el líder no puede modificarlo.
-   El administrador puede modificar horarios cerrados.
-   Los espacios ocupados deben aparecer como no disponibles para otros
    líderes.
-   La disponibilidad debe validarse también en backend.
-   La información debe poder cargarse mediante formularios.
-   La información debe poder importarse desde Excel.
-   Los datos deben reutilizarse entre periodos.
-   El sistema debe permitir filtrar información por diferentes
    criterios.
-   El sistema debe incluir auditoría de clases.
-   La auditoría debe permitir verificar docente, asignatura, programa,
    facultad, sede, bloque, espacio y horario.
-   La institución debe poder personalizar logo, lema y colores.
-   La primera implementación será para Unicaribe.
-   La arquitectura debe prepararse para futuras instituciones.

------------------------------------------------------------------------

# 65. Visión final

UniFET busca convertirse en una plataforma que cubra el ciclo completo
de planificación y control académico:

``` text
                  INFORMACIÓN
                       ↓
                 PLANIFICACIÓN
                       ↓
                ASIGNACIÓN DOCENTE
                       ↓
                 RESTRICCIONES
                       ↓
          ┌────────────┴────────────┐
          ↓                         ↓
       MANUAL                       FET
          │                         │
          │                    PROPUESTA
          │                         │
          └────────────┬────────────┘
                       ↓
                  REVISIÓN
                       ↓
                  VALIDACIÓN
                       ↓
                    CIERRE
                       ↓
              HORARIO DEFINITIVO
                       ↓
                   EJECUCIÓN
                       ↓
                   AUDITORÍA
                       ↓
                    REPORTES
```

El objetivo no es solamente decir **"qué clase debería ocurrir y
dónde"**, sino construir un sistema capaz de gestionar todo el proceso:

> **quién enseña qué, para qué programa, en qué facultad, durante qué
> periodo, en qué sede, bloque y espacio, en qué día y hora, bajo qué
> restricciones, cómo se generó el horario y posteriormente si la clase
> realmente se realizó.**

------------------------------------------------------------------------

## Estado del proyecto

**Estado actual:** Definición funcional y diseño conceptual.

**Institución inicial:** Unicaribe --- Institución Universitaria del
Caribe.

**Producto:** UniFET.

**Motor de generación:** FET / FET-CL.

**Roles:** Administrador + Líder de Programa.

**Modelo de generación:** Manual + Automático + Híbrido.

**Auditoría:** Sí.

**Importación Excel:** Sí.

**Personalización institucional:** Sí.

**Arquitectura preparada para múltiples instituciones:** Sí.

------------------------------------------------------------------------

## Próximo paso recomendado

Antes de comenzar nuevamente con el código, se recomienda completar en
este orden:

1.  **Modelo de dominio definitivo**
2.  **Modelo entidad-relación**
3.  **Estados y transiciones de horarios**
4.  **Reglas de negocio detalladas**
5.  **Casos de uso**
6.  **Diseño de integración FET**
7.  **Estructura de importación Excel**
8.  **Arquitectura técnica**
9.  **API**
10. **Wireframes**
11. **Implementación**

> **Nota:** Este documento representa la definición funcional obtenida
> hasta la reunión actual. Las reglas académicas específicas y la
> estructura real de los datos institucionales deben validarse con la
> institución antes de convertirlas en restricciones técnicas
> permanentes.
