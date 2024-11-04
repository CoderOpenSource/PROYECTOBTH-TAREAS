<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionUsuario extends Model
{
    protected $table = 'notificacion_usuario';

    protected $fillable = [
        'notificacion_id',
        'usuario_id',
        'leido',
    ];
}
