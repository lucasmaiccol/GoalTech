<?php
$mensajeError = "";
$mensajeExito = "";
$email = "";
//variable vacia para luego imprimir mensajes de errores.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
//revisa si el usuario envio el formulario.
    $email = $_POST["email"];
    $password = $_POST["password"];
//guarda los datos que los usuarios envien.

//verificacion en caso de que las casillas queden vacias.
    if (empty($email)) {
        $mensajeError = "Debe ingresar un correo electronico";
    } elseif (empty($password)) {
        $mensajeError = "Debe ingresar una contraseña";
    } else {
        $mensajeExito = "Datos recibidos correctamente";
    }
}
//verificacion en caso de que las casillas queden vacias.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>
<body>
    <main class="contenedor-login">
    <h1>Iniciar Sesión</h1>
    <p class="subtitulo-login">Ingrese sus datos para acceder al sistema</p>
    
    <?php
    //Si la variable $mensajeError no esta vacia, se muestra un parrafo con el mensaje, si esta vacia no se muestra nada.
    if (!empty($mensajeError)) { ?>
        <p class="error"><?php echo $mensajeError; ?></p>
    <?php } ?>

    <?php
    //Si la variable $mensajeExito no esta vacia, se muestra un parrafo con el mensaje, si esta vacia no se muestra nada.
    if (!empty($mensajeExito)) { ?>
        <p class="exito"><?php echo $mensajeExito; ?></p>
    <?php } ?>
    
        <form class="formulario-login" action="login.php" method="post">
            <div class="campo">
            <label for="email">Correo electronico</label>
            <!-- 
            value - pone adentro del campo el valor que tenga la variable $email.
            htmlspecialchars - hace que no se puedan enviar caracteres especiales dentro.
            required - hace obligatorio llenar la casilla
            placeholder - pone dentro de la casilla un mensaje.
            -->
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="ejemplo@gmail.com">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
            </div>

        <button class="boton-login" type="submit">Entrar</button>
        </form>
        <a class="volver-inicio" href="../../../index.php">Volver al inicio</a>  <!-- ir a otra pagina, en este caso al inicio -->
    </main>
</body>
</html>