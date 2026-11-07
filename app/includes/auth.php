<?php                                                                          // Abre el bloque de codigo PHP.
// auth.php: funciones comunes de sesion y permisos. Se incluye al principio de cada pagina.

date_default_timezone_set("America/Montevideo");                                // Zona horaria de Uruguay, para que "hoy" y "ahora" sean correctos.

if (session_status() === PHP_SESSION_NONE) {                                   // Si todavia no hay una sesion iniciada...
    session_start();                                                           // ...la inicia (permite usar la variable $_SESSION).
}                                                                              // Cierra el if.

// h() = "escapar HTML": convierte caracteres como < y > para que nadie pueda inyectar codigo en la pagina (ataque XSS).
function h($texto)                                                             // Define la funcion h que recibe un texto.
{                                                                              // Abre la funcion.
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");              // Devuelve el texto ya "limpio" para mostrar en HTML.
}                                                                              // Cierra la funcion.

// Devuelve true si hay un usuario con sesion iniciada.
function estaLogueado()                                                        // Define la funcion.
{                                                                              // Abre la funcion.
    return isset($_SESSION["usuario_id"]);                                     // Si existe el id guardado en la sesion, es porque inicio sesion.
}                                                                              // Cierra la funcion.

// Devuelve true si el usuario logueado es administrador.
function esAdmin()                                                             // Define la funcion.
{                                                                              // Abre la funcion.
    return estaLogueado() && ($_SESSION["usuario_rol"] ?? "") === "admin";     // Debe estar logueado y su rol guardado debe ser "admin".
}                                                                              // Cierra la funcion.

// Obliga a estar logueado: si no lo esta, lo manda al login.
function requerirLogin()                                                       // Define la funcion.
{                                                                              // Abre la funcion.
    if (!estaLogueado()) {                                                     // Si NO hay sesion iniciada...
        header("Location: login.php");                                         // ...redirige al login.
        exit;                                                                  // ...y corta la ejecucion para que no se vea nada de la pagina.
    }                                                                          // Cierra el if.
}                                                                              // Cierra la funcion.

// Obliga a ser cliente (los admin tienen su propio panel).
function requerirCliente()                                                     // Define la funcion.
{                                                                              // Abre la funcion.
    requerirLogin();                                                           // Primero verifica que haya sesion.
    if (esAdmin()) {                                                           // Si es admin...
        header("Location: admin_reservas.php");                                // ...lo manda a su panel de administracion.
        exit;                                                                  // Corta la ejecucion.
    }                                                                          // Cierra el if.
}                                                                              // Cierra la funcion.

// Obliga a ser administrador: si no lo es, lo manda al dashboard de cliente.
function requerirAdmin()                                                       // Define la funcion.
{                                                                              // Abre la funcion.
    requerirLogin();                                                           // Primero verifica que haya sesion.
    if (!esAdmin()) {                                                          // Si NO es admin...
        header("Location: dashboard.php");                                     // ...lo manda al dashboard comun.
        exit;                                                                  // Corta la ejecucion.
    }                                                                          // Cierra el if.
}                                                                              // Cierra la funcion.

// Guarda un mensaje para mostrarlo UNA sola vez en la proxima pagina (tipo: "exito" o "error").
function setMensaje($tipo, $texto)                                             // Define la funcion con el tipo y el texto.
{                                                                              // Abre la funcion.
    $_SESSION["mensaje_flash"] = ["tipo" => $tipo, "texto" => $texto];         // Lo guarda en la sesion.
}                                                                              // Cierra la funcion.

// Lee el mensaje guardado y lo borra para que no se repita al recargar.
function tomarMensaje()                                                        // Define la funcion.
{                                                                              // Abre la funcion.
    $mensaje = $_SESSION["mensaje_flash"] ?? null;                             // Toma el mensaje si existe, si no deja null.
    unset($_SESSION["mensaje_flash"]);                                         // Lo borra de la sesion.
    return $mensaje;                                                           // Lo devuelve (o null si no habia).
}                                                                              // Cierra la funcion.

// Imagen que se muestra cuando una foto no carga (por ejemplo, si no hay internet). Es un dibujo guardado en el proyecto.
function imagenRespaldo()                                                      // No recibe parametros.
{                                                                              // Abre la funcion.
    return "../../img/canchas/default.svg";                                    // Ruta de la imagen por defecto (las vistas estan 2 carpetas dentro del proyecto).
}                                                                              // Cierra la funcion.

// Devuelve la direccion de la foto de una cancha (lista para usar en <img src>).
function imagenCancha($cancha)                                                 // Recibe la fila de la cancha.
{                                                                              // Abre la funcion.
    if (empty($cancha["imagen"])) {                                            // Si la cancha no tiene foto...
        return imagenRespaldo();                                               // ...usa la imagen por defecto.
    }                                                                          // Cierra el if.
    if (strpos($cancha["imagen"], "http") === 0) {                             // Si la foto es un link de internet (empieza con http)...
        return $cancha["imagen"];                                              // ...se usa tal cual.
    }                                                                          // Cierra el if.
    return "../../" . $cancha["imagen"];                                       // Si es una ruta del proyecto, le agrega "../../" para llegar desde las vistas.
}                                                                              // Cierra la funcion.

// Genera la etiqueta <img> completa de una cancha. Si la foto no carga, muestra automaticamente la imagen de respaldo.
function htmlImagenCancha($cancha, $clase, $loading = "")                      // Recibe la cancha, la clase CSS y (opcional) el modo de carga.
{                                                                              // Abre la funcion.
    $html = '<img class="' . h($clase) . '" src="' . h(imagenCancha($cancha)) . '"'; // Empieza la etiqueta con la clase y la direccion de la foto.
    $html .= ' alt="Foto de ' . h($cancha["nombre"]) . '"';                    // Agrega el texto alternativo.
    $html .= ' onerror="this.onerror=null;this.src=\'' . imagenRespaldo() . '\'"'; // Si la foto falla, cambia a la imagen de respaldo (una sola vez).
    if ($loading !== "") {                                                     // Si se pidio un modo de carga...
        $html .= ' loading="' . h($loading) . '"';                             // ...lo agrega (lazy = carga la foto cuando se acerca en pantalla).
    }                                                                          // Cierra el if.
    return $html . '>';                                                        // Cierra la etiqueta y la devuelve.
}                                                                              // Cierra la funcion.
