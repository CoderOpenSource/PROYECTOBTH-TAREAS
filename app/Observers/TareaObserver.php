<?php

namespace App\Observers;

use App\Models\Tarea;
use App\Models\Notificacion;
use App\Models\NotificacionUsuario;

class TareaObserver
{
    /**
     * Handle the Tarea "created" event.
     */
    public function created(Tarea $tarea)
    {
        // Crear la notificación automática
        $notificacion = Notificacion::create([
            'titulo' => 'Nueva Tarea Asignada',
            'mensaje' => "El profesor ha asignado la siguiente tarea: {$tarea->titulo}",
            'es_automatica' => true,
            'curso_id' => $tarea->curso_id,
            'profesor_id' => $tarea->profesor_id,
        ]);

        // Asociar la notificación a cada estudiante en el curso
        $curso = $tarea->curso; // Obtener el curso relacionado con la tarea
        $estudiantes = $curso->estudiantes; // Obtener los estudiantes inscritos en el curso

        foreach ($estudiantes as $estudiante) {
            NotificacionUsuario::create([
                'notificacion_id' => $notificacion->id,
                'usuario_id' => $estudiante->id,
                'leido' => false, // Inicialmente, la notificación está sin leer
            ]);
        }
    }
}
