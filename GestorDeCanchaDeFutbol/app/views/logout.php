<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion (el controlador la pide).
require_once __DIR__ . "/../controllers/AuthController.php";                            // Carga el controlador de autenticacion.

$auth = new AuthController($conexion);                                                  // Crea el controlador.
$auth->cerrarSesion();                                                                  // Borra todos los datos de la sesion.
header("Location: login.php");                                                          // Manda al usuario a la pantalla de login.
exit;                                                                                   // Corta la ejecucion.
