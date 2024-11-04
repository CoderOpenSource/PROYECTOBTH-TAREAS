<?php

namespace App\Http\Controllers;

use App\Models\Comportamiento;
use App\Models\Acta;
use App\Models\Estudiante;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ComportamientoController extends Controller
{
    public function index(Request $request, Acta $acta)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor' && session('rol') !== 'padre') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Definir lista de faltas
        $faltas = [
            'A) No trabajó en clases',
            'B) No presentó tareas',
            'C) No trajo material escolar',
            'D) No trajo agenda',
            'E) No participó en clases',
            'F) Llegó tarde a clases',
            'G) No dio la evaluación',
            'H) Faltó sin licencia',
            'I) Faltó con licencia',
            'J) Usó el celular en horas de clase (sin autorización)',
            'K) Indisciplina en el aula',
            'L) Molestó a sus compañer@s',
            'M) Faltó el respeto a sus compañeros',
            'N) Faltó el respeto a sus profesores',
            'O) Otros'
        ];

        // Obtener los estudiantes que pertenecen al curso del acta
        $estudiantes = Estudiante::where('curso_id', $acta->curso_id)->get();

        // Aplicar filtros si se proporcionan
        $query = Comportamiento::where('acta_id', $acta->id)->with('estudiante');

        // Filtro por nombre de estudiante
        if ($request->filled('nombre')) {
            $query->whereHas('estudiante', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por fecha desde
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Obtener los comportamientos filtrados
        $comportamientos = $query->get();

        return view('admin.actas.comportamientos', compact('acta', 'comportamientos', 'estudiantes', 'faltas'));
    }

    // Crear un nuevo comportamiento
    public function store(Request $request, Acta $acta)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'tipo' => 'required|string',
            'estudiante_id' => 'required|exists:estudiantes,id',
        ]);

        // Verificar que el estudiante pertenezca al curso del acta
        $estudiante = Estudiante::where('id', $request->estudiante_id)
            ->where('curso_id', $acta->curso_id)
            ->first();

        if (!$estudiante) {
            return redirect()->back()->withErrors(['error' => 'El estudiante no pertenece al curso de esta acta.']);
        }

        // Obtener el ID del profesor desde la sesión
        $profesorId = session('usuario_id');

        // Obtener los datos del profesor (nombre y materia)
        $profesor = Usuario::findOrFail($profesorId);
        $nombreProfesor = $profesor->nombre;
        $materiaProfesor = $profesor->materia;

        // Crear el comportamiento con la fecha actual y el campo profesor
        Comportamiento::create([
            'descripcion' => $request->descripcion,
            'fecha' => now(),  // Generar la fecha actual automáticamente
            'tipo' => $request->tipo,
            'acta_id' => $acta->id,
            'estudiante_id' => $request->estudiante_id,
            'profesor' => $nombreProfesor . ' - ' . $materiaProfesor, // Guardar el nombre y materia concatenados
        ]);

        return redirect()->route('comportamientos.index', $acta->id)->with('success', 'Comportamiento añadido exitosamente.');
    }

    // Editar un comportamiento
    public function edit(Acta $acta, $comportamiento_id)
    {
        // Buscar el comportamiento usando su 'id'
        $comportamiento = Comportamiento::where('acta_id', $acta->id)->where('id', $comportamiento_id)->firstOrFail();

        // Obtener el ID del profesor desde la sesión
        $profesorId = session('usuario_id');

        // Obtener los datos del profesor (nombre y materia)
        $profesor = Usuario::findOrFail($profesorId);
        $nombreProfesor = $profesor->nombre;
        $materiaProfesor = $profesor->materia;

        // Verificar si el profesor actual es el que creó el comportamiento
        if ($comportamiento->profesor !== $nombreProfesor . ' - ' . $materiaProfesor) {
            return redirect()->route('comportamientos.index', $acta->id)->withErrors(['error' => 'No puedes editar este comportamiento porque pertenece a otro profesor.']);
        }

        // Obtener estudiantes que pertenecen al mismo curso que el acta
        $estudiantes = Estudiante::where('curso_id', $acta->curso_id)->get();

        return view('admin.comportamientos.edit', compact('acta', 'comportamiento', 'estudiantes'));
    }

    // Actualizar un comportamiento
    public function update(Request $request, Acta $acta, $comportamiento_id)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'tipo' => 'required|string',
            'estudiante_id' => 'required|exists:estudiantes,id',
        ]);

        // Buscar el comportamiento usando su 'id'
        $comportamiento = Comportamiento::where('acta_id', $acta->id)->where('id', $comportamiento_id)->firstOrFail();

        // Obtener el ID del profesor desde la sesión
        $profesorId = session('usuario_id');

        // Obtener los datos del profesor (nombre y materia)
        $profesor = Usuario::findOrFail($profesorId);
        $nombreProfesor = $profesor->nombre;
        $materiaProfesor = $profesor->materia;

        // Verificar si el profesor actual es el que creó el comportamiento
        if ($comportamiento->profesor !== $nombreProfesor . ' - ' . $materiaProfesor) {
            return redirect()->route('comportamientos.index', $acta->id)->withErrors(['error' => 'No puedes editar este comportamiento porque pertenece a otro profesor.']);
        }

        // Actualizar el comportamiento
        $comportamiento->update([
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'estudiante_id' => $request->estudiante_id,
        ]);

        return redirect()->route('comportamientos.index', $acta->id)->with('success', 'Comportamiento actualizado exitosamente.');
    }

    // Eliminar un comportamiento
    public function destroy(Acta $acta, $comportamiento_id)
    {
        // Buscar el comportamiento usando su 'id'
        $comportamiento = Comportamiento::where('acta_id', $acta->id)->where('id', $comportamiento_id)->firstOrFail();

        // Obtener el ID del profesor desde la sesión
        $profesorId = session('usuario_id');

        // Obtener los datos del profesor (nombre y materia)
        $profesor = Usuario::findOrFail($profesorId);
        $nombreProfesor = $profesor->nombre;
        $materiaProfesor = $profesor->materia;

        // Verificar si el profesor actual es el que creó el comportamiento
        if ($comportamiento->profesor !== $nombreProfesor . ' - ' . $materiaProfesor) {
            return redirect()->route('comportamientos.index', $acta->id)->withErrors(['error' => 'No puedes eliminar este comportamiento porque pertenece a otro profesor.']);
        }

        // Eliminar el comportamiento
        $comportamiento->delete();

        return redirect()->route('comportamientos.index', $acta->id)->with('success', 'Comportamiento eliminado exitosamente.');
    }
}
