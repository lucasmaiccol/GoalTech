<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoalTech · Reservá tu cancha</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="GestorDeCanchaDeFutbol/css/estilo.css">
</head>
<body class="pagina-inicio">

    <header class="nav-inicio">
        <span class="logo-inicio">Goal<span class="logo-acento">Tech</span></span>
    </header>

    <main class="hero-inicio">

        <!-- Ilustracion decorativa de cancha, no aporta contenido -->
        <svg class="lineas-cancha" viewBox="0 0 800 220" aria-hidden="true">
            <line x1="400" y1="0" x2="400" y2="220" class="linea-svg"/>
            <circle cx="400" cy="110" r="80" class="linea-svg"/>
            <circle cx="400" cy="110" r="3" class="punto-svg"/>
        </svg>

        <div class="contenido-hero">
            <span class="eyebrow-hero">Reservas online 24/7</span>
            <h1>Tu cancha,<br>a un toque de distancia.</h1>
            <p class="subtitulo-hero">Elegí el horario, reservá y jugá. Sin llamados, sin vueltas.</p>

            <div class="acciones-hero">
                <a class="boton-primario" href="GestorDeCanchaDeFutbol/app/views/login.php">Iniciar sesión</a>
                <a class="boton-secundario" href="GestorDeCanchaDeFutbol/app/views/registro.php">Registrarse</a>
            </div>
        </div>

    </main>

</body>
</html>
