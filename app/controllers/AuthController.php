<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de autenticacion: portada, login, registro y logout.
// Lee la peticion, llama al servicio y elige la vista. No escribe SQL ni reglas del negocio.

class AuthController extends Controlador                                                // Hereda las herramientas de Controlador.
{                                                                                       // Abre la clase.
    private $auth;                                                                      // Servicio de autenticacion.

    public function __construct(AuthService $auth)                                      // Recibe el servicio.
    {                                                                                   // Abre el constructor.
        $this->auth = $auth;                                                            // Lo guarda.
    }                                                                                   // Cierra el constructor.

    public function inicio()                                                            // Portada.
    {                                                                                   // Abre el metodo.
        $this->render("inicio");                                                        // Muestra la plantilla de la portada.
    }                                                                                   // Cierra el metodo.

    public function login()                                                             // Pantalla y proceso de login.
    {                                                                                   // Abre el metodo.
        $error = "";                                                                    // Mensaje de error (vacio al principio).
        $email = "";                                                                    // Correo escrito (para recordarlo si hay error).
        if ($this->esPost()) {                                                          // Si envio el formulario...
            $email = $this->post("email");                                              // Lee el correo.
            $password = $this->postCrudo("password");                                   // Lee la contraseña (sin recortar).
            if ($email === "" || $password === "") {                                    // Si falta algun dato...
                $error = "Debe ingresar correo y contraseña";                           // ...error.
            } else {                                                                    // Si estan los dos...
                $resultado = $this->auth->iniciarSesion($email, $password, $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0"); // ...pide validar el login (con la IP del visitante).
                if ($resultado["exito"]) {                                              // Si el login fue correcto...
                    redirigir($resultado["usuario"]->esAdmin() ? "admin_reservas" : "dashboard"); // ...va al panel segun su rol.
                }                                                                       // Cierra el if.
                $error = $resultado["mensaje"];                                         // Si fallo, guarda el mensaje.
            }                                                                           // Cierra el if/else.
        }                                                                               // Cierra el if del POST.
        $aviso = Sesion::tomarMensaje();                                                // Lee un aviso pendiente (ej: "tu sesion se cerro por inactividad").
        if ($aviso && $error === "") {                                                  // Si hay aviso y no hay otro error...
            $error = $aviso["texto"];                                                   // ...lo muestra en el login.
        }                                                                               // Cierra el if.
        $this->render("login", ["error" => $error, "email" => $email]);                 // Muestra la plantilla de login.
    }                                                                                   // Cierra el metodo.

    public function registro()                                                          // Pantalla y proceso de registro.
    {                                                                                   // Abre el metodo.
        $datos = ["nombre" => "", "email" => "", "telefono" => ""];                     // Valores a recordar en el formulario.
        $error = "";                                                                    // Mensaje de error.
        $exito = "";                                                                    // Mensaje de exito.
        if ($this->esPost()) {                                                          // Si envio el formulario...
            $datos = ["nombre" => $this->post("nombre"), "email" => $this->post("email"), "telefono" => $this->post("telefono")]; // Lee los datos de texto.
            $resultado = $this->auth->registrar($datos["nombre"], $datos["email"], $datos["telefono"], $this->postCrudo("password"), $this->postCrudo("confirmPassword")); // Pide registrar.
            if ($resultado["exito"]) {                                                  // Si se registro...
                $exito = $resultado["mensaje"];                                         // ...guarda el mensaje de exito...
                $datos = ["nombre" => "", "email" => "", "telefono" => ""];             // ...y limpia el formulario.
            } else {                                                                    // Si hubo error...
                $error = $resultado["mensaje"];                                         // ...guarda el mensaje.
            }                                                                           // Cierra el if/else.
        }                                                                               // Cierra el if del POST.
        $this->render("registro", ["error" => $error, "exito" => $exito, "datos" => $datos]); // Muestra la plantilla de registro.
    }                                                                                   // Cierra el metodo.

    public function logout()                                                            // Cierra la sesion (solo por POST con token CSRF).
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo si vino de un formulario (no por un simple enlace)...
            $this->auth->cerrarSesion();                                                // ...cierra la sesion.
        }                                                                               // Cierra el if.
        redirigir($this->esPost() ? "login" : "inicio");                                // Va al login (o a la portada si entro por URL).
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
