<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3); /* Azul claro a azul grisáceo */
            color: #333;
        }
    </style>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center text-white" style="background-color: #003366;">
                    <h3>Iniciar Sesión</h3>
                    <p class="mb-0">Accede como Administrador, Profesor o Estudiante</p>
                </div>
                <div class="card-body">

                    <!-- Mostrar mensajes de error aquí -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf <!-- Token de protección contra CSRF -->

                        <!-- Correo -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="correo" value="{{ old('correo') }}" required autofocus>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Recordarme -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>

                        <!-- Botón de Iniciar Sesión -->
                        <div class="d-grid">
                            <button type="submit" class="btn text-white" style="background-color: #003366;">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Usuario loggeado con éxito.
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mostrar el modal de éxito y cerrarlo después de 3 segundos
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        setTimeout(function() {
            successModal.hide();
        }, 3000);
        @endif
    });
</script>

<!-- Footer -->
<footer class="footer text-center mt-5" style="padding: 20px;">
    <div class="container">
        <p class="text-muted mb-0">© 2024 Todos los derechos reservados | Proyecto Bachiller Técnico Humanístico</p>
    </div>
</footer>
</body>
</html>
