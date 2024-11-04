@extends('layouts.app')

@section('title', 'Gestionar Cursos')
@section('panel_title', 'Panel de Administración')

@section('sidebar')
    <li class="nav-item">
        <a href="/dashboard" class="nav-link">Menú</a>
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
        <h2 class="mb-4">Lista de Cursos</h2>
        @if(session('rol') === 'administrador')
        <button class="btn mb-3 text-white" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Curso</button>
        @endif
        <!-- Mostrar mensaje de éxito en un modal -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Cursos -->
        <table class="table table-striped">
            <thead class="table" style="background-color: #003366;">
            <tr>
                <th class="text-white">Nombre</th>
                <th class="text-white">Descripción</th>
                <th class="text-white">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->nombre }}</td>
                    <td>{{ $curso->descripcion }}</td>
                    <td>
                        <!-- Botón para ver estudiantes -->
                        <a href="{{ route('cursos.estudiantes', $curso->id) }}" class="btn btn-info btn-sm">Ver Estudiantes</a>
                        @if(session('rol') === 'administrador')
                        <!-- Botón para editar curso -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $curso->id }}">Editar</button>
                        @endif
                        <!-- Formulario para eliminar curso -->
                        @if(session('rol') === 'administrador')
                            <form action="{{ route('cursos.destroy', $curso->id) }}" method="POST" style="display:inline;">
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

    <!-- Modal para Crear Curso -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cursos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Cargar Lista de Estudiantes</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xls,.xlsx" required>
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

    <!-- Modales para Editar Curso -->
    @foreach($cursos as $curso)
        <div class="modal fade" id="editModal-{{ $curso->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Curso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('cursos.update', $curso->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $curso->nombre }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" required>{{ $curso->descripcion }}</textarea>
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
