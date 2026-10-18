<?php                                                          // Abre el bloque de codigo PHP.
// Archivo de conexion: todas las paginas lo incluyen para hablar con la base de datos.
$host = "localhost";                                           // Servidor de la base de datos (XAMPP corre MySQL en la misma PC).
$baseDatos = "sistema_canchas";                                // Nombre de la base de datos creada con el archivo .sql.
$usuario = "root";                                             // Usuario de MySQL (el que trae XAMPP por defecto).
$password = "";                                                // Contraseña de MySQL (XAMPP viene sin contraseña).

try {                                                          // Intenta conectarse; si falla, salta al bloque catch de abajo.
    $conexion = new PDO(                                       // Crea la conexion usando PDO (libreria de PHP para bases de datos).
        "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4",  // Tipo de base, servidor, nombre de la base y codificacion (tildes y ñ).
        $usuario,                                              // Usuario de MySQL.
        $password                                              // Contraseña de MySQL.
    );                                                         // Cierra la creacion de la conexion.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            // Si una consulta falla, PHP lanza una excepcion (asi podemos atraparla).
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);       // Las filas se devuelven como arrays con el nombre de la columna.
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);                    // Usa consultas preparadas reales de MySQL (mas seguro).
} catch (PDOException $error) {                                // Si no se pudo conectar, entra aca.
    die("Error de conexion: " . $error->getMessage());         // Corta el programa y muestra el motivo del error.
}                                                              // Cierra el bloque catch.
