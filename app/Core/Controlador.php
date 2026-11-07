<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador: clase base de todos los controladores. Tiene las herramientas comunes.
// Un controlador lee la peticion ($_POST/$_GET), llama a un SERVICIO y elige la vista. No escribe SQL ni HTML.

abstract class Controlador                                                              // Clase abstracta: no se usa sola, se hereda.
{                                                                                       // Abre la clase.
    protected $usuario = null;                                                          // Usuario logueado (entidad Usuario) o null.

    public function definirUsuario($usuario)                                            // El router le pasa el usuario logueado.
    {                                                                                   // Abre el metodo.
        $this->usuario = $usuario;                                                      // Lo guarda.
    }                                                                                   // Cierra el metodo.

    protected function esPost()                                                         // Dice si la peticion es un envio de formulario.
    {                                                                                   // Abre el metodo.
        return $_SERVER["REQUEST_METHOD"] === "POST";                                   // true si el metodo HTTP es POST.
    }                                                                                   // Cierra el metodo.

    // Lee un campo de texto de $_POST (sin espacios sobrantes). Si no existe o no es texto, devuelve el valor por defecto.
    protected function post($campo, $porDefecto = "")                                   // Recibe el nombre del campo.
    {                                                                                   // Abre el metodo.
        return isset($_POST[$campo]) && is_string($_POST[$campo]) ? trim($_POST[$campo]) : $porDefecto; // Devuelve el texto "limpio".
    }                                                                                   // Cierra el metodo.

    // Igual que post() pero SIN recortar espacios (para las contraseñas).
    protected function postCrudo($campo)                                                // Recibe el nombre del campo.
    {                                                                                   // Abre el metodo.
        return isset($_POST[$campo]) && is_string($_POST[$campo]) ? $_POST[$campo] : ""; // Devuelve el texto tal cual.
    }                                                                                   // Cierra el metodo.

    // Lee un parametro de la URL ($_GET), como el id.
    protected function consulta($campo, $porDefecto = "")                               // Recibe el nombre del parametro.
    {                                                                                   // Abre el metodo.
        return isset($_GET[$campo]) && is_string($_GET[$campo]) ? trim($_GET[$campo]) : $porDefecto; // Devuelve el valor.
    }                                                                                   // Cierra el metodo.

    // Guarda el resultado de un servicio como mensaje para la proxima pagina (verde si salio bien, rojo si no).
    protected function avisar($resultado)                                               // Recibe ["exito" => bool, "mensaje" => texto].
    {                                                                                   // Abre el metodo.
        Sesion::mensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]); // Guarda el mensaje con su tipo.
    }                                                                                   // Cierra el metodo.

    protected function render($vista, $datos = [])                                      // Muestra una plantilla.
    {                                                                                   // Abre el metodo.
        Vista::render($vista, $datos);                                                  // Delega en la clase Vista.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
