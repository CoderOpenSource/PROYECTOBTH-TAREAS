@extends('layouts.app')

@section('title', 'Comportamientos para Acta: ' . $acta->titulo)
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
        <h2 class="mb-4">Comportamientos para Acta: {{ $acta->titulo }}</h2>

        <!-- Formulario de búsqueda y filtros -->
        <form method="GET" action="{{ route('comportamientos.index', $acta->id) }}">
            <div class="row mb-4">
                <div class="col-md-4 position-relative">
                    <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre de estudiante" value="{{ request('nombre') }}">
                    @if(request('nombre'))
                        <!-- Botón para limpiar el campo de búsqueda -->
                        <a href="{{ route('comportamientos.index', $acta->id) }}" class="position-absolute top-50 translate-middle-y" style="right: 10px;">
                            <span class="btn btn-sm btn-light">X</span>
                        </a>
                    @endif
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_desde" class="form-control" placeholder="Fecha desde" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_hasta" class="form-control" placeholder="Fecha hasta" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                </div>
            </div>
        </form>

        @if(session('rol') === 'profesor')
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Comportamiento</button>
        @endif

        <!-- Mostrar mensajes de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Comportamientos -->
        <table id="tabla-comportamientos" class="table table-striped">
            <thead class="table-dark">
            <tr>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Estudiante</th>
                <th>Profesor</th>
                @if(session('rol') === 'profesor')
                    <th>Acciones</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($comportamientos as $comportamiento)
                <tr>
                    <td>{{ $comportamiento->descripcion }}</td>
                    <td>{{ $comportamiento->fecha }}</td>
                    <td>{{ $comportamiento->tipo }}</td>
                    <td>{{ $comportamiento->estudiante->nombre }}</td>
                    <td>{{ $comportamiento->profesor }}</td> <!-- Mostrar el campo profesor -->
                    @if(session('rol') === 'profesor')
                        <td>
                            <!-- Botón para editar comportamiento -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $comportamiento->id }}">Editar</button>

                            <!-- Formulario para eliminar comportamiento -->
                            <form action="{{ route('comportamientos.destroy', [$acta->id, $comportamiento->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para Crear Comportamiento -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">Añadir Comportamiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('comportamientos.store', $acta->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="falta" class="form-label">Falta</label>
                            <select class="form-select" id="falta" name="descripcion" required>
                                @foreach($faltas as $falta)
                                    <option value="{{ $falta }}">{{ $falta }}</option>
                                @endforeach
                                <option value="Otros">Otros</option> <!-- Agregamos la opción de "Otros" -->
                            </select>
                        </div>
                        <!-- Campo oculto para ingresar una descripción personalizada -->
                        <div class="mb-3" id="otrosDescripcion" style="display:none;">
                            <label for="descripcion_personalizada" class="form-label">Descripción Personalizada</label>
                            <textarea class="form-control" id="descripcion_personalizada" name="descripcion_personalizada"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="positivo">Positivo</option>
                                <option value="negativo">Negativo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estudiante_id" class="form-label">Estudiante</label>
                            <select class="form-select" id="estudiante_id" name="estudiante_id" required>
                                <option value="" disabled selected>Selecciona un estudiante</option>
                                @foreach($estudiantes as $estudiante)
                                    <option value="{{ $estudiante->id }}">{{ $estudiante->nombre }}</option>
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

    <!-- Script para mostrar el campo de texto si se selecciona "Otros" -->
    <script>
        document.getElementById('falta').addEventListener('change', function() {
            const otrosDescripcion = document.getElementById('otrosDescripcion');
            if (this.value === 'Otros') {
                otrosDescripcion.style.display = 'block';
            } else {
                otrosDescripcion.style.display = 'none';
            }
        });
    </script>

    <!-- Modales para Editar Comportamiento -->
    @foreach($comportamientos as $comportamiento)
        <div class="modal fade" id="editModal-{{ $comportamiento->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Editar Comportamiento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('comportamientos.update', [$acta->id, $comportamiento->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" required>{{ $comportamiento->descripcion }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="positivo" @if($comportamiento->tipo == 'positivo') selected @endif>Positivo</option>
                                    <option value="negativo" @if($comportamiento->tipo == 'negativo') selected @endif>Negativo</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estudiante_id" class="form-label">Estudiante</label>
                                <select class="form-select" id="estudiante_id" name="estudiante_id" required>
                                    @foreach($estudiantes as $estudiante)
                                        <option value="{{ $estudiante->id }}" @if($comportamiento->estudiante_id == $estudiante->id) selected @endif>
                                            {{ $estudiante->nombre }}
                                        </option>
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
