<?php                                                                                   // Abre el bloque de codigo PHP.
// Sesion: todo lo que tiene que ver con $_SESSION (login, mensajes y token CSRF) en un solo lugar.

class Sesion                                                                            // Define la clase Sesion.
{                                                                                       // Abre la clase.
    // Inicia la sesion con cookies seguras. Se llama una sola vez, desde index.php.
    public static function iniciar()                                                    // No recibe parametros.
    {                                                                                   // Abre el metodo.
        if (session_status() === PHP_SESSION_NONE) {                                    // Si todavia no hay sesion iniciada...
            ini_set("session.use_strict_mode", "1");                                    // El servidor NO acepta ids de sesion inventados por el visitante (evita fijacion de sesion).
            ini_set("session.use_only_cookies", "1");                                   // El id de sesion solo viaja en la cookie, nunca en la URL.
            session_set_cookie_params([                                                 // ...configura la cookie de sesion ANTES de iniciarla.
                "httponly" => true,                                                     // JavaScript no puede leer la cookie (protege de XSS).
                "samesite" => "Lax",                                                    // El navegador no envia la cookie desde otros sitios (protege de CSRF).
                "secure" => !empty($_SERVER["HTTPS"]),                                  // Si el sitio usa HTTPS, la cookie solo viaja por HTTPS.
            ]);                                                                         // Cierra las opciones.
            session_start();                                                            // Inicia la sesion.
            self::controlarInactividad();                                               // Cierra la sesion si estuvo mucho tiempo sin usarse.
        }                                                                               // Cierra el if.
    }                                                                                   // Cierra el metodo.

    // Si pasaron mas de X segundos sin actividad, cierra la sesion (protege si alguien deja la PC abierta).
    private static function controlarInactividad()                                      // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $maximo = (int)(Config::get("seguridad")["inactividad_segundos"] ?? 1800);      // Lee el limite desde la configuracion.
        $ahora = time();                                                                // Momento actual.
        if (isset($_SESSION["ultima_actividad"]) && ($ahora - $_SESSION["ultima_actividad"]) > $maximo) { // Si estuvo inactiva mas del limite...
            $estabaLogueado = isset($_SESSION["usuario_id"]);                           // Recuerda si habia alguien logueado.
            $_SESSION = [];                                                             // Borra todos los datos de la sesion...
            session_regenerate_id(true);                                                // ...y cambia el id de sesion.
            if ($estabaLogueado) {                                                      // Si habia un usuario logueado...
                $_SESSION["mensaje_flash"] = ["tipo" => "error", "texto" => "Tu sesion se cerro por inactividad. Inicia sesion de nuevo."]; // ...le avisa.
            }                                                                           // Cierra el if.
        }                                                                               // Cierra el if.
        $_SESSION["ultima_actividad"] = $ahora;                                         // Anota la actividad de este momento.
    }                                                                                   // Cierra el metodo.

    // Guarda en la sesion QUE usuario inicio sesion (solo el id; el rol se lee de la base en cada pedido).
    public static function guardarUsuario($usuarioId)                                   // Recibe el id del usuario.
    {                                                                                   // Abre el metodo.
        session_regenerate_id(true);                                                    // Cambia el id de sesion al loguear (evita robo de sesion).
        $_SESSION["usuario_id"] = (int)$usuarioId;                                      // Guarda el id.
    }                                                                                   // Cierra el metodo.

    // Devuelve el id del usuario logueado, o null si no hay nadie.
    public static function usuarioId()                                                  // No recibe parametros.
    {                                                                                   // Abre el metodo.
        return isset($_SESSION["usuario_id"]) ? (int)$_SESSION["usuario_id"] : null;    // El id guardado o null.
    }                                                                                   // Cierra el metodo.

    // Cierra la sesion: borra todos los datos y la cookie.
    public static function cerrar()                                                     // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $_SESSION = [];                                                                 // Vacia las variables de sesion.
        if (session_status() === PHP_SESSION_ACTIVE) {                                  // Si la sesion esta activa...
            session_destroy();                                                          // ...la destruye en el servidor.
        }                                                                               // Cierra el if.
    }                                                                                   // Cierra el metodo.

    // Guarda un mensaje (tipo "exito" o "error") para mostrarlo UNA vez en la proxima pagina.
    public static function mensaje($tipo, $texto)                                       // Recibe el tipo y el texto.
    {                                                                                   // Abre el metodo.
        $_SESSION["mensaje_flash"] = ["tipo" => $tipo, "texto" => $texto];             // Lo guarda en la sesion.
    }                                                                                   // Cierra el metodo.

    // Lee el mensaje guardado y lo borra para que no se repita al recargar.
    public static function tomarMensaje()                                               // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $mensaje = $_SESSION["mensaje_flash"] ?? null;                                  // Toma el mensaje si existe.
        unset($_SESSION["mensaje_flash"]);                                              // Lo borra de la sesion.
        return $mensaje;                                                                // Lo devuelve (o null).
    }                                                                                   // Cierra el metodo.

    // Guarda un dato cualquiera en la sesion (se usa para la reserva pendiente de pago).
    public static function guardar($clave, $valor)                                      // Recibe nombre y valor.
    {                                                                                   // Abre el metodo.
        $_SESSION[$clave] = $valor;                                                     // Lo guarda.
    }                                                                                   // Cierra el metodo.

    // Lee un dato de la sesion (o null si no existe).
    public static function leer($clave)                                                 // Recibe el nombre.
    {                                                                                   // Abre el metodo.
        return $_SESSION[$clave] ?? null;                                               // Devuelve el dato o null.
    }                                                                                   // Cierra el metodo.

    // Borra un dato de la sesion.
    public static function borrar($clave)                                               // Recibe el nombre.
    {                                                                                   // Abre el metodo.
        unset($_SESSION[$clave]);                                                       // Lo elimina.
    }                                                                                   // Cierra el metodo.

    // CSRF: devuelve el token secreto de esta sesion (lo crea la primera vez).
    public static function tokenCsrf()                                                  // No recibe parametros.
    {                                                                                   // Abre el metodo.
        if (empty($_SESSION["csrf_token"])) {                                           // Si todavia no hay token...
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));                        // ...crea uno aleatorio de 64 caracteres.
        }                                                                               // Cierra el if.
        return $_SESSION["csrf_token"];                                                 // Devuelve el token.
    }                                                                                   // Cierra el metodo.

    // CSRF: compara el token recibido con el de la sesion (hash_equals evita ataques por tiempo de respuesta).
    public static function csrfValido($tokenRecibido)                                   // Recibe el token que mando el formulario.
    {                                                                                   // Abre el metodo.
        return is_string($tokenRecibido) && hash_equals(self::tokenCsrf(), $tokenRecibido); // true solo si es un texto y coincide EXACTO.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
