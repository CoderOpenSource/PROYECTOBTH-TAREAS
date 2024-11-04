@extends('layouts.app')

@section('title', 'Gestionar Tareas')
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
    @if(session('rol') === 'administrador' || session('rol') === 'profesor')
        <li class="nav-item">
            <a href="/estudiantes" class="nav-link">Gestionar Estudiantes</a>
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
        <h2 class="mb-4 text-indigo-950">Lista de Tareas</h2>

        <!-- Mensajes de éxito y error -->
        @if(session('errors'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('errors')->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('rol') === 'administrador' || session('rol') === 'profesor')
            <!-- Filtros para admin y profesor -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label for="curso_id" class="form-label">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos los cursos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Fecha desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Restablecer</a>
                    </div>
                </div>
            </form>
            <!-- Botón Añadir Tarea solo para profesores -->
            @if(session('rol') === 'profesor')
                <div class="mb-3">
                    <a class="btn text-white mb-3" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Tarea</a>
                </div>
            @endif
        @endif

        <!-- Sección para profesores y estudiantes: tareas de hoy y próximas -->
        @if(session('rol') === 'profesor' || session('rol') === 'estudiante')
            <h3>Tareas de Hoy</h3>
            <table class="table table-hover table-bordered table-striped align-middle">
                <thead class="table" style="background-color: #003366;">
                <tr>
                    <th class="text-white">Título</th>
                    <th class="text-white">Descripción</th>
                    <th class="text-white">Fecha de Entrega</th>
                    <th class="text-white">Materia</th>
                    <th class="text-white">Recurso</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $tareasHoy = $tareas->filter(fn($tarea) => $tarea->fecha_entrega == \Carbon\Carbon::today()->toDateString());
                @endphp
                @forelse($tareasHoy as $tarea)
                    <tr>
                        <td>{{ $tarea->titulo }}</td>
                        <td>{{ $tarea->descripcion }}</td>
                        <td>{{ $tarea->fecha_entrega }}</td>
                        <td>{{ $tarea->profesor->materia ?? 'Sin asignar' }}</td>
                        <td>
                            @if($tarea->recurso)
                                <a href="{{ $tarea->recurso }}" target="_blank">Ver Recurso</a>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay tareas para hoy</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <h3>Próximas Tareas</h3>
            <table class="table table-hover table-bordered table-striped align-middle">
                <thead class="table" style="background-color: #003366;">
                <tr>
                    <th class="text-white">Título</th>
                    <th class="text-white">Descripción</th>
                    <th class="text-white">Fecha de Entrega</th>
                    <th class="text-white">Materia</th>
                    <th class="text-white">Recurso</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $tareasProximas = $tareas->filter(fn($tarea) => $tarea->fecha_entrega > \Carbon\Carbon::today()->toDateString());
                @endphp
                @forelse($tareasProximas as $tarea)
                    <tr>
                        <td>{{ $tarea->titulo }}</td>
                        <td>{{ $tarea->descripcion }}</td>
                        <td>{{ $tarea->fecha_entrega }}</td>
                        <td>{{ $tarea->profesor->materia ?? 'Sin asignar' }}</td>
                        <td>
                            @if($tarea->recurso)
                                <a href="{{ $tarea->recurso }}" target="_blank">Ver Recurso</a>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay próximas tareas</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @endif

        <!-- Tabla general de tareas para admin y profesor -->
        @if(session('rol') === 'administrador' || session('rol') === 'profesor')
            <table class="table table-hover table-bordered table-striped align-middle">
                <thead class="table" style="background-color: #003366;">
                <tr>
                    <th class="text-white">Título</th>
                    <th class="text-white">Descripción</th>
                    <th class="text-white">Fecha de Entrega</th>
                    <th class="text-white">Curso</th>
                    <th class="text-white">Materia</th>
                    <th class="text-white">Recurso</th>
                    <th class="text-white">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tareas as $tarea)
                    <tr>
                        <td>{{ $tarea->titulo }}</td>
                        <td>{{ $tarea->descripcion }}</td>
                        <td>{{ $tarea->fecha_entrega }}</td>
                        <td>{{ $tarea->curso->nombre ?? 'Sin asignar' }}</td>
                        <td>{{ $tarea->profesor->materia ?? 'Sin asignar' }}</td>
                        <td>
                            @if($tarea->recurso)
                                <a href="{{ $tarea->recurso }}" target="_blank">Ver Recurso</a>
                            @else
                                No disponible
                            @endif
                        </td>
                        <td>
                            @if(session('rol') === 'profesor')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $tarea->id }}">Editar</button>
                                <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" style="display:inline;">
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
        @endif
    </div>

    <!-- Modal para Crear Tarea -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega">
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
                        <div class="mb-3">
                            <label for="recurso" class="form-label">Recurso (opcional)</label>
                            <input type="file" class="form-control" id="recurso" name="recurso">
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

    <!-- Modales para Editar Tarea -->
    @foreach($tareas as $tarea)
        <div class="modal fade" id="editModal-{{ $tarea->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Tarea</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('tareas.update', $tarea->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $tarea->titulo }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion">{{ $tarea->descripcion }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="{{ $tarea->fecha_entrega }}">
                            </div>
                            <div class="mb-3">
                                <label for="curso_id" class="form-label">Curso</label>
                                <select name="curso_id" id="curso_id" class="form-select" required>
                                    <option value="" disabled>Selecciona un curso</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" @if($tarea->curso_id == $curso->id) selected @endif>{{ $curso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="recurso" class="form-label">Recurso (opcional)</label>
                                <input type="file" class="form-control" id="recurso" name="recurso">
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
