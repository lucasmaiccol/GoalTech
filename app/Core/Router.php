<?php                                                                                   // Abre el bloque de codigo PHP.
// Router: recibe TODAS las peticiones (via index.php), controla los PERMISOS y el token CSRF en un solo lugar,
// y llama al controlador correspondiente. Asi ningun controlador tiene que acordarse de proteger su pagina.

class Router                                                                            // Define la clase.
{                                                                                       // Abre la clase.
    private $rutas;                                                                     // Tabla de rutas (config/rutas.php).

    public function __construct($rutas)                                                 // Recibe la tabla de rutas.
    {                                                                                   // Abre el constructor.
        $this->rutas = $rutas;                                                          // La guarda.
    }                                                                                   // Cierra el constructor.

    // Muestra una pagina de error con su codigo HTTP.
    private function error($codigo, $titulo, $mensaje)                                  // Recibe codigo, titulo y mensaje.
    {                                                                                   // Abre el metodo.
        http_response_code($codigo);                                                    // Envia el codigo HTTP (404, 403, 500...).
        Vista::render("error", ["codigo" => $codigo, "titulo" => $titulo, "mensaje" => $mensaje]); // Muestra la pagina de error.
    }                                                                                   // Cierra el metodo.

    // Atiende la peticion actual.
    public function despachar()                                                         // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $ruta = isset($_GET["r"]) && is_string($_GET["r"]) ? $_GET["r"] : "inicio";     // Lee la ruta pedida (?r=login). Por defecto "inicio".
        if (!isset($this->rutas[$ruta])) {                                              // Si la ruta no existe...
            $this->error(404, "Pagina no encontrada", "La pagina que buscas no existe."); // ...muestra error 404.
            return;                                                                     // Termina.
        }                                                                               // Cierra el if.
        list($clase, $metodo, $permiso) = $this->rutas[$ruta];                          // Obtiene controlador, metodo y permiso de la ruta.

        // CSRF: TODO envio de formulario (POST) debe traer el token secreto de la sesion.
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !Sesion::csrfValido($_POST["csrf_token"] ?? null)) { // Si falta el token o no coincide...
            $this->error(403, "Solicitud no valida", "No se pudo verificar el formulario (token CSRF) o el archivo era demasiado grande. Volve atras, recarga la pagina e intenta de nuevo."); // ...rechaza.
            return;                                                                     // Termina.
        }                                                                               // Cierra el if.

        $usuario = Fabrica::authService()->usuarioActual();                             // Usuario logueado, leido de la BASE en cada pedido (el rol siempre esta actualizado).

        // PERMISOS: se controlan ACA, antes de ejecutar cualquier controlador.
        if ($permiso === "invitado" && $usuario) {                                      // Pagina solo para visitantes (login/registro) y ya esta logueado...
            redirigir($usuario->esAdmin() ? "admin_reservas" : "dashboard");           // ...lo manda a su panel.
        }                                                                               // Cierra el if.
        if (in_array($permiso, ["logueado", "cliente", "admin"], true) && !$usuario) {  // Pagina privada y no hay sesion...
            redirigir("login");                                                         // ...lo manda al login.
        }                                                                               // Cierra el if.
        if ($permiso === "cliente" && $usuario->esAdmin()) {                            // Pagina de cliente y entro un admin...
            redirigir("admin_reservas");                                                // ...lo manda a su panel.
        }                                                                               // Cierra el if.
        if ($permiso === "admin" && !$usuario->esAdmin()) {                             // Pagina de admin y NO es admin...
            redirigir("dashboard");                                                     // ...lo manda al panel de cliente.
        }                                                                               // Cierra el if.

        Vista::compartir("usuarioActual", $usuario);                                    // Deja el usuario disponible en todas las vistas.
        $controlador = Fabrica::crearControlador($clase);                               // Crea el controlador con sus servicios.
        $controlador->definirUsuario($usuario);                                         // Le pasa el usuario logueado.
        $controlador->$metodo();                                                        // Ejecuta la accion pedida.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
