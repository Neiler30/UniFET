<?php
$activo = $activo ?? '';
$this->render('shared/tabs', ['tabs' => [
    ['etiqueta' => 'Periodos', 'ruta' => 'admin/academico/periodos', 'activa' => $activo === 'periodos'],
    ['etiqueta' => 'Oferta', 'ruta' => 'admin/academico/oferta', 'activa' => $activo === 'oferta'],
    ['etiqueta' => 'Areas Academicas', 'ruta' => 'admin/academico/areas', 'activa' => $activo === 'areas'],
    ['etiqueta' => 'Asignaturas', 'ruta' => 'admin/academico/asignaturas', 'activa' => $activo === 'asignaturas'],
    ['etiqueta' => 'Docentes', 'ruta' => 'admin/academico/docentes', 'activa' => $activo === 'docentes'],
    ['etiqueta' => 'Restricciones', 'ruta' => 'admin/academico/restricciones', 'activa' => $activo === 'restricciones'],
]]);
