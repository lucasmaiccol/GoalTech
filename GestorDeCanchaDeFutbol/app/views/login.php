<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    echo "Correo electronico  " . $email;
    echo "<br>";
    echo "Contraseña recibida correctamente";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion;</title>
</head>
<body>
    <h1>Iniciar Sesion</h1>
    
    <form action="login.php" method="post">
        <label for="email">Correo electronico</label>
        <input type="email" id="email" name="email">

        <br><br>

        <label for="password">contraseña</label>
        <input type="password" id="password" name="password">

        <br><br>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>