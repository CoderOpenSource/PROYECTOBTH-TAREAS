@extends('layouts.app')

@section('title', 'Gestionar Actas')
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
@if(session('rol') === 'profesor' || session('rol') === 'administrador')
    <li class="nav-item">
        <a href="/cursos" class="nav-link">Gestionar Cursos</a>
    </li>
@endif

<li class="nav-item">
    <a href="/actas" class="nav-link">Gestionar Actas</a>
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
    <h2 class="mb-4">Lista de Actas para Padres o Profesores</h2>
    @if(session('rol') === 'administrador')
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Acta</button>
    @endif

    <!-- Mostrar mensaje de éxito en un modal -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <!-- Mostrar mensaje de error si ya existe una acta para el curso -->
    @if($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Tabla de Actas -->
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th>Título</th>
            <th>Curso</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actas as $acta)
        <tr>
            <td>{{ $acta->titulo }}</td>
            <td>{{ $acta->curso->nombre }}</td>
            <td>
                <!-- Botón para ver comportamientos -->
                <a href="{{ route('comportamientos.index', $acta->id) }}" class="btn btn-info btn-sm">Ver Seguimiento</a>
                <!-- Botón para editar acta -->
                @if(session('rol') === 'administrador')
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $acta->id }}">Editar</button>
                @endif
                <!-- Formulario para eliminar acta -->
                @if(session()->has('admin'))
                <form action="{{ route('actas.destroy', $acta->id) }}" method="POST" style="display:inline;">
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

<!-- Modal para Crear Acta -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createModalLabel">Añadir Acta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('actas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="curso_id" class="form-label">Curso</label>
                        <select class="form-select" id="curso_id" name="curso_id" required>
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

<!-- Modales para Editar Acta -->
@foreach($actas as $acta)
<div class="modal fade" id="editModal-{{ $acta->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Editar Acta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('actas.update', $acta->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $acta->titulo }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="curso_id" class="form-label">Curso</label>
                        <select class="form-select" id="curso_id" name="curso_id" required>
                            <option value="" disabled>Selecciona un curso</option>
                            @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" @if($acta->curso_id == $curso->id) selected @endif>{{ $curso->nombre }}</option>
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
