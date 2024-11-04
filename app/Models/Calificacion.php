<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    // Especifica el nombre de la tabla
    protected $table = 'calificaciones';

    protected $fillable = [
        'tarea_id',
        'estudiante_id',
        'calificacion',
        'comentarios',
        'profesor_id',
    ];

    // Relación con la tarea a la que pertenece la calificación
    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    // Relación con el estudiante que recibe la calificación
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }

    // Relación con el profesor que da la calificación
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id');
    }
}
