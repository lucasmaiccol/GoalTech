<?php
$mensajeError = "";
$mensajeExito = "";
$nombre = "";
$email = "";
$telefono = "";


//variable vacia para luego imprimir mensajes.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
// guarda los datos que los usuarios envien.

    if(empty($nombre)) {
        $mensajeError = "Debe ingresar un nombre de usuario";
    } elseif (empty($email)) {
        $mensajeError = "Debe ingresar un correo electronico";
    } elseif (empty($telefono)) {
        $mensajeError = "Debe ingresar un numero de telefono";
    } elseif (empty($password)) {
        $mensajeError = "Debe ingresar una contraseña";
    } elseif (empty($confirmPassword)) {
        $mensajeError = "Debe confirmar la contraseña";
    } elseif ($password != $confirmPassword) {
        $mensajeError = "Las contraseñas no coinciden";
    } else {
        $mensajeExito = "Datos recibidos correctamente";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>
<body>
    <h1>Registrarse</h1>
    <form class="formulario-registro" action="registro.php" method="post">
        
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre">

        <label for="email">Correo electronico</label>
        <input type="email" id="email" name="email">

        <label for="telefono">Numero de telefono</label>
        <input type="tel" id="telefono" name="telefono">
        
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password">

        <label for="confirmPassword">Confirmar contraseña</label>
        <input type="password" id="confirmPassword" name="confirmPassword">
        
        <button type="submit">Registrarse</button>
</form>

</body>
</html>