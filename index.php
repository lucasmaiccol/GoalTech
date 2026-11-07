<?php                                                                                   // Abre el bloque de codigo PHP.
// index.php = FRONT CONTROLLER: es el unico archivo que se abre desde el navegador.
// Recibe todas las peticiones (index.php?r=login, index.php?r=dashboard, etc.) y se las pasa al Router.

ini_set("display_errors", "0");                                                         // Los errores de PHP NO se muestran en pantalla (podrian revelar rutas y datos del servidor)...
ini_set("log_errors", "1");                                                             // ...se guardan en el log del servidor.
header_remove("X-Powered-By");                                                          // No avisa que version de PHP usa el servidor.
header("X-Frame-Options: DENY");                                                        // Ninguna otra pagina puede mostrar este sitio dentro de un <iframe> (evita clickjacking).
header("X-Content-Type-Options: nosniff");                                              // El navegador respeta el tipo de archivo que se le indica (no "adivina").
header("Referrer-Policy: same-origin");                                                 // Solo envia la pagina de origen a nuestro propio sitio.
header("Content-Security-Policy: frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'"); // Politica de seguridad: no iframes, formularios solo a este sitio, sin plugins.
header("Cache-Control: no-store");                                                      // El navegador no guarda copia de las paginas (al cerrar sesion, el boton "atras" no muestra datos privados).

require_once __DIR__ . "/app/Core/helpers.php";                                         // Carga las funciones de ayuda (h(), url(), etc.).

// Autoload: cuando el codigo usa una clase nueva, PHP la busca solo en estas carpetas (no hace falta un require por clase).
spl_autoload_register(function ($clase) {                                               // Registra la funcion que busca clases.
    foreach (["Core", "Entities", "Repositories", "Services", "Controllers"] as $carpeta) { // Recorre las carpetas del proyecto.
        $archivo = __DIR__ . "/app/" . $carpeta . "/" . $clase . ".php";                // Arma la ruta: app/Carpeta/Clase.php.
        if (is_file($archivo)) {                                                        // Si el archivo existe...
            require_once $archivo;                                                      // ...lo carga...
            return;                                                                     // ...y termina.
        }                                                                               // Cierra el if.
    }                                                                                   // Cierra el foreach.
});                                                                                     // Cierra el autoload.

try {                                                                                   // Intenta atender la peticion.
    Sesion::iniciar();                                                                  // Inicia la sesion con cookies seguras.
    $router = new Router(require __DIR__ . "/config/rutas.php");                        // Crea el router con la tabla de rutas.
    $router->despachar();                                                               // Atiende la peticion.
} catch (Throwable $e) {                                                                // Si ocurre CUALQUIER error inesperado...
    error_log("GoalTech - " . get_class($e) . ": " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine()); // ...guarda el detalle tecnico en el log del servidor...
    http_response_code(500);                                                            // ...responde con error 500...
    Vista::render("error", ["codigo" => 500, "titulo" => "Algo salio mal", "mensaje" => "El sistema no esta disponible por el momento. Intenta de nuevo en unos minutos."]); // ...y muestra un mensaje generico (el usuario NO ve datos tecnicos).
}                                                                                       // Cierra el catch.
