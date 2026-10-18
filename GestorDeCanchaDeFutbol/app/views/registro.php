<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AuthController.php";                            // Carga el controlador de autenticacion.

$mensajeError = "";                                                                     // Variable vacia para luego imprimir errores.
$mensajeExito = "";                                                                     // Variable vacia para luego imprimir el exito.
$nombre = "";                                                                           // Para recordar el nombre escrito.
$email = "";                                                                            // Para recordar el email escrito.
$telefono = "";                                                                         // Para recordar el telefono escrito.

if ($_SERVER["REQUEST_METHOD"] == "POST") {                                             // Si el usuario envio el formulario...
    $nombre = trim($_POST["nombre"] ?? "");                                             // Guarda el nombre (sin espacios de mas).
    $email = trim($_POST["email"] ?? "");                                               // Guarda el email.
    $telefono = trim($_POST["telefono"] ?? "");                                         // Guarda el telefono.
    $password = $_POST["password"] ?? "";                                               // Guarda la contraseña.
    $confirmPassword = $_POST["confirmPassword"] ?? "";                                 // Guarda la confirmacion de la contraseña.

    $auth = new AuthController($conexion);                                              // Crea el controlador de autenticacion.
    $resultado = $auth->registrar($nombre, $email, $telefono, $password, $confirmPassword); // Le pide validar y registrar al usuario.

    if ($resultado["exito"]) {                                                          // Si se registro bien...
        $mensajeExito = $resultado["mensaje"];                                          // ...guarda el mensaje de exito.
        $nombre = $email = $telefono = "";                                              // ...y limpia los campos para que no queden escritos.
    } else {                                                                            // Si hubo un error...
        $mensajeError = $resultado["mensaje"];                                          // ...guarda el mensaje de error.
    }                                                                                   // Cierra el if/else.
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
    <title>Registrarse · GoalTech</title>
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
            <h2 class="titulo-grande">Armá el partido<br>con <em>tus amigos</em></h2>
            <!-- Titulo grande de la presentacion -->
            <p class="texto-hero">Creá tu cuenta gratis y empezá a reservar las mejores canchas de fútbol 5 en minutos.</p>
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
                <h1>Registrarse</h1>
                <!-- Titulo -->
                <p class="subtitulo-login">Creá tu cuenta y reservá en minutos</p>
                <!-- Subtitulo -->

                <?php if (!empty($mensajeError)) { ?>
                    <!-- Si hay error, lo muestra -->
                    <p class="error"><?php echo h($mensajeError); ?></p>
                    <!-- Cuadro rojo con el error -->
                <?php } ?>

                <?php if (!empty($mensajeExito)) { ?>
                    <!-- Si se registro bien, lo muestra -->
                    <p class="exito"><?php echo h($mensajeExito); ?></p>
                    <!-- Cuadro verde con el mensaje -->
                <?php } ?>

                <form class="formulario-registro" action="registro.php" method="post">
                    <!-- Formulario que envia los datos a esta misma pagina por POST -->
                    <div class="campo">
                        <!-- Grupo del campo nombre -->
                        <label for="nombre">Nombre</label>
                        <!-- Etiqueta -->
                        <input type="text" id="nombre" name="nombre" value="<?php echo h($nombre); ?>" required placeholder="Ingrese su nombre">
                        <!-- Campo de texto para el nombre -->
                    </div>
                    <div class="campo">
                        <!-- Grupo del campo email -->
                        <label for="email">Correo electronico</label>
                        <!-- Etiqueta -->
                        <input type="email" id="email" name="email" value="<?php echo h($email); ?>" required placeholder="ejemplo@gmail.com">
                        <!-- Campo de email (el navegador valida el formato) -->
                    </div>
                    <div class="campo">
                        <!-- Grupo del campo telefono -->
                        <label for="telefono">Telefono</label>
                        <!-- Etiqueta -->
                        <input type="text" id="telefono" name="telefono" value="<?php echo h($telefono); ?>" required placeholder="099123456">
                        <!-- Campo de texto para el telefono -->
                    </div>
                    <div class="campo">
                        <!-- Grupo del campo contraseña -->
                        <label for="password">Contraseña</label>
                        <!-- Etiqueta -->
                        <input type="password" id="password" name="password" required placeholder="Minimo 6 caracteres">
                        <!-- Campo de contraseña -->
                    </div>
                    <div class="campo">
                        <!-- Grupo del campo confirmar contraseña -->
                        <label for="confirmPassword">Confirmar contraseña</label>
                        <!-- Etiqueta -->
                        <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Repita su contraseña">
                        <!-- Segundo campo de contraseña para evitar errores de tipeo -->
                    </div>

                    <button class="boton-registro" type="submit">Registrarse</button>
                    <!-- Boton que envia el formulario -->
                </form>
                <p class="registro-link">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
                <!-- Enlace a la pagina de login -->
                <a class="volver-inicio" href="../../../index.php">Volver al inicio</a>
                <!-- Enlace para volver a la pagina principal -->
            </main>
        </div>
    </div>
</body>
</html>
