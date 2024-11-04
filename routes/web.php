<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
// routes/web.php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\ComportamientoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\NotificacionController;
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// Grupo de rutas para gestionar profesores
Route::get('/profesores', [UsuarioController::class, 'index'])->name('profesores.index');
Route::post('/profesores', [UsuarioController::class, 'store'])->name('profesores.store');
Route::put('/profesores/{profesor}', [UsuarioController::class, 'update'])->name('profesores.update');
Route::delete('/profesores/{profesor}', [UsuarioController::class, 'destroy'])->name('profesores.destroy');
Route::post('/profesores/import', [UsuarioController::class, 'import'])->name('profesores.import');

// Grupo de rutas para gestionar cursos
Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');

// Grupo de rutas para gestionar estudiantes
Route::get('/cursos/{curso}/estudiantes', [EstudianteController::class, 'estudiantes'])->name('cursos.estudiantes');
Route::post('/cursos/{curso}/estudiantes', [EstudianteController::class, 'store'])->name('estudiantes.store');
Route::get('/estudiantes/{estudiante}/edit', [EstudianteController::class, 'edit'])->name('estudiantes.edit');
Route::put('/estudiantes/{estudiante}', [EstudianteController::class, 'update'])->name('estudiantes.update');
Route::delete('/estudiantes/{estudiante}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
// Nueva ruta para listar todos los estudiantes
Route::get('/estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');

// Rutas adicionales para crear, actualizar y eliminar estudiantes con curso
Route::post('/estudiantes', [EstudianteController::class, 'store2'])->name('estudiantes.store2');
Route::put('/estudiantes/{estudiante}/update2', [EstudianteController::class, 'update2'])->name('estudiantes.update2');
Route::delete('/estudiantes/{estudiante}/destroy2', [EstudianteController::class, 'destroy2'])->name('estudiantes.destroy2');

// Rutas para gestionar tareas
Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
Route::get('/tareas/create', [TareaController::class, 'create'])->name('tareas.create');
Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
Route::get('/tareas/{tarea}/edit', [TareaController::class, 'edit'])->name('tareas.edit');
Route::put('/tareas/{tarea}', [TareaController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');

// Rutas para gestionar notificaciones
Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
Route::get('/notificaciones/create', [NotificacionController::class, 'create'])->name('notificaciones.create');
Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('notificaciones.store');
Route::get('/notificaciones/{notificacion}/edit', [NotificacionController::class, 'edit'])->name('notificaciones.edit');
Route::put('/notificaciones/{notificacion}', [NotificacionController::class, 'update'])->name('notificaciones.update');
Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy'])->name('notificaciones.destroy');

// Rutas para gestionar calificaciones
Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
Route::get('/calificaciones/create', [CalificacionController::class, 'create'])->name('calificaciones.create');
Route::post('/calificaciones', [CalificacionController::class, 'store'])->name('calificaciones.store');
Route::get('/calificaciones/{calificacion}/edit', [CalificacionController::class, 'edit'])->name('calificaciones.edit');
Route::put('/calificaciones/{calificacion}', [CalificacionController::class, 'update'])->name('calificaciones.update');
Route::delete('/calificaciones/{calificacion}', [CalificacionController::class, 'destroy'])->name('calificaciones.destroy');
Route::get('/api/tareas', [TareaController::class, 'getTareasByCurso']);
Route::get('/api/estudiantes', [UsuarioController::class, 'getEstudiantesByCurso']);

// Rutas para gestionar actas
Route::get('/actas', [ActaController::class, 'index'])->name('actas.index');
Route::get('/actas/create', [ActaController::class, 'create'])->name('actas.create');
Route::post('/actas', [ActaController::class, 'store'])->name('actas.store');
Route::get('/actas/{acta}/edit', [ActaController::class, 'edit'])->name('actas.edit');
Route::put('/actas/{acta}', [ActaController::class, 'update'])->name('actas.update');
Route::delete('/actas/{acta}', [ActaController::class, 'destroy'])->name('actas.destroy');

Route::get('/actas/{acta}/comportamientos', [ComportamientoController::class, 'index'])->name('comportamientos.index');
Route::post('/actas/{acta}/comportamientos', [ComportamientoController::class, 'store'])->name('comportamientos.store');
Route::get('/actas/{acta}/comportamientos/{comportamiento_id}/edit', [ComportamientoController::class, 'edit'])->name('comportamientos.edit');
Route::put('/actas/{acta}/comportamientos/{comportamiento_id}', [ComportamientoController::class, 'update'])->name('comportamientos.update');
Route::delete('/actas/{acta}/comportamientos/{comportamiento_id}', [ComportamientoController::class, 'destroy'])->name('comportamientos.destroy');
