<?php
// Controlador de autenticacion: recibe email/password desde la vista,
// pregunta al modelo Usuario, y decide si el login es valido.

require_once __DIR__ . "/../models/Usuario.php";

class AuthController
{
    private $usuarioModelo;

    public function __construct($conexion)
    {
        $this->usuarioModelo = new Usuario($conexion);
    }

    // Devuelve un array ["exito" => bool, "mensaje" => string].
    // No hace nada con HTML, eso queda para la vista.
    public function iniciarSesion($email, $password)
    {
        $usuario = $this->usuarioModelo->buscarPorEmail($email);

        // Mensaje generico a proposito: no decimos si fallo el email
        // o la contraseña, para no darle pistas a quien intente adivinar.
        $mensajeInvalido = "Correo o contraseña incorrectos";

        if (!$usuario) {
            return ["exito" => false, "mensaje" => $mensajeInvalido];
        }

        // password_verify compara el texto plano contra el hash guardado.
        // Nunca se comparan contraseñas en texto plano entre si.
        if (!password_verify($password, $usuario["password"])) {
            return ["exito" => false, "mensaje" => $mensajeInvalido];
        }

        // Login valido: abrimos sesion y guardamos lo minimo necesario.
        // session_start() debe llamarse antes de escribir en $_SESSION.
        session_start();
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_nombre"] = $usuario["nombre"];
        $_SESSION["usuario_rol"] = $usuario["rol"];

        return ["exito" => true, "mensaje" => "Bienvenido, " . $usuario["nombre"]];
    }
}
