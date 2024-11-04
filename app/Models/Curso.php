<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación con los estudiantes que pertenecen al curso
    public function estudiantes()
    {
        return $this->hasMany(Usuario::class);
    }

    // Relación con las tareas asignadas al curso
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}
