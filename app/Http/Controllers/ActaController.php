<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Curso;
use Illuminate\Http\Request;

class ActaController extends Controller
{
    public function index()
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor' && session('rol') !== 'padre') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }
        $cursos = Curso::all();
        // Obtener todas las actas
        $actas = Acta::with('curso')->get();
        return view('admin.actas.index', compact('actas', 'cursos'));
    }

    public function create()
    {
        // Obtener la lista de cursos para asignar a un acta
        $cursos = Curso::all();
        return view('admin.actas.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Validar los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        // Verificar si ya existe una acta para el curso
        $actaExistente = Acta::where('curso_id', $request->curso_id)->first();
        if ($actaExistente) {
            return redirect()->route('actas.index')->withErrors(['error' => 'Ya existe una acta para este curso.']);
        }

        // Crear el acta
        Acta::create([
            'titulo' => $request->titulo,
            'curso_id' => $request->curso_id,
        ]);

        return redirect()->route('actas.index')->with('success', 'Acta creada exitosamente.');
    }


    public function edit(Acta $acta)
    {
        $cursos = Curso::all(); // Obtener los cursos para seleccionar al editar
        return view('admin.actas.edit', compact('acta', 'cursos'));
    }

    public function update(Request $request, Acta $acta)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Validar los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        // Actualizar el acta
        $acta->update([
            'titulo' => $request->titulo,
            'curso_id' => $request->curso_id,
        ]);

        return redirect()->route('actas.index')->with('success', 'Acta actualizada exitosamente.');
    }

    public function destroy(Acta $acta)
    {
        // Verificar si el usuario tiene el rol de administrador o profesor
        if (session('rol') !== 'administrador' && session('rol') !== 'profesor') {
            return redirect('/login')->withErrors(['No tienes acceso a esta área.']);
        }

        // Eliminar el acta
        $acta->delete();
        return redirect()->route('actas.index')->with('success', 'Acta eliminada exitosamente.');
    }

}
