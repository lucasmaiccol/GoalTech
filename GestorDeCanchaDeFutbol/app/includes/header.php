<?php                                                                          // Abre el bloque de codigo PHP.
// header.php: parte de arriba comun a todas las paginas del panel (menu + mensajes).
// Cada pagina define $tituloPagina antes de incluir este archivo.
$tituloPagina = $tituloPagina ?? "Panel";                                      // Si la pagina no definio titulo, usa "Panel".
$mensajeFlash = tomarMensaje();                                                // Lee el mensaje pendiente (exito o error) si hay uno.
?>
<!DOCTYPE html>
<!-- Le avisa al navegador que es un documento HTML5 -->
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
    <link rel="stylesheet" href="../../css/estilo.css">
    <!-- Estilos generales del proyecto (login, registro, mensajes) -->
    <link rel="stylesheet" href="../../css/panel.css">
    <!-- Estilos del panel: menu, tablas, formularios, botones -->
</head>
<body class="panel">
    <!-- Cuerpo de la pagina; la clase "panel" activa los estilos del panel -->
    <header class="barra">
        <!-- Barra superior de navegacion -->
        <a class="barra-logo" href="dashboard.php">Goal<span>Tech</span></a>
        <!-- Logo: al hacer clic vuelve al inicio del panel -->
        <nav class="barra-nav">
            <!-- Contenedor de los enlaces del menu -->
            <?php if (esAdmin()) { ?>
                <!-- Si es admin, muestra el menu de administracion -->
                <a href="admin_reservas.php">Reservas</a>
                <!-- Enlace a la gestion de reservas -->
                <a href="admin_canchas.php">Canchas</a>
                <!-- Enlace a la gestion de canchas -->
                <a href="admin_usuarios.php">Usuarios</a>
                <!-- Enlace a la gestion de usuarios -->
            <?php } else { ?>
                <!-- Si es cliente, muestra solo su enlace -->
                <a href="dashboard.php">Mis reservas</a>
                <!-- Enlace al dashboard del cliente -->
            <?php } ?>
            <span class="barra-usuario"><?php echo h($_SESSION["usuario_nombre"] ?? ""); ?> (<?php echo h($_SESSION["usuario_rol"] ?? ""); ?>)</span>
            <!-- Muestra quien esta logueado y con que rol -->
            <a class="barra-salir" href="logout.php">Salir</a>
            <!-- Enlace para cerrar sesion -->
        </nav>
    </header>

    <main class="panel-contenido">
        <!-- Zona principal donde cada pagina pone su contenido -->
        <?php if ($mensajeFlash) { ?>
            <!-- Si hay un mensaje pendiente, lo muestra -->
            <p class="<?php echo $mensajeFlash["tipo"] === "exito" ? "exito" : "error"; ?>"><?php echo h($mensajeFlash["texto"]); ?></p>
            <!-- Cuadro verde si es exito, rojo si es error -->
        <?php } ?>
