<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Actividades Académicas</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* Estilo para asegurar que las imágenes se ajusten */
        .carousel-image {
            height: 100%;
            max-height: 400px; /* Altura máxima que deseas para el carrusel */
            width: 100%;
            object-fit: cover; /* Ajusta la imagen sin deformarla */
        }

        /* Fondo con gradiente sutil */
        body {
            background: linear-gradient(to right, #e0eafc, #cfdef3); /* Azul claro a azul grisáceo */
            color: #333;
        }

        /* Ajuste responsivo para el carrusel */
        #studentCarousel {
            min-height: 300px; /* Altura mínima */
        }

        /* Estilo para el botón de login */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Estilo para el card */
        .card {
            background-color: #ffffffcc; /* Fondo blanco con transparencia */
            border: none;
        }
    </style>
</head>
<body class="bg-light text-dark">
<!-- Header con opciones de Registro y Login -->
<header class="w-100 d-flex justify-content-end align-items-center py-3" style="background-color: #003366;">
    <span class="me-auto ms-5 text-white" style="font-size: 1.5rem;">Bienvenido a la Plataforma de Gestión Académica</span>
    <a href="{{ route('login') }}" class="btn btn-primary me-2 bg-white text-black">Login</a>
</header>
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="row w-100">

        <div class="text-center py-5">
            <h1 class="display-4 text-primary">Aplicación Web para la Gestión de Asignación de Tareas Académicas en Juancito Pinto</h1>
            <p class="lead">Proyecto final de grado para optar por el título profesional a nivel técnico medio en Sistemas Informáticos</p>
        </div>
    </div>

    <!-- Slider (Carousel) -->
    <div class="row w-100 mb-5">
        <div id="studentCarousel" class="carousel slide col-12 col-lg-8 mx-auto" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://res.cloudinary.com/dkpuiyovk/image/upload/v1730568251/pngwing.com_18_onvorg.png" class="d-block w-100 carousel-image" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="https://res.cloudinary.com/dkpuiyovk/image/upload/v1730567380/pngwing.com_17_ocxgn5.png" class="d-block w-100 carousel-image" alt="Slide 2">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#studentCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#studentCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>

    <div class="row w-100">
        <main class="col-12 col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body" style="background-color: #003366;">
                    <h2 class="h3 text-white">Nuestro Proyecto</h2>
                    <p class="mt-4 text-white">Una herramienta esencial para mejorar la organización, responsabilidad y comunicación en la comunidad educativa.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer text-center mt-5" style="padding: 20px;">
        <div class="container">
            <p class="text-muted mb-0">© 2024 Todos los derechos reservados | Proyecto Bachiller Técnico Humanístico</p>
        </div>
    </footer>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
