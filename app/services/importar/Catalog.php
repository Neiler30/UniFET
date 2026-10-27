<?php

namespace App\Services\Importar;

class Catalog {
    
    /**
     * Retorna el catálogo completo de entidades importables, sus campos y alias.
     */
    public static function getEntities(): array {
        return [
            'ASIGNATURA' => [
                'nombre_amigable' => 'Asignaturas y Malla Curricular',
                'campos' => [
                    'codigo' => [
                        'label' => 'Código',
                        'requerido' => true,
                        'alias' => ['codigo', 'cod', 'cod asig', 'codigo asignatura', 'cod asignatura', 'identificador']
                    ],
                    'nombre' => [
                        'label' => 'Nombre',
                        'requerido' => true,
                        'alias' => ['nombre', 'nombre asignatura', 'asignatura', 'materia', 'nombre materia']
                    ],
                    'creditos' => [
                        'label' => 'Créditos',
                        'requerido' => true,
                        'alias' => ['creditos', 'n creditos', 'num creditos', 'cantidad creditos']
                    ],
                    'programa' => [
                        'label' => 'Programa Académico',
                        'requerido' => false,
                        'alias' => ['programa', 'programa academico', 'carrera', 'nombre programa', 'cod programa', 'codigo programa']
                    ],
                    'nivel_academico' => [
                        'label' => 'Nivel Académico',
                        'requerido' => false,
                        'alias' => ['nivel academico', 'nivel de formacion', 'nivel formacion', 'nivel', 'grado', 'tipo programa']
                    ],
                    'semestre' => [
                        'label' => 'Semestre',
                        'requerido' => false,
                        'alias' => ['semestre', 'sem', 'semestre curricular', 'nivel semestre', 'semestre academico']
                    ],
                    'tipo' => [
                        'label' => 'Tipo / Componente',
                        'requerido' => false,
                        'alias' => ['tipo', 'tipo materia', 'modalidad', 'clasificacion', 'componente', 'area']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'estatus', 'status']
                    ]
                ]
            ],
            'DOCENTE' => [
                'nombre_amigable' => 'Docentes',
                'campos' => [
                    'documento' => [
                        'label' => 'Documento',
                        'requerido' => true,
                        'alias' => ['documento', 'cedula', 'cc', 'identificacion', 'num doc', 'id']
                    ],
                    'nombre' => [
                        'label' => 'Nombre Completo',
                        'requerido' => true,
                        'alias' => ['nombre', 'nombre completo', 'nombres', 'docente', 'profesor']
                    ],
                    'correo' => [
                        'label' => 'Correo Electrónico',
                        'requerido' => false,
                        'alias' => ['correo', 'email', 'correo electronico']
                    ],
                    'telefono' => [
                        'label' => 'Teléfono',
                        'requerido' => false,
                        'alias' => ['telefono', 'celular', 'tel', 'contacto']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'estatus', 'status']
                    ]
                ]
            ],
            'ESPACIO' => [
                'nombre_amigable' => 'Espacios (Salones/Laboratorios)',
                'campos' => [
                    'nombre' => [
                        'label' => 'Nombre / Código',
                        'requerido' => true,
                        'alias' => ['nombre', 'codigo', 'salon', 'espacio', 'aula']
                    ],
                    'bloque' => [
                        'label' => 'Bloque',
                        'requerido' => true,
                        'alias' => ['bloque', 'edificio', 'torre']
                    ],
                    'sede' => [
                        'label' => 'Sede',
                        'requerido' => true,
                        'alias' => ['sede', 'campus', 'ubicacion']
                    ],
                    'tipo' => [
                        'label' => 'Tipo',
                        'requerido' => false,
                        'alias' => ['tipo', 'tipo espacio', 'clasificacion']
                    ],
                    'capacidad' => [
                        'label' => 'Capacidad',
                        'requerido' => false,
                        'alias' => ['capacidad', 'aforo', 'sillas', 'cupos']
                    ]
                ]
            ],
            'PROGRAMA' => [
                'nombre_amigable' => 'Programas Académicos',
                'campos' => [
                    'codigo' => [
                        'label' => 'Código',
                        'requerido' => false,
                        'alias' => ['codigo', 'cod', 'identificador']
                    ],
                    'nombre' => [
                        'label' => 'Nombre del Programa',
                        'requerido' => true,
                        'alias' => ['nombre', 'programa', 'carrera', 'programa academico']
                    ],
                    'facultad' => [
                        'label' => 'Facultad',
                        'requerido' => true,
                        'alias' => ['facultad', 'escuela']
                    ],
                    'nivel_academico' => [
                        'label' => 'Nivel Académico',
                        'requerido' => false,
                        'alias' => ['nivel academico', 'nivel', 'tipo programa', 'grado']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'status']
                    ]
                ]
            ],
            'FACULTAD' => [
                'nombre_amigable' => 'Facultades',
                'campos' => [
                    'nombre' => [
                        'label' => 'Nombre de la Facultad',
                        'requerido' => true,
                        'alias' => ['nombre', 'facultad', 'escuela']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'status']
                    ]
                ]
            ],
            'SEDE' => [
                'nombre_amigable' => 'Sedes',
                'campos' => [
                    'nombre' => [
                        'label' => 'Nombre de la Sede',
                        'requerido' => true,
                        'alias' => ['nombre', 'sede', 'campus', 'ubicacion']
                    ],
                    'direccion' => [
                        'label' => 'Dirección',
                        'requerido' => false,
                        'alias' => ['direccion', 'dir']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'status']
                    ]
                ]
            ],
            'BLOQUE' => [
                'nombre_amigable' => 'Bloques / Edificios',
                'campos' => [
                    'nombre' => [
                        'label' => 'Nombre del Bloque',
                        'requerido' => true,
                        'alias' => ['nombre', 'bloque', 'edificio', 'torre']
                    ],
                    'sede' => [
                        'label' => 'Sede',
                        'requerido' => true,
                        'alias' => ['sede', 'campus', 'ubicacion']
                    ],
                    'estado' => [
                        'label' => 'Estado',
                        'requerido' => false,
                        'alias' => ['estado', 'status']
                    ]
                ]
            ],
        ];
    }
}
