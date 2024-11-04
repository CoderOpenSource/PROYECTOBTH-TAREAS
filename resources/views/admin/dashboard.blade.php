@extends('layouts.app')

@section('title', 'Dashboard')
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
    @if(session('rol') === 'administrador' || session('rol') === 'profesor' || session('rol') === 'estudiante')
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
    <div class="container text-center mt-4">
        <!-- Mensaje personalizado basado en el rol del usuario -->
        @if(session('rol') === 'administrador')
            <h1>Bienvenido Administrador {{ session('nombre') }}</h1>
        @elseif(session('rol') === 'profesor')
            <h1>Bienvenido Profesor {{ session('nombre') }}</h1>
        @elseif(session('rol') === 'estudiante')
            <h1>Bienvenido Estudiante {{ session('nombre') }}</h1>
        @endif

        <!-- Menú en formato de tarjetas -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-4">
            @if(session('rol') === 'administrador')
                <!-- Tarjeta para Profesores -->
                <div class="col" >
                    <a href="/profesores" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                            <div class="card-body">
                                <h5 class="card-title text-white">Profesores</h5>
                                <img src="{{ asset('assets/img/profesores.png') }}" alt="Profesores" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if(session('rol') === 'administrador' || session('rol') === 'profesor')
                <!-- Tarjeta para Cursos -->
                <div class="col">
                    <a href="/cursos" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                            <div class="card-body">
                                <h5 class="card-title text-white">Cursos</h5>
                                <img src="{{ asset('assets/img/cursos.png') }}" alt="Cursos" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if(session('rol') === 'administrador' || session('rol') === 'profesor')
                <!-- Tarjeta para Estudiantes -->
                <div class="col">
                    <a href="/estudiantes" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                            <div class="card-body">
                                <h5 class="card-title text-white">Estudiantes</h5>
                                <img src="{{ asset('assets/img/estudiantes.png') }}" alt="Estudiantes" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Tarjeta para Actividades (Tareas, Prácticos, etc.) -->
            @if(session('rol') === 'administrador' || session('rol') === 'profesor' || session('rol') === 'estudiante')
                <div class="col">
                    <a href="/actividades" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                            <div class="card-body">
                                <h5 class="card-title text-white">Actividades</h5>
                                <img src="{{ asset('assets/img/actividades.png') }}" alt="Actividades" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Tarjeta para Notificaciones -->
            <div class="col">
                <a href="/notificaciones" class="text-decoration-none">
                    <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                        <div class="card-body">
                            <h5 class="card-title text-white">Notificaciones</h5>
                            <img src="{{ asset('assets/img/notificaciones.png') }}" alt="Notificaciones" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tarjeta para Calificaciones -->
            @if(session('rol') === 'administrador' || session('rol') === 'profesor' || session('rol') === 'estudiante')
                <div class="col">
                    <a href="/calificaciones" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm" style="background-color: #003366;">
                            <div class="card-body">
                                <h5 class="card-title text-white">Calificaciones</h5>
                                <img src="{{ asset('assets/img/calificaciones.png') }}" alt="Calificaciones" class="img-fluid mb-3" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
        <!-- Modal para notificaciones no leídas -->
        @if($notificacionesNoLeidas > 0)
            <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="notificationModalLabel">Tienes nuevas notificaciones</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Tienes {{ $notificacionesNoLeidas }} notificación(es) sin leer. Haz clic en el botón a continuación para verlas.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <a href="/notificaciones" class="btn btn-primary">Ver Notificaciones</a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Mostrar el modal automáticamente cuando hay notificaciones no leídas
                window.addEventListener('DOMContentLoaded', (event) => {
                    var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                    notificationModal.show();
                });
            </script>
        @endif
    </div>
@endsection
