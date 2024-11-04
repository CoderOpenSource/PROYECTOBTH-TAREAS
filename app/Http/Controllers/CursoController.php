<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\Imports\EstudiantesImport;
use Maatwebsite\Excel\Facades\Excel;
class CursoController extends Controller
{
    public function index()
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Obtener todos los cursos (ya no necesitamos cargar el usuario)
        $cursos = Curso::all(); // Obtener todos los cursos
        return view('admin.cursos.index', compact('cursos'));
    }

    public function store(Request $request)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Validar datos del curso y archivo
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'file' => 'required|mimes:xlsx,xls'
        ]);

        // Crear curso
        $curso = Curso::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // Importar estudiantes
        Excel::import(new EstudiantesImport($curso->id), $request->file('file'));

        return redirect()->route('cursos.index')->with('success', 'Curso y estudiantes creados exitosamente.');
    }

    public function update(Request $request, Curso $curso)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        // Actualizar el curso
        $curso->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('cursos.index')->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroy(Curso $curso)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Eliminar el curso
        $curso->delete();
        return redirect()->route('cursos.index')->with('success', 'Curso eliminado exitosamente.');
    }
    // Método para ver estudiantes de un curso
    public function estudiantes(Curso $curso)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Obtener los estudiantes del curso
        $estudiantes = Estudiante::where('curso_id', $curso->id)->get();

        return view('admin.cursos.estudiantes', compact('curso', 'estudiantes'));
    }
}
