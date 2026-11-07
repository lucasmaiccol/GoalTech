<?php                                                                                   // Abre el bloque de codigo PHP.
// cabecera.php: parte de arriba comun a las paginas del panel (menu + mensajes). Cada vista define $tituloPagina antes de incluirla.
$tituloPagina = $tituloPagina ?? "Panel";                                               // Si la pagina no definio titulo, usa "Panel".
$mensajeFlash = Sesion::tomarMensaje();                                                 // Lee el mensaje pendiente (exito o error), si hay uno.
?>
<!DOCTYPE html>
<!-- Le avisa al navegador que es HTML5 -->
<html lang="es">
<!-- Raiz del documento, idioma español -->
<head>
    <!-- Cabecera: datos que no se ven en la pagina -->
    <meta charset="UTF-8">
    <!-- Codificacion de caracteres para ver tildes y ñ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Hace que se vea bien en celulares -->
    <title><?php echo h($tituloPagina); ?> · GoalTech</title>
    <!-- Titulo de la pestaña del navegador -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Carga las tipografias Oswald e Inter desde Google Fonts -->
    <link rel="stylesheet" href="css/estilo.css">
    <!-- Estilos generales del proyecto (mensajes de error y exito) -->
    <link rel="stylesheet" href="css/panel.css">
    <!-- Estilos del panel: menu, tablas, formularios, botones -->
</head>
<body class="panel">
    <!-- Cuerpo de la pagina; la clase "panel" activa los estilos del panel -->
    <header class="barra">
        <!-- Barra superior de navegacion -->
        <a class="barra-logo" href="<?php echo h(url($usuarioActual->esAdmin() ? "admin_reservas" : "dashboard")); ?>">Goal<span>Tech</span></a>
        <!-- Logo: al hacer clic vuelve al inicio del panel -->
        <nav class="barra-nav">
            <!-- Contenedor de los enlaces del menu -->
            <?php if ($usuarioActual->esAdmin()) { ?>
                <!-- Si es admin, muestra el menu de administracion -->
                <a href="<?php echo h(url("admin_reservas")); ?>">Reservas</a>
                <!-- Enlace a la gestion de reservas -->
                <a href="<?php echo h(url("admin_canchas")); ?>">Canchas</a>
                <!-- Enlace a la gestion de canchas -->
                <a href="<?php echo h(url("admin_usuarios")); ?>">Usuarios</a>
                <!-- Enlace a la gestion de usuarios -->
            <?php } else { ?>
                <!-- Si es cliente, muestra solo su enlace -->
                <a href="<?php echo h(url("dashboard")); ?>">Mis reservas</a>
                <!-- Enlace al dashboard del cliente -->
            <?php } ?>
            <span class="barra-usuario"><?php echo h($usuarioActual->nombre); ?> (<?php echo h($usuarioActual->rol); ?>)</span>
            <!-- Muestra quien esta logueado y con que rol -->
            <form class="form-salir" action="<?php echo h(url("logout")); ?>" method="post">
                <!-- Cerrar sesion es un formulario POST con token CSRF (un simple enlace podria ser usado por otra pagina para cerrarte la sesion) -->
                <?php echo campoCsrf(); ?>
                <!-- Token CSRF -->
                <button class="barra-salir" type="submit">Salir</button>
                <!-- Boton para cerrar sesion -->
            </form>
        </nav>
    </header>

    <main class="panel-contenido">
        <!-- Zona principal donde cada pagina pone su contenido -->
        <?php if ($mensajeFlash) { ?>
            <!-- Si hay un mensaje pendiente, lo muestra -->
            <p class="<?php echo $mensajeFlash["tipo"] === "exito" ? "exito" : "error"; ?>"><?php echo h($mensajeFlash["texto"]); ?></p>
            <!-- Cuadro verde si es exito, rojo si es error -->
        <?php } ?>
