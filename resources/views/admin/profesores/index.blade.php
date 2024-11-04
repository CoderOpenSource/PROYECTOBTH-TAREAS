@extends('layouts.app')

@section('title', 'Gestionar Profesores')
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
        <h2 class="mb-4 text-indigo-950">Lista de Profesores</h2>
        <button class="btn text-white mb-3" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Profesor</button>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#importModal">Añadir Profesores mediante Excel</button>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Profesores -->
        <table class="table table-hover table-bordered table-striped align-middle">
            <thead class="table" style="background-color: #003366;">
            <tr>
                <th class="text-white">CI</th>
                <th class="text-white">Nombre</th>
                <th class="text-white">Correo Electrónico</th>
                <th class="text-white">Materia</th>
                <th class="text-white">Fecha de Registro</th>
                <th class="text-white">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($profesores as $profesor)
                <tr>
                    <td>{{ $profesor->ci }}</td>
                    <td>{{ $profesor->nombre }}</td>
                    <td>{{ $profesor->correo }}</td>
                    <td>{{ $profesor->materia }}</td>
                    <td>{{ $profesor->fecha_registro }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $profesor->id }}">Editar</button>
                        <form action="{{ route('profesores.destroy', $profesor->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para Crear Profesor -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Profesor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profesores.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ci" class="form-label">CI</label>
                            <input type="text" class="form-control" id="ci" name="ci" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="materia" class="form-label">Materia</label>
                            <select class="form-select" id="materia" name="materia" required>
                                <option value="" disabled selected>Selecciona una materia</option>
                                @foreach($materias as $materia)
                                    <option value="{{ $materia }}">{{ $materia }}</option>
                                @endforeach
                            </select>
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

    <!-- Modales para Editar Profesor -->
    @foreach($profesores as $profesor)
        <div class="modal fade" id="editModal-{{ $profesor->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Profesor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('profesores.update', $profesor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ci" class="form-label">CI</label>
                                <input type="text" class="form-control" id="ci" name="ci" value="{{ $profesor->ci }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $profesor->nombre }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="materia" class="form-label">Materia</label>
                                <select class="form-select" id="materia" name="materia" required>
                                    <option value="" disabled>Selecciona una materia</option>
                                    @foreach($materias as $materia)
                                        <option value="{{ $materia }}" @if($profesor->materia == $materia) selected @endif>{{ $materia }}</option>
                                    @endforeach
                                </select>
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

    <!-- Modal para importar Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importModalLabel">Importar Profesores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profesores.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Subir archivo Excel</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
