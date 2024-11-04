<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EstudianteController extends Controller
{
    // Método para ver todos los estudiantes con paginación y filtros
    public function index(Request $request)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $cursos = Curso::all();

        $query = Usuario::where('rol', 'estudiante');

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        $estudiantes = $query->paginate(10);

        return view('admin.estudiantes.index', compact('estudiantes', 'cursos'));
    }

    // Método para guardar un nuevo estudiante con curso seleccionado
    public function store2(Request $request)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|integer|unique:usuarios',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $nombre = strtolower(str_replace(' ', '', $request->nombre));
        $correo = $nombre . '@juancitopinto.com';

        Usuario::create([
            'nombre' => $request->nombre,
            'ci' => $request->ci,
            'correo' => $correo,
            'contraseña' => Hash::make($request->ci),
            'rol' => 'estudiante',
            'fecha_registro' => now(),
            'curso_id' => $request->curso_id,
        ]);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado exitosamente.');
    }

    // Método para actualizar un estudiante con curso
    public function update2(Request $request, Usuario $estudiante)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|integer|unique:usuarios,ci,' . $estudiante->id,
            'curso_id' => 'required|exists:cursos,id',
        ]);

        if ($estudiante->rol === 'estudiante') {
            $estudiante->update([
                'nombre' => $request->nombre,
                'ci' => $request->ci,
                'curso_id' => $request->curso_id,
            ]);
        } else {
            return redirect()->back()->withErrors(['El usuario no es un estudiante.']);
        }

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado exitosamente.');
    }

    // Método para eliminar un estudiante individual
    public function destroy2(Usuario $estudiante)
    {
        if ($estudiante->rol === 'estudiante') {
            $estudiante->delete();
            return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado exitosamente.');
        } else {
            return redirect()->back()->withErrors(['El usuario no es un estudiante.']);
        }
    }

    // Método para ver los estudiantes de un curso específico
    public function estudiantes(Curso $curso)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Obtener los estudiantes del curso
        $estudiantes = Usuario::where('curso_id', $curso->id)
            ->where('rol', 'estudiante')
            ->get();

        // Enviar la lista de estudiantes a la vista
        return view('admin.cursos.estudiantes', compact('curso', 'estudiantes'));
    }

    // Método para guardar un nuevo estudiante
    public function store(Request $request, Curso $curso)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|integer|unique:usuarios',
        ]);

        // Generar el correo y la contraseña basados en el CI
        $nombre = strtolower(str_replace(' ', '', $request->nombre));
        $correo = $nombre . '@juancitopinto.com';

        Usuario::create([
            'nombre' => $request->nombre,
            'ci' => $request->ci,
            'correo' => $correo,
            'contraseña' => Hash::make($request->ci),
            'rol' => 'estudiante',
            'fecha_registro' => now(),
            'curso_id' => $curso->id,
        ]);

        return redirect()->route('cursos.estudiantes', $curso->id)->with('success', 'Estudiante creado exitosamente.');
    }

    // Método para mostrar el formulario de edición de un estudiante
    public function edit(Usuario $estudiante)
    {
        // Verificar si el rol es estudiante antes de enviar a la vista de edición
        if ($estudiante->rol !== 'estudiante') {
            return redirect()->back()->withErrors(['El usuario no es un estudiante.']);
        }

        return view('admin.estudiantes.edit', compact('estudiante'));
    }

    // Método para actualizar un estudiante
    public function update(Request $request, Usuario $estudiante)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|integer|unique:usuarios,ci,' . $estudiante->id,
        ]);

        // Actualizar solo si el usuario tiene rol de estudiante
        if ($estudiante->rol === 'estudiante') {
            $estudiante->update($request->only('nombre', 'ci'));
        } else {
            return redirect()->back()->withErrors(['El usuario no es un estudiante.']);
        }

        return redirect()->route('cursos.estudiantes', $estudiante->curso_id)->with('success', 'Estudiante actualizado exitosamente.');
    }

    // Método para eliminar un estudiante
    public function destroy(Usuario $estudiante)
    {
        // Verificar si el usuario es un estudiante
        if ($estudiante->rol === 'estudiante') {
            $curso_id = $estudiante->curso_id;
            $estudiante->delete();
            return redirect()->route('cursos.estudiantes', $curso_id)->with('success', 'Estudiante eliminado exitosamente.');
        } else {
            return redirect()->back()->withErrors(['El usuario no es un estudiante.']);
        }
    }
}
