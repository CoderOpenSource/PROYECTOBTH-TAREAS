@extends('layouts.app')

@section('title', 'Gestionar Calificaciones')
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
        <h2 class="mb-4">Lista de Calificaciones</h2>
        <!-- Filtros de búsqueda -->
        <form method="GET" action="{{ route('calificaciones.index') }}" class="mb-3">
            <div class="row">
                @if(session('rol') === 'administrador' || session('rol') === 'profesor')
                    <!-- Filtro de Curso -->
                    <div class="col-md-3">
                        <label for="filter_curso_id" class="form-label">Curso</label>
                        <select name="curso_id" id="filter_curso_id" class="form-select">
                            <option value="">Todos los cursos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro de Tarea -->
                    <div class="col-md-3">
                        <label for="filter_tarea_id" class="form-label">Tarea</label>
                        <select name="tarea_id" id="filter_tarea_id" class="form-select">
                            <option value="">Todas las tareas</option>
                            @foreach($tareas as $tarea)
                                <option value="{{ $tarea->id }}" {{ request('tarea_id') == $tarea->id ? 'selected' : '' }}>{{ $tarea->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif(session('rol') === 'estudiante')
                    <!-- Filtro de Materia (para Estudiante) -->
                    <div class="col-md-3">
                        <label for="filter_materia" class="form-label">Materia</label>
                        <select name="materia" id="filter_materia" class="form-select">
                            <option value="">Todas las materias</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia }}" {{ request('materia') == $materia ? 'selected' : '' }}>{{ $materia }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filtros de Fecha -->
                <div class="col-md-3">
                    <label for="filter_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="filter_fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="filter_fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <!-- Botón para aplicar filtros -->
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                    <a href="{{ route('calificaciones.index') }}" class="btn btn-secondary">Restablecer</a>
                </div>
            </div>
        </form>

        <!-- Mostrar mensaje de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Botón Añadir Tarea solo para profesores -->
        @if(session('rol') === 'profesor')
            <div class="mb-3">
                <a class="btn text-white mb-3" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#createModal">Añadir Calificación</a>
            </div>
        @endif
        <!-- Tabla de Calificaciones -->
        <table class="table table-striped">
            <thead class="table" style="background-color: #003366;">
            <tr>
                <th class="text-white">Estudiante</th>
                <th class="text-white">Tarea</th>
                <th class="text-white">Calificación</th>
                <th class="text-white">Comentarios</th>
                <th class="text-white">Profesor</th>
                <th class="text-white">Materia</th>
                <th class="text-white">Curso</th>
                <th class="text-white">Fecha</th>
                <th class="text-white">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($calificaciones as $calificacion)
                <tr>
                    <td>{{ $calificacion->estudiante->nombre }}</td>
                    <td>{{ $calificacion->tarea->titulo }}</td>
                    <td>{{ $calificacion->calificacion }}</td>
                    <td>{{ $calificacion->comentarios ?? 'Sin comentarios' }}</td>
                    <td>{{ $calificacion->profesor->nombre }}</td>
                    <td>{{ $calificacion->profesor->materia }}</td>
                    <td>{{ $calificacion->tarea->curso->nombre }}</td>
                    <td>{{ $calificacion->created_at->format('d-m-Y') }}</td>
                    <td>
                        @if(session('rol') === 'profesor')
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $calificacion->id }}">Editar</button>
                            <form action="{{ route('calificaciones.destroy', $calificacion->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                <!-- Modal para Editar Calificación -->
                <div class="modal fade" id="editModal-{{ $calificacion->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $calificacion->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="editModalLabel-{{ $calificacion->id }}">Editar Calificación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('calificaciones.update', $calificacion->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="curso_id_{{ $calificacion->id }}" class="form-label">Curso</label>
                                        <input type="text" class="form-control" id="curso_id_{{ $calificacion->id }}" value="{{ $calificacion->tarea->curso->nombre }}" readonly>
                                        <input type="hidden" name="curso_id" value="{{ $calificacion->tarea->curso->id }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tarea_id_{{ $calificacion->id }}" class="form-label">Tarea</label>
                                        <input type="text" class="form-control" id="tarea_id_{{ $calificacion->id }}" value="{{ $calificacion->tarea->titulo }}" readonly>
                                        <input type="hidden" name="tarea_id" value="{{ $calificacion->tarea->id }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="estudiante_id_{{ $calificacion->id }}" class="form-label">Estudiante</label>
                                        <input type="text" class="form-control" id="estudiante_id_{{ $calificacion->id }}" value="{{ $calificacion->estudiante->nombre }}" readonly>
                                        <input type="hidden" name="estudiante_id" value="{{ $calificacion->estudiante->id }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="calificacion_{{ $calificacion->id }}" class="form-label">Calificación</label>
                                        <input type="number" step="0.1" class="form-control" id="calificacion_{{ $calificacion->id }}" name="calificacion" value="{{ $calificacion->calificacion }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comentarios_{{ $calificacion->id }}" class="form-label">Comentarios</label>
                                        <textarea class="form-control" id="comentarios_{{ $calificacion->id }}" name="comentarios">{{ $calificacion->comentarios }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para Crear Calificación -->
    <form method="POST" action="{{ route('calificaciones.store') }}">
        @csrf
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createModalLabel">Añadir Calificación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="curso_id" class="form-label">Curso</label>
                            <select name="curso_id" id="curso_id" class="form-select" required onchange="actualizarTareasYEstudiantes()">
                                <option value="" disabled selected>Selecciona un curso</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tarea_id" class="form-label">Tarea</label>
                            <select name="tarea_id" id="tarea_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona una tarea</option>
                                @foreach($tareas as $tarea)
                                    <option value="{{ $tarea->id }}">{{ $tarea->titulo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estudiante_id" class="form-label">Estudiante</label>
                            <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona un estudiante</option>
                                @foreach($estudiantes as $estudiante)
                                    <option value="{{ $estudiante->id }}">{{ $estudiante->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="calificacion" class="form-label">Calificación</label>
                            <input type="number" step="0.1" class="form-control" id="calificacion" name="calificacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="comentarios" name="comentarios"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function actualizarTareasYEstudiantes() {
            const cursoId = document.getElementById('curso_id').value;
            const profesorId = {{ session('usuario_id') }}; // Obtenemos el usuario_id de la sesión
            console.log("Curso seleccionado:", cursoId); // Verificar el ID del curso
            console.log("Profesor ID:", profesorId); // Verificar el ID del profesor

            if (!cursoId) return;

            // Actualizar el select de tareas
            console.log("Solicitando tareas para el curso y profesor", cursoId, profesorId);
            fetch(`/api/tareas?curso_id=${cursoId}&profesor_id=${profesorId}`)
                .then(response => {
                    if (!response.ok) throw new Error("Error al obtener tareas");
                    return response.json();
                })
                .then(data => {
                    const tareaSelect = document.getElementById('tarea_id');
                    tareaSelect.innerHTML = '<option value="" disabled selected>Selecciona una tarea</option>';
                    data.forEach(tarea => {
                        tareaSelect.innerHTML += `<option value="${tarea.id}">${tarea.titulo}</option>`;
                    });
                })
                .catch(error => console.error("Error al cargar tareas:", error));

            // Actualizar el select de estudiantes (no se cambia aquí)
            fetch(`/api/estudiantes?curso_id=${cursoId}`)
                .then(response => {
                    if (!response.ok) throw new Error("Error al obtener estudiantes");
                    return response.json();
                })
                .then(data => {
                    const estudianteSelect = document.getElementById('estudiante_id');
                    estudianteSelect.innerHTML = '<option value="" disabled selected>Selecciona un estudiante</option>';
                    data.forEach(estudiante => {
                        estudianteSelect.innerHTML += `<option value="${estudiante.id}">${estudiante.nombre}</option>`;
                    });
                })
                .catch(error => console.error("Error al cargar estudiantes:", error));
        }

    </script>

@endsection
