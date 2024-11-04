<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Curso;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $rol = session('rol');
        $usuario_id = session('usuario_id');
        $curso_id = session('curso_id');

        // Iniciar la consulta base de notificaciones con relaciones necesarias
        $query = Notificacion::with('curso', 'profesor');

        // Filtrar notificaciones según el rol
        if ($rol === 'administrador') {
            // Administrador ve todas las notificaciones
        } elseif ($rol === 'profesor') {
            // Profesor ve solo sus propias notificaciones
            $query->where('profesor_id', $usuario_id);
        } elseif ($rol === 'estudiante') {
            // Estudiante ve solo las notificaciones de su curso y relacionadas con su usuario
            $query->where('curso_id', $curso_id)
                ->whereHas('usuarios', function ($q) use ($usuario_id) {
                    $q->where('usuario_id', $usuario_id);
                });

            // Obtener las materias relacionadas con las notificaciones para el filtro
            $materias = Notificacion::where('curso_id', $curso_id)
                ->whereHas('profesor', function ($q) {
                    $q->whereNotNull('materia');
                })
                ->get()
                ->pluck('profesor.materia')
                ->unique()
                ->values();

            // Marcar todas las notificaciones no leídas del estudiante como leídas
            \DB::table('notificacion_usuario')
                ->where('usuario_id', $usuario_id)
                ->where('leido', false)
                ->update(['leido' => true]);

            // Aplicar filtro de materia si está seleccionado
            if ($request->filled('materia')) {
                $query->whereHas('profesor', function ($q) use ($request) {
                    $q->where('materia', $request->materia);
                });
            }

            // Paginar los resultados y retornar la vista para estudiante
            $notificaciones = $query->paginate(10);
            $cursos = Curso::all();

            return view('admin.notificaciones.index', compact('notificaciones', 'materias', 'cursos'));
        } else {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Aplicar filtro de curso y título para administrador y profesor
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . $request->titulo . '%');
        }

        // Paginar los resultados para administrador y profesor
        $notificaciones = $query->paginate(10);
        $cursos = Curso::all();

        return view('admin.notificaciones.index', compact('notificaciones', 'cursos'));
    }



    // Mostrar el formulario de creación de una nueva notificación
    public function create()
    {
        $cursos = Curso::all();
        return view('admin.notificaciones.create', compact('cursos'));
    }

    // Almacenar una nueva notificación en la base de datos
    public function store(Request $request)
    {
        // Verificar que el rol en sesión sea "profesor"
        if (session('rol') !== 'profesor') {
            return redirect()->route('notificaciones.index')->withErrors(['error' => 'Solo los profesores pueden crear notificaciones.']);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $data = $request->only(['titulo', 'mensaje', 'curso_id']);
        $data['profesor_id'] = session('usuario_id');
        $data['es_automatica'] = false;

        // Crear la notificación
        $notificacion = Notificacion::create($data);

        // Asociar la notificación a cada estudiante en el curso
        $curso = Curso::find($data['curso_id']);
        foreach ($curso->estudiantes as $estudiante) {
            $notificacion->usuarios()->attach($estudiante->id, ['leido' => false]);
        }

        return redirect()->route('notificaciones.index')->with('success', 'Notificación creada exitosamente.');
    }

    // Mostrar el formulario para editar una notificación existente
    public function edit(Notificacion $notificacion)
    {
        $cursos = Curso::all();
        return view('admin.notificaciones.edit', compact('notificacion', 'cursos'));
    }

    // Actualizar una notificación existente en la base de datos
    public function update(Request $request, Notificacion $notificacion)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $data = $request->only(['titulo', 'mensaje', 'curso_id']);
        $notificacion->update($data);

        return redirect()->route('notificaciones.index')->with('success', 'Notificación actualizada exitosamente.');
    }

    // Eliminar una notificación existente de la base de datos
    public function destroy(Notificacion $notificacion)
    {
        $notificacion->usuarios()->detach(); // Desvincular notificación de usuarios
        $notificacion->delete();

        return redirect()->route('notificaciones.index')->with('success', 'Notificación eliminada exitosamente.');
    }
}
