<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/GestorDeCanchaDeFutbol/app/includes/auth.php";                 // Carga las funciones de sesion (para saber si ya hay alguien logueado).
$panel = esAdmin() ? "admin_reservas.php" : "dashboard.php";                            // Decide a que panel mandar segun el rol.
?>
<!DOCTYPE html>
<!-- Le avisa al navegador que es HTML5 -->
<html lang="es">
<!-- Raiz del documento, idioma español -->
<head>
    <!-- Datos que no se ven en la pagina -->
    <meta charset="UTF-8">
    <!-- Codificacion para tildes y ñ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Se adapta a celulares -->
    <title>GoalTech · Reservá tu cancha de fútbol 5</title>
    <!-- Titulo de la pestaña -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Acelera la carga de las tipografias -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Carga las tipografias Oswald e Inter -->
    <link rel="stylesheet" href="GestorDeCanchaDeFutbol/css/tema.css">
    <!-- Tema compartido con el login y el registro -->
    <link rel="stylesheet" href="GestorDeCanchaDeFutbol/css/inicio.css">
    <!-- Estilos propios de la portada -->
</head>
<body class="tema">
    <!-- Cuerpo de la portada; la clase "tema" activa el fondo con foto (igual que login y registro) -->

    <header class="inicio-nav">
        <!-- Barra superior -->
        <a class="tema-logo" href="index.php">Goal<span>Tech</span></a>
        <!-- Logo -->
        <nav class="acciones-nav">
            <!-- Botones de la barra -->
            <?php if (estaLogueado()) { ?>
                <!-- Si ya inicio sesion, muestra acceso a su panel -->
                <a class="btn-verde" href="GestorDeCanchaDeFutbol/app/views/<?php echo $panel; ?>">Ir a mi panel</a>
                <!-- Lleva al dashboard (cliente) o al panel (admin) -->
                <a class="btn-borde" href="GestorDeCanchaDeFutbol/app/views/logout.php">Cerrar sesión</a>
                <!-- Cierra la sesion -->
            <?php } else { ?>
                <!-- Si no inicio sesion, muestra login y registro -->
                <a class="btn-borde" href="GestorDeCanchaDeFutbol/app/views/login.php">Iniciar sesión</a>
                <!-- Lleva al login -->
                <a class="btn-verde" href="GestorDeCanchaDeFutbol/app/views/registro.php">Registrarse</a>
                <!-- Lleva al registro -->
            <?php } ?>
        </nav>
    </header>

    <main class="inicio-hero">
        <!-- Seccion principal: texto a la izquierda y fotos a la derecha -->
        <section>
            <!-- Columna de texto -->
            <span class="etiqueta-top">Reservas online 24/7</span>
            <!-- Texto chico sobre el titulo -->
            <h1 class="titulo-grande">Tu cancha,<br>tu tiempo,<br><em>tu partido</em></h1>
            <!-- Titulo principal -->
            <p class="texto-hero">Elegí la cancha de fútbol 5, el día y el horario. Reservá en un minuto, sin llamados ni vueltas.</p>
            <!-- Subtitulo -->
            <div class="acciones-hero">
                <!-- Botones de accion principales -->
                <?php if (estaLogueado()) { ?>
                    <!-- Si ya inicio sesion -->
                    <a class="btn-verde" href="GestorDeCanchaDeFutbol/app/views/<?php echo $panel; ?>">Reservar ahora</a>
                    <!-- Lleva a su panel -->
                <?php } else { ?>
                    <!-- Si no inicio sesion -->
                    <a class="btn-verde" href="GestorDeCanchaDeFutbol/app/views/registro.php">Crear mi cuenta</a>
                    <!-- Lleva al registro -->
                    <a class="btn-borde" href="GestorDeCanchaDeFutbol/app/views/login.php">Ya tengo cuenta</a>
                    <!-- Lleva al login -->
                <?php } ?>
            </div>
            <div class="datos-hero">
                <!-- Datos cortos destacados -->
                <div><strong>24/7</strong><span>Reservá cuando quieras</span></div>
                <!-- Dato 1 -->
                <div><strong>1 hora</strong><span>Turnos por cancha</span></div>
                <!-- Dato 2 -->
                <div><strong>Fútbol 5</strong><span>Cesped sintético</span></div>
                <!-- Dato 3 -->
            </div>
        </section>

        <section>
            <!-- Columna de fotos -->
            <div class="collage">
                <!-- Collage de tres fotos inclinadas, decorativo -->
                <img class="foto f1" src="https://images.unsplash.com/photo-1632300951015-42d7df909581?auto=format&fit=crop&w=800&q=80" alt="Partido de futbol entre amigos" onerror="this.onerror=null;this.src='GestorDeCanchaDeFutbol/img/canchas/default.svg'">
                <!-- Foto grande de un partido -->
                <img class="foto f2" src="https://images.unsplash.com/photo-1604412132568-9d5c14de13c4?auto=format&fit=crop&w=800&q=80" alt="Cancha de futbol entre arboles" onerror="this.onerror=null;this.src='GestorDeCanchaDeFutbol/img/canchas/default.svg'">
                <!-- Foto de una cancha entre arboles -->
                <img class="foto f3" src="https://images.unsplash.com/photo-1600529533273-938db2591dc4?auto=format&fit=crop&w=800&q=80" alt="Cancha de futbol sintetica" onerror="this.onerror=null;this.src='GestorDeCanchaDeFutbol/img/canchas/default.svg'">
                <!-- Foto de una cancha sintetica -->
                <span class="chip chip1">Fútbol 5</span>
                <!-- Cartelito flotante -->
                <span class="chip chip2">Pago simulado</span>
                <!-- Cartelito flotante -->
            </div>
        </section>
    </main>

    <section class="inicio-pasos">
        <!-- Seccion "como funciona" -->
        <h2>Reservar es muy fácil</h2>
        <!-- Titulo de la seccion -->
        <div class="grilla-pasos">
            <!-- Tres tarjetas -->
            <article class="paso">
                <!-- Paso 1 -->
                <div class="numero">01</div>
                <!-- Numero del paso -->
                <h3>Elegí tu cancha</h3>
                <!-- Titulo del paso -->
                <p>Mirá las fotos, los precios y quedate con la que más te guste.</p>
                <!-- Descripcion del paso -->
            </article>
            <article class="paso">
                <!-- Paso 2 -->
                <div class="numero">02</div>
                <!-- Numero del paso -->
                <h3>Reservá y pagá</h3>
                <!-- Titulo del paso -->
                <p>Seleccioná día y horario, y confirmá con el pago simulado.</p>
                <!-- Descripcion del paso -->
            </article>
            <article class="paso">
                <!-- Paso 3 -->
                <div class="numero">03</div>
                <!-- Numero del paso -->
                <h3>¡A jugar!</h3>
                <!-- Titulo del paso -->
                <p>Tu turno queda confirmado con su comprobante. Solo falta la pelota.</p>
                <!-- Descripcion del paso -->
            </article>
        </div>
    </section>

    <footer class="inicio-pie">GoalTech · Sistema de reservas de canchas de fútbol 5</footer>
    <!-- Pie de pagina -->
</body>
</html>
