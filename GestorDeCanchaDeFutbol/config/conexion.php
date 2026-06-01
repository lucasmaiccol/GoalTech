<?php
$host = "localhost";
$baseDatos = "sistema_canchas";
$usuario = "root";
$password = "";

try {                                       // intenta esto si falla mostra el mensaje de error.
    $conexion = new PDO(
        "mysql:host=$host;dbname=$baseDatos;charset=utf8",    // codificacion utf8 (sirven letras como ñ, á, é, í, ó, ú).
        $usuario,
        $password
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch  (PDOException $error) {
        die("Error de conexion: " . $error->getMessage());
}
?> 