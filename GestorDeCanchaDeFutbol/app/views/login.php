<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion (e inicia la sesion).
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AuthController.php";                            // Carga el controlador de autenticacion.

if (estaLogueado()) {                                                                   // Si ya hay una sesion iniciada...
    header("Location: " . (esAdmin() ? "admin_reservas.php" : "dashboard.php"));        // ...lo manda a su panel segun su rol.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$mensajeError = "";                                                                     // Variable vacia para luego imprimir errores.
$mensajeExito = "";                                                                     // Variable vacia para luego imprimir mensajes de exito.
$email = "";                                                                            // Variable para recordar el email escrito si hubo error.

if ($_SERVER["REQUEST_METHOD"] == "POST") {                                             // Revisa si el usuario envio el formulario.
    $email = trim($_POST["email"] ?? "");                                               // Guarda el email enviado (sin espacios de mas).
    $password = $_POST["password"] ?? "";                                               // Guarda la contraseña enviada.

    if (empty($email)) {                                                                // Si dejo el email vacio...
        $mensajeError = "Debe ingresar un correo electronico";                          // ...prepara el error.
    } elseif (empty($password)) {                                                       // Si dejo la contraseña vacia...
        $mensajeError = "Debe ingresar una contraseña";                                 // ...prepara el error.
    } else {                                                                            // Si los dos campos tienen datos...
        $auth = new AuthController($conexion);                                          // Crea el controlador de autenticacion.
        $resultado = $auth->iniciarSesion($email, $password);                           // Le pide validar el login.

        if ($resultado["exito"]) {                                                      // Si el login fue correcto...
            header("Location: " . (esAdmin() ? "admin_reservas.php" : "dashboard.php")); // ...redirige: admin a su panel, cliente a su dashboard.
            exit;                                                                       // Corta la ejecucion.
        } else {                                                                        // Si el login fallo...
            $mensajeError = $resultado["mensaje"];                                      // ...guarda el mensaje de error para mostrarlo.
        }                                                                               // Cierra el if/else interno.
    }                                                                                   // Cierra el if/else de validacion.
}                                                                                       // Cierra el if del POST.
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
    <title>Iniciar Sesión · GoalTech</title>
    <!-- Titulo de la pestaña -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Acelera la carga de las tipografias -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Carga las tipografias Oswald e Inter -->
    <link rel="stylesheet" href="../../css/estilo.css">
    <!-- Estilos de los formularios del proyecto -->
    <link rel="stylesheet" href="../../css/tema.css">
    <!-- Tema compartido: fondo, tipografias, botones y collage -->
    <link rel="stylesheet" href="../../css/auth.css">
    <!-- Estilos del login y registro (dos columnas) -->
</head>
<body class="tema pagina-auth">
    <!-- Cuerpo de la pagina; la clase "tema" activa el fondo con foto -->
    <div class="auth-wrap">
        <!-- Contenedor general centrado -->
        <a class="tema-logo" href="../../../index.php">Goal<span>Tech</span></a>
        <!-- Logo: vuelve a la portada -->
        <div class="auth-grid">
            <!-- Dos columnas: presentacion a la izquierda y formulario a la derecha -->
            <section class="auth-hero">
                <!-- Columna de presentacion con fotos -->
            <span class="etiqueta-top">Reservas online 24/7</span>
            <!-- Texto chico sobre el titulo -->
            <h2 class="titulo-grande">Tu cancha de <em>fútbol&nbsp;5</em>,<br>a un clic</h2>
            <!-- Titulo grande de la presentacion -->
            <p class="texto-hero">Entrá a tu cuenta, elegí el horario que más te guste y asegurá tu partido de la semana.</p>
            <!-- Texto de apoyo -->
            <ul class="beneficios">
                <!-- Lista de beneficios -->
                <li>Elegí tu cancha, el día y el horario</li>
                <!-- Beneficio 1 -->
                <li>Cancelá tu reserva cuando quieras</li>
                <!-- Beneficio 2 -->
            </ul>
            <div class="collage">
                <!-- Collage de tres fotos inclinadas, decorativo -->
                <img class="foto f1" src="https://images.unsplash.com/photo-1632300951015-42d7df909581?auto=format&fit=crop&w=800&q=80" alt="Partido de futbol entre amigos" onerror="this.onerror=null;this.src='../../img/canchas/default.svg'">
                <!-- Foto grande de un partido -->
                <img class="foto f2" src="https://images.unsplash.com/photo-1604412132568-9d5c14de13c4?auto=format&fit=crop&w=800&q=80" alt="Cancha de futbol entre arboles" onerror="this.onerror=null;this.src='../../img/canchas/default.svg'">
                <!-- Foto de una cancha entre arboles -->
                <img class="foto f3" src="https://images.unsplash.com/photo-1600529533273-938db2591dc4?auto=format&fit=crop&w=800&q=80" alt="Cancha de futbol sintetica" onerror="this.onerror=null;this.src='../../img/canchas/default.svg'">
                <!-- Foto de una cancha sintetica -->
                <span class="chip chip1">Fútbol 5</span>
                <!-- Cartelito flotante -->
                <span class="chip chip2">Reservá en 1 minuto</span>
                <!-- Cartelito flotante -->
            </div>
            </section>

            <main class="auth-card">
                <!-- Tarjeta blanca con el formulario -->
                <h1>Iniciar Sesión</h1>
                <!-- Titulo -->
                <p class="subtitulo-login">Ingrese sus datos para acceder al sistema</p>
                <!-- Subtitulo -->

                <?php if (!empty($mensajeError)) { ?>
                    <!-- Si hay mensaje de error, se muestra un parrafo; si esta vacio no se muestra nada -->
                    <p class="error"><?php echo h($mensajeError); ?></p>
                    <!-- Cuadro rojo con el error (h() evita inyeccion de codigo) -->
                <?php } ?>

                <?php if (!empty($mensajeExito)) { ?>
                    <!-- Si hay mensaje de exito, se muestra -->
                    <p class="exito"><?php echo h($mensajeExito); ?></p>
                    <!-- Cuadro verde con el mensaje -->
                <?php } ?>

                <form class="formulario-login" action="login.php" method="post">
                    <!-- Formulario que envia los datos a esta misma pagina por POST -->
                    <div class="campo">
                        <!-- Grupo del campo email -->
                        <label for="email">Correo electronico</label>
                        <!-- Etiqueta del campo -->
                        <input type="email" id="email" name="email" value="<?php echo h($email); ?>" required placeholder="ejemplo@gmail.com">
                        <!-- value: recuerda lo escrito | required: obligatorio | placeholder: texto de ayuda -->
                    </div>
                    <div class="campo">
                        <!-- Grupo del campo contraseña -->
                        <label for="password">Contraseña</label>
                        <!-- Etiqueta del campo -->
                        <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
                        <!-- Campo de contraseña (los caracteres se ocultan) -->
                    </div>

                    <button class="boton-login" type="submit">Entrar</button>
                    <!-- Boton que envia el formulario -->
                </form>
                <p class="registro-link">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <!-- Enlace a la pagina de registro -->
                <a class="volver-inicio" href="../../../index.php">Volver al inicio</a>
                <!-- Enlace para volver a la pagina principal -->
            </main>
        </div>
    </div>
</body>
</html>
