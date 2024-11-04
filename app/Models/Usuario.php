<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    // Especificar la tabla asociada
    protected $table = 'usuarios';

    // Atributos que son asignables en masa
    protected $fillable = [
        'nombre', 'correo', 'contraseña', 'rol', 'fecha_registro', 'materia', 'curso_id', 'ci'
    ];

    // Especificar los atributos ocultos, como la contraseña
    protected $hidden = [
        'contraseña',
    ];

    // Relación con el curso al que pertenece (solo estudiantes)
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Relación para las tareas asignadas (solo profesores)
    public function tareasAsignadas()
    {
        return $this->hasMany(Tarea::class, 'profesor_id');
    }

    // Relación para las calificaciones dadas (solo profesores)
    public function calificacionesDadas()
    {
        return $this->hasMany(Calificacion::class, 'profesor_id');
    }

    // Relación para las calificaciones recibidas (solo estudiantes)
    public function calificacionesRecibidas()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id');
    }

    // Relación para obtener las notificaciones del usuario
    public function notificaciones()
    {
        return $this->belongsToMany(Notificacion::class, 'notificacion_usuario')
            ->withPivot('leido')
            ->withTimestamps();
    }

    // Método para contar las notificaciones no leídas
    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->wherePivot('leido', false)->count();
    }

    // Accesor para obtener el campo 'correo' como el atributo 'email'
    public function getEmailAttribute()
    {
        return $this->attributes['correo'];
    }

    // Mutador para definir el campo 'correo'
    public function setEmailAttribute($value)
    {
        $this->attributes['correo'] = $value;
    }

    // Mutador para encriptar la contraseña al establecerla
    public function setPasswordAttribute($value)
    {
        $this->attributes['contraseña'] = bcrypt($value);
    }
}
