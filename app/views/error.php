<!DOCTYPE html>
<!-- Pagina de error generica (404, 403, 500). Nunca muestra datos tecnicos. -->
<html lang="es">
<!-- Raiz del documento -->
<head>
    <!-- Datos que no se ven -->
    <meta charset="UTF-8">
    <!-- Codificacion -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Adaptable a celulares -->
    <title><?php echo h($titulo); ?> · GoalTech</title>
    <!-- Titulo de la pestaña -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tipografias -->
    <link rel="stylesheet" href="css/tema.css">
    <!-- Tema compartido -->
    <link rel="stylesheet" href="css/inicio.css">
    <!-- Estilos de la portada (se reutilizan para centrar el contenido) -->
</head>
<body class="tema">
    <!-- Cuerpo con el fondo del tema -->
    <main class="inicio-hero error-pagina">
        <!-- Contenido centrado -->
        <section>
            <!-- Caja del mensaje -->
            <span class="etiqueta-top">Error <?php echo (int)$codigo; ?></span>
            <!-- Codigo del error -->
            <h1 class="titulo-grande"><?php echo h($titulo); ?></h1>
            <!-- Titulo del error -->
            <p class="texto-hero"><?php echo h($mensaje); ?></p>
            <!-- Explicacion para el usuario -->
            <a class="btn-verde" href="<?php echo h(url("inicio")); ?>">Volver al inicio</a>
            <!-- Boton para volver -->
        </section>
    </main>
</body>
</html>
