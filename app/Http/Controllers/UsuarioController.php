<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProfesoresImport;

class UsuarioController extends Controller
{
    public function index()
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $materias = [
            'MATEMÁTICAS', 'CIENCIAS', 'HISTORIA', 'LENGUAJE', 'INGLÉS',
            'ARTES PLÁSTICAS', 'EDUCACIÓN FÍSICA', 'FÍSICA', 'QUÍMICA',
            'RELIGIÓN', 'MÚSICA', 'PSICOLOGÍA', 'FILOSOFÍA'
        ];

        $profesores = Usuario::where('rol', 'profesor')->get();

        return view('admin.profesores.index', compact('profesores', 'materias'));
    }

    public function store(Request $request)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|string|unique:usuarios,ci',
            'materia' => 'required|string|max:255',
        ]);

        $correo = strtolower(str_replace(' ', '', $request->nombre)) . '@juancitopinto.com';

        Usuario::create([
            'nombre' => $request->nombre,
            'correo' => $correo,
            'contraseña' => Hash::make($request->ci),
            'rol' => 'profesor',
            'ci' => $request->ci,
            'materia' => $request->materia,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('profesores.index')->with('success', 'Profesor creado exitosamente.');
    }

    public function update(Request $request, Usuario $profesor)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|string|unique:usuarios,ci,' . $profesor->id,
            'materia' => 'required|string|max:255',
        ]);

        $correo = strtolower(str_replace(' ', '', $request->nombre)) . '@juancitopinto.com';

        $profesor->update([
            'nombre' => $request->nombre,
            'correo' => $correo,
            'ci' => $request->ci,
            'materia' => $request->materia,
        ]);

        return redirect()->route('profesores.index')->with('success', 'Profesor actualizado exitosamente.');
    }

    public function destroy(Usuario $profesor)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $profesor->delete();
        return redirect()->route('profesores.index')->with('success', 'Profesor eliminado exitosamente.');
    }

    public function import(Request $request)
    {
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProfesoresImport, $request->file('file'));

        return redirect()->route('profesores.index')->with('success', 'Profesores importados exitosamente.');
    }
    public function getEstudiantesByCurso(Request $request)
    {
        $curso_id = $request->query('curso_id');
        $estudiantes = Usuario::where('curso_id', $curso_id)->where('rol', 'estudiante')->get();
        return response()->json($estudiantes);
    }

}
