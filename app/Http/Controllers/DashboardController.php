<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $rol = session('rol');
        $usuario_id = session('usuario_id');

        // Verifica si el estudiante tiene notificaciones no leídas
        $notificacionesNoLeidas = 0;
        if ($rol === 'estudiante') {
            $notificacionesNoLeidas = Usuario::find($usuario_id)
                ->notificaciones()
                ->wherePivot('leido', false)
                ->count();
        }

        return view('admin.dashboard', compact('notificacionesNoLeidas'));
    }
}
