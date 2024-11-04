<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Tarea;
use App\Models\Usuario;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $rol = session('rol');
        $usuario_id = session('usuario_id');
        $curso_id = session('curso_id');

        // Iniciar la consulta base de calificaciones con relaciones necesarias
        $query = Calificacion::with('tarea', 'estudiante', 'profesor');

        // Filtros generales
        if ($request->filled('curso_id')) {
            $query->whereHas('tarea', function ($q) use ($request) {
                $q->where('curso_id', $request->curso_id);
            });
        }

        if ($request->filled('tarea_id')) {
            $query->where('tarea_id', $request->tarea_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtrar calificaciones según el rol
        if ($rol === 'administrador') {
            // Administrador ve todas las calificaciones
        } elseif ($rol === 'profesor') {
            // Profesor ve solo las calificaciones de sus tareas
            $query->where('profesor_id', $usuario_id);
        } elseif ($rol === 'estudiante') {
            // Estudiante ve solo sus propias calificaciones y puede filtrar por materia
            $query->where('estudiante_id', $usuario_id);

            if ($request->filled('materia')) {
                $query->whereHas('profesor', function ($q) use ($request) {
                    $q->where('materia', $request->materia);
                });
            }
        } else {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Ejecutar la consulta
        $calificaciones = $query->get();

        // Obtener datos adicionales para los filtros
        $cursos = Curso::all();
        $tareas = $request->filled('curso_id') ? Tarea::where('curso_id', $request->curso_id)->get() : Tarea::all();
        $estudiantes = $request->filled('curso_id')
            ? Usuario::where('rol', 'estudiante')->where('curso_id', $request->curso_id)->get()
            : Usuario::where('rol', 'estudiante')->get();
        $materias = Usuario::where('rol', 'profesor')->pluck('materia')->unique();

        return view('admin.calificaciones.index', compact('calificaciones', 'cursos', 'tareas', 'estudiantes', 'materias'));
    }




    // Mostrar el formulario de creación de una nueva calificación
    public function create()
    {
        $cursos = Curso::all();
        return view('admin.calificaciones.create', compact('cursos'));
    }


    // Almacenar una nueva calificación en la base de datos
    public function store(Request $request)
    {
        // Verificar que el rol en sesión sea "profesor"
        if (session('rol') !== 'profesor') {
            return redirect()->route('calificaciones.index')->withErrors(['error' => 'Solo los profesores pueden crear calificaciones.']);
        }
        $request->validate([
            'tarea_id' => 'required|exists:tareas,id',
            'estudiante_id' => 'required|exists:usuarios,id',
            'calificacion' => 'required|numeric|min:0|max:100',
            'comentarios' => 'nullable|string',
        ]);

        $data = $request->only(['tarea_id', 'estudiante_id', 'calificacion', 'comentarios']);
        $data['profesor_id'] = session('usuario_id');
        // Verificar el contenido de $data


        Calificacion::create($data);

        return redirect()->route('calificaciones.index')->with('success', 'Calificación creada exitosamente.');
    }

    // Mostrar el formulario para editar una calificación existente
    public function edit(Calificacion $calificacion)
    {
        $cursos = Curso::all();
        return view('admin.calificaciones.edit', compact('calificacion', 'cursos'));
    }

    // Actualizar una calificación existente en la base de datos
    public function update(Request $request, Calificacion $calificacion)
    {
        $request->validate([
            'tarea_id' => 'required|exists:tareas,id',
            'estudiante_id' => 'required|exists:usuarios,id',
            'calificacion' => 'required|numeric|min:0|max:100',
            'comentarios' => 'nullable|string',
        ]);

        $data = $request->only(['tarea_id', 'estudiante_id', 'calificacion', 'comentarios']);
        $calificacion->update($data);

        return redirect()->route('calificaciones.index')->with('success', 'Calificación actualizada exitosamente.');
    }

    // Eliminar una calificación existente de la base de datos
    public function destroy(Calificacion $calificacion)
    {
        $calificacion->delete();

        return redirect()->route('calificaciones.index')->with('success', 'Calificación eliminada exitosamente.');
    }
}
