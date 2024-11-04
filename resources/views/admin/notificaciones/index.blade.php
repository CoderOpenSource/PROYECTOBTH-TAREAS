@extends('layouts.app')

@section('title', 'Listado de Notificaciones')
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
    @if(session('rol') === 'administrador' || session('rol') === 'profesor')
        <li class="nav-item">
            <a href="/cursos" class="nav-link">Gestionar Cursos</a>
        </li>
    @endif
        <li class="nav-item">
            <a href="/tareas" class="nav-link">Gestionar Tareas</a>
        </li>
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
        <h2 class="mb-4">Listado de Notificaciones</h2>
        <!-- Filtros de búsqueda -->
        <form method="GET" action="{{ route('notificaciones.index') }}" class="row g-3 mb-4">
            @if(session('rol') === 'profesor' || session('rol') === 'administrador')
                <!-- Filtro de título y curso -->
                <div class="col-md-4">
                    <input type="text" name="titulo" class="form-control" placeholder="Buscar por título" value="{{ request('titulo') }}">
                </div>
                <div class="col-md-4">
                    <select name="curso_id" class="form-select">
                        <option value="">Selecciona un curso</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @elseif(session('rol') === 'estudiante')
                <!-- Filtro de materia para el estudiante -->
                <div class="col-md-4">
                    <select name="materia" class="form-select">
                        <option value="">Selecciona una materia</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia }}" {{ request('materia') == $materia ? 'selected' : '' }}>
                                {{ $materia }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('notificaciones.index') }}" class="btn btn-secondary">Restablecer</a>
            </div>
        </form>

        <!-- Botón para abrir el modal de creación -->
        @if(session('rol') === 'profesor')
            <button class="btn mb-3 text-white" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Notificación</button>
        @endif

        <!-- Mostrar mensaje de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Notificaciones -->
        <table class="table table-striped">
            <thead class="table" style="background-color: #003366;">
            <tr>
                <th class="text-white">Título</th>
                <th class="text-white">Mensaje</th>
                <th class="text-white">Curso</th>
                <th class="text-white">Profesor</th>
                <th class="text-white">Materia</th>
                <th class="text-white">Fecha</th>
                <th class="text-white">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($notificaciones as $notificacion)
                <tr>
                    <td>{{ $notificacion->titulo }}</td>
                    <td>{{ $notificacion->mensaje }}</td>
                    <td>{{ $notificacion->curso->nombre ?? 'Sin asignar' }}</td>
                    <td>{{ $notificacion->profesor->nombre ?? 'Automática' }}</td>
                    <td>{{ $notificacion->profesor->materia ?? 'Automática' }}</td>
                    <td>{{ $notificacion->created_at->format('d-m-Y') }}</td>
                    <td>
                        @if(session('rol') === 'profesor' && !$notificacion->es_automatica)
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $notificacion->id }}">Editar</button>
                            <form action="{{ route('notificaciones.destroy', $notificacion->id) }}" method="POST" style="display:inline;">
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

        <!-- Paginación -->
        <div class="mt-4">
            {{ $notificaciones->links() }}
        </div>
    </div>

    <!-- Modal para Crear Notificación -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('notificaciones.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="curso_id" class="form-label">Curso</label>
                            <select name="curso_id" id="curso_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona un curso</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
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

    <!-- Modales para Editar Notificación -->
    @foreach($notificaciones as $notificacion)
        <div class="modal fade" id="editModal-{{ $notificacion->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Notificación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('notificaciones.update', $notificacion->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="titulo-{{ $notificacion->id }}" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo-{{ $notificacion->id }}" name="titulo" value="{{ $notificacion->titulo }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="mensaje-{{ $notificacion->id }}" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="mensaje-{{ $notificacion->id }}" name="mensaje" required>{{ $notificacion->mensaje }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="curso_id-{{ $notificacion->id }}" class="form-label">Curso</label>
                                <select name="curso_id" id="curso_id-{{ $notificacion->id }}" class="form-select" required>
                                    <option value="" disabled>Selecciona un curso</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" {{ $notificacion->curso_id == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
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
@endsection
