<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de autenticacion: recibe los datos de las vistas, le pregunta al modelo y decide que hacer.
// No imprime HTML: devuelve arrays [exito, mensaje] y la vista decide como mostrarlos.

require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo Usuario (una sola vez).

class AuthController                                                                    // Define la clase del controlador.
{                                                                                       // Abre la clase.
    private $usuarioModelo;                                                             // Aca se guarda el modelo Usuario.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->usuarioModelo = new Usuario($conexion);                                  // Crea el modelo Usuario con esa conexion.
    }                                                                                   // Cierra el constructor.

    // LOGIN: valida email y contraseña; si estan bien, abre la sesion.
    public function iniciarSesion($email, $password)                                    // Recibe lo que escribio el usuario.
    {                                                                                   // Abre el metodo.
        $usuario = $this->usuarioModelo->buscarPorEmail($email);                        // Busca el usuario en la base por su email.
        $mensajeInvalido = "Correo o contraseña incorrectos";                           // Mensaje generico: no decimos cual de los dos fallo (seguridad).

        if (!$usuario) {                                                                // Si no existe un usuario con ese email...
            return ["exito" => false, "mensaje" => $mensajeInvalido];                   // ...devuelve error.
        }                                                                               // Cierra el if.

        if (!password_verify($password, $usuario["password"])) {                        // Compara la contraseña escrita contra el hash guardado.
            return ["exito" => false, "mensaje" => $mensajeInvalido];                   // Si no coincide, devuelve error.
        }                                                                               // Cierra el if.

        session_regenerate_id(true);                                                    // Cambia el id de sesion al loguear (evita robo de sesion).
        $_SESSION["usuario_id"] = $usuario["id"];                                       // Guarda el id del usuario en la sesion.
        $_SESSION["usuario_nombre"] = $usuario["nombre"];                               // Guarda el nombre para mostrarlo en el menu.
        $_SESSION["usuario_rol"] = $usuario["rol"];                                     // Guarda el rol ("cliente" o "admin") para los permisos.

        return ["exito" => true, "mensaje" => "Bienvenido, " . $usuario["nombre"]];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // REGISTRO: valida los datos y crea un usuario nuevo con rol "cliente".
    public function registrar($nombre, $email, $telefono, $password, $confirmar)        // Recibe los datos del formulario.
    {                                                                                   // Abre el metodo.
        if ($nombre === "") {                                                           // Si el nombre esta vacio...
            return ["exito" => false, "mensaje" => "Debe ingresar un nombre"];          // ...error.
        }                                                                               // Cierra el if.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {                               // Si el email no tiene formato valido...
            return ["exito" => false, "mensaje" => "Debe ingresar un correo electronico valido"]; // ...error.
        }                                                                               // Cierra el if.
        if ($telefono === "") {                                                         // Si el telefono esta vacio...
            return ["exito" => false, "mensaje" => "Debe ingresar un numero de telefono"]; // ...error.
        }                                                                               // Cierra el if.
        if (strlen($password) < 6) {                                                    // Si la contraseña tiene menos de 6 caracteres...
            return ["exito" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]; // ...error.
        }                                                                               // Cierra el if.
        if ($password !== $confirmar) {                                                 // Si las dos contraseñas no son iguales...
            return ["exito" => false, "mensaje" => "Las contraseñas no coinciden"];     // ...error.
        }                                                                               // Cierra el if.
        if ($this->usuarioModelo->buscarPorEmail($email)) {                             // Si ya existe un usuario con ese email...
            return ["exito" => false, "mensaje" => "Ese correo ya esta registrado"];    // ...error (evita cuentas duplicadas).
        }                                                                               // Cierra el if.

        $hash = password_hash($password, PASSWORD_DEFAULT);                             // Convierte la contraseña en un hash (nunca se guarda en texto plano).
        $this->usuarioModelo->crear($nombre, $email, $telefono, $hash, "cliente");      // Guarda el usuario; todo registro publico es "cliente".
        return ["exito" => true, "mensaje" => "Usuario registrado correctamente. Ya podes iniciar sesion."]; // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // LOGOUT: borra todos los datos de la sesion.
    public function cerrarSesion()                                                      // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $_SESSION = [];                                                                 // Vacia todas las variables de sesion.
        session_destroy();                                                              // Destruye la sesion en el servidor.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
