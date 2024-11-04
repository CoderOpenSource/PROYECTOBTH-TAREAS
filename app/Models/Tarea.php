<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_entrega',
        'recurso',
        'curso_id',
        'profesor_id',
    ];

    // Relación con el curso al que pertenece la tarea
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Relación con el profesor que asignó la tarea
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id');
    }

    // Relación con las calificaciones asociadas a la tarea
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }
}
