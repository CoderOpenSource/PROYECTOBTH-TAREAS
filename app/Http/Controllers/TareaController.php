<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Curso;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TareaController extends Controller
{
    // Mostrar una lista de todas las tareas
    public function index(Request $request)
    {
        $rol = session('rol');
        $usuario_id = session('usuario_id');
        $curso_id = session('curso_id');

        // Iniciar la consulta base de tareas con relaciones necesarias
        $query = Tarea::with('curso', 'profesor');

        // Filtrar tareas según el rol
        if ($rol === 'administrador') {
            // Sin filtro adicional para administrador, ya tiene acceso a todas las tareas
        } elseif ($rol === 'profesor') {
            $query->where('profesor_id', $usuario_id);
        } elseif ($rol === 'estudiante') {
            $query->where('curso_id', $curso_id);
        } else {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Aplicar filtro de curso si está presente en la solicitud
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        // Aplicar filtro de fecha desde
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_entrega', '>=', $request->fecha_desde);
        }

        // Aplicar filtro de fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_entrega', '<=', $request->fecha_hasta);
        }

        // Ejecutar la consulta
        $tareas = $query->get();

        // Obtener todos los cursos para el filtro en la vista
        $cursos = Curso::all();

        return view('admin.tareas.index', compact('tareas', 'cursos'));
    }

    // Mostrar el formulario de creación de una nueva tarea
    public function create()
    {
        $cursos = Curso::all();
        return view('admin.tareas.create', compact('cursos'));
    }

    // Almacenar una nueva tarea en la base de datos
    public function store(Request $request)
    {
        // Verificar que el rol en sesión sea "profesor"
        if (session('rol') !== 'profesor') {
            return redirect()->route('tareas.index')->withErrors(['error' => 'Solo los profesores pueden crear tareas.']);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
            'curso_id' => 'required|exists:cursos,id',
            'recurso' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,png',
        ]);

        $data = $request->only(['titulo', 'descripcion', 'fecha_entrega', 'curso_id']);
        $data['profesor_id'] = session('usuario_id');

        // Manejo de archivo adjunto en Cloudinary
        if ($request->hasFile('recurso')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('recurso')->getRealPath())->getSecurePath();
            $data['recurso'] = $uploadedFileUrl;
        }

        Tarea::create($data);

        return redirect()->route('tareas.index')->with('success', 'Tarea creada exitosamente.');
    }

    // Mostrar el formulario para editar una tarea existente
    public function edit(Tarea $tarea)
    {
        $cursos = Curso::all();
        return view('admin.tareas.edit', compact('tarea', 'cursos'));
    }

    // Actualizar una tarea existente en la base de datos
    public function update(Request $request, Tarea $tarea)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
            'curso_id' => 'required|exists:cursos,id',
            'recurso' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,png',
        ]);

        $data = $request->only(['titulo', 'descripcion', 'fecha_entrega', 'curso_id']);

        // Manejo de archivo adjunto en Cloudinary
        if ($request->hasFile('recurso')) {
            if ($tarea->recurso) {
                Cloudinary::destroy($tarea->recurso);
            }
            $uploadedFileUrl = Cloudinary::upload($request->file('recurso')->getRealPath())->getSecurePath();
            $data['recurso'] = $uploadedFileUrl;
        }

        $tarea->update($data);

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    // Eliminar una tarea existente de la base de datos
    public function destroy(Tarea $tarea)
    {
        if ($tarea->recurso) {
            Cloudinary::destroy($tarea->recurso);
        }

        $tarea->delete();

        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada exitosamente.');
    }
    public function getTareasByCurso(Request $request)
    {
        $curso_id = $request->query('curso_id');
        $profesor_id = $request->query('profesor_id'); // Obtenemos el profesor_id de los parámetros de la consulta

        // Filtramos las tareas por curso y profesor
        $tareas = Tarea::where('curso_id', $curso_id)
            ->where('profesor_id', $profesor_id)
            ->get();

        return response()->json($tareas);
    }


}
