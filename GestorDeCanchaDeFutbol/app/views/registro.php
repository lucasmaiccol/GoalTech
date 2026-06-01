<?php
$mensajeError = "";
$mensajeExito = "";
$nombre = "";
$email = "";
$telefono = "";
require_once "../../config/conexion.php";

//variable vacia para luego imprimir mensajes.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
// guarda los datos que los usuarios envien.

    if(empty($nombre)) {
        $mensajeError = "Debe ingresar un nombre";
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
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, telefono, password, rol)
            VALUES (:nombre, :email, :telefono, :password, :rol)";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ":nombre" => $nombre,
        ":email" => $email,
        ":telefono" => $telefono,
        ":password" => $passwordHash,
        ":rol" => "cliente"
    ]);

    $mensajeExito = "Usuario registrado correctamente";
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
    <main class="contenedor-registro">
    <h1>Registrarse</h1>
    
    <?php
    if (!empty($mensajeError)) { ?>
        <p class="error"><?php echo $mensajeError; ?></p>
    <?php } ?>

    <?php
    if (!empty($mensajeExito)) { ?>
        <p class="exito"><?php echo $mensajeExito; ?></P>
    <?php } ?>

    
    
    
    
        <form class="formulario-registro" action="registro.php" method="post">
            <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required placeholder="Ingrese su nombre">
            </div>
        
            <div class="campo">
            <label for="email">Correo electronico</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="ejemplo@gmail.com">
            </div>
        
            <div class="campo">
            <label for="telefono">Numero de telefono</label>
            <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese un telefono">
            </div>
        
            <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
            </div>
        
            <div class="campo">
            <label for="confirmPassword">Confirmar contraseña</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirme su contraseña">
            </div>

            <button class="boton-registro" type="submit">Registrarse</button>
        </form>
    </main>
</body>
</html>