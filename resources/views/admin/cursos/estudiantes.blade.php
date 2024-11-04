@extends('layouts.app')

@section('title', 'Estudiantes del Curso ' . $curso->nombre)
@section('panel_title', 'Panel de Administración')

@section('sidebar')
    <li class="nav-item">
        <a href="/dashboard" class="nav-link">Dashboard</a>
    </li>
    @if(session('rol') === 'administrador')
        <li class="nav-item">
            <a href="/profesores" class="nav-link">Gestionar Profesores</a>
        </li>
    @endif
    <li class="nav-item">
        <a href="/cursos" class="nav-link">Gestionar Cursos</a>
    </li>
    @if(session('rol') === 'administrador' || session('rol') === 'profesor')
        <li class="nav-item">
            <a href="/estudiantes" class="nav-link">Gestionar Estudiantes</a>
        </li>
    @endif
    @if(session('rol') === 'administrador' || session('rol') === 'profesor' || session('rol') === 'profesor')
        <li class="nav-item">
            <a href="/tareas" class="nav-link">Gestionar Tareas</a>
        </li>
    @endif
    <li class="nav-item">
        <a href="/calificaciones" class="nav-link">Gestionar Calificaciones</a>
    </li>
    <li class="nav-item">
        <a href="/notificaciones" class="nav-link">Gestionar Notificaciones</a>
    </li>
    <li class="nav-item">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Cerrar Sesión
        </a>
    </li>
@endsection

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Estudiantes del Curso: {{ $curso->nombre }}</h2>
        <button class="btn mb-3 text-white" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Estudiante</button>

        <!-- Mostrar mensaje de éxito en un modal -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Estudiantes -->
        <table class="table table-striped">
            <thead class="table" style="background-color: #003366;">
            <tr>
                <th class="text-white">Cédula de Identidad (CI)</th>
                <th class="text-white">Nombre</th>
                <th class="text-white">Correo Electrónico</th>
                <th class="text-white">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($estudiantes as $estudiante)
                <tr>
                    <td>{{ $estudiante->ci }}</td>
                    <td>{{ $estudiante->nombre }}</td>
                    <td>{{ $estudiante->correo }}</td>
                    <td>
                        <!-- Botón para editar estudiante -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $estudiante->id }}">Editar</button>
                        @if(session('rol') === 'administrador')
                            <!-- Formulario para eliminar estudiante -->
                            <form action="{{ route('estudiantes.destroy', $estudiante->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para Crear Estudiante -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('estudiantes.store', $curso->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="ci" class="form-label">Cédula de Identidad (CI)</label>
                            <input type="number" class="form-control" id="ci" name="ci" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales para Editar Estudiante -->
    @foreach($estudiantes as $estudiante)
        <div class="modal fade" id="editModal-{{ $estudiante->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Estudiante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('estudiantes.update', $estudiante->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $estudiante->nombre }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="ci" class="form-label">Cédula de Identidad (CI)</label>
                                <input type="number" class="form-control" id="ci" name="ci" value="{{ $estudiante->ci }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="{{ $estudiante->correo }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
