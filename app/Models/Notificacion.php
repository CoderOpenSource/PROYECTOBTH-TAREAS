<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;
    protected $table = 'notificaciones';
    protected $fillable = [
        'titulo',
        'mensaje',
        'es_automatica',
        'curso_id',
        'profesor_id',
    ];


    // Relación con el curso al que pertenece la notificación
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Relación con el profesor que envía la notificación
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id');
    }

    // Relación con los estudiantes que reciben la notificación
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'notificacion_usuario')
            ->withPivot('leido')
            ->withTimestamps();
    }
}
