<?php                                                                                   // Abre el bloque de codigo PHP.
// helpers.php: funciones chicas que usan las vistas y los controladores.

// h() = "escapar HTML": evita que alguien inyecte codigo en la pagina (ataque XSS).
function h($texto)                                                                      // Recibe un texto.
{                                                                                       // Abre la funcion.
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");                       // Devuelve el texto ya "limpio" para mostrar en HTML.
}                                                                                       // Cierra la funcion.

// url() arma la direccion de una pagina. Ej: url("login") => "index.php?r=login".
function url($ruta, $parametros = [])                                                   // Recibe el nombre de la ruta y parametros extra.
{                                                                                       // Abre la funcion.
    $parametros = array_merge(["r" => $ruta], $parametros);                             // Pone la ruta como primer parametro.
    return "index.php?" . http_build_query($parametros);                                // Arma "index.php?r=...&id=...".
}                                                                                       // Cierra la funcion.

// Redirige a otra ruta y corta la ejecucion.
function redirigir($ruta, $parametros = [])                                             // Recibe la ruta y parametros.
{                                                                                       // Abre la funcion.
    header("Location: " . url($ruta, $parametros));                                     // Le dice al navegador que vaya a esa direccion.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra la funcion.

// Campo oculto con el token CSRF. TODO formulario POST debe incluirlo.
function campoCsrf()                                                                    // No recibe parametros.
{                                                                                       // Abre la funcion.
    return '<input type="hidden" name="csrf_token" value="' . h(Sesion::tokenCsrf()) . '">'; // Devuelve el <input> oculto con el token.
}                                                                                       // Cierra la funcion.

// Formatea un monto: 1200 => "1.200".
function dinero($monto)                                                                 // Recibe un numero.
{                                                                                       // Abre la funcion.
    return number_format((float)$monto, 0, ",", ".");                                   // Sin decimales, punto para los miles.
}                                                                                       // Cierra la funcion.

// Imagen que se muestra cuando una foto no carga (por ejemplo, sin internet). Es un dibujo guardado en el proyecto.
function imagenRespaldo()                                                               // No recibe parametros.
{                                                                                       // Abre la funcion.
    return "img/canchas/default.svg";                                                   // Ruta de la imagen por defecto.
}                                                                                       // Cierra la funcion.

// Devuelve la direccion de la foto de una cancha (un link de internet o una ruta del proyecto).
function imagenCancha($cancha)                                                          // Recibe la entidad Cancha.
{                                                                                       // Abre la funcion.
    if (empty($cancha->imagen)) {                                                       // Si la cancha no tiene foto...
        return imagenRespaldo();                                                        // ...usa la imagen por defecto.
    }                                                                                   // Cierra el if.
    return $cancha->imagen;                                                             // Si no, usa su foto (link http... o ruta como uploads/canchas/x.jpg).
}                                                                                       // Cierra la funcion.

// Genera la etiqueta <img> de una cancha. Si la foto no carga, muestra la imagen de respaldo.
function htmlImagenCancha($cancha, $clase, $loading = "")                               // Recibe la cancha, la clase CSS y el modo de carga.
{                                                                                       // Abre la funcion.
    $html = '<img class="' . h($clase) . '" src="' . h(imagenCancha($cancha)) . '"';    // Empieza la etiqueta con la clase y la foto.
    $html .= ' alt="Foto de ' . h($cancha->nombre) . '"';                               // Agrega el texto alternativo.
    $html .= ' onerror="this.onerror=null;this.src=\'' . imagenRespaldo() . '\'"';      // Si la foto falla, cambia a la de respaldo (una sola vez).
    if ($loading !== "") {                                                              // Si se pidio un modo de carga...
        $html .= ' loading="' . h($loading) . '"';                                      // ...lo agrega (lazy = carga cuando se acerca en pantalla).
    }                                                                                   // Cierra el if.
    return $html . '>';                                                                 // Cierra la etiqueta y la devuelve.
}                                                                                       // Cierra la funcion.
