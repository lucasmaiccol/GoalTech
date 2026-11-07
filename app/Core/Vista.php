<?php                                                                                   // Abre el bloque de codigo PHP.
// Vista: muestra una plantilla HTML (app/Views/...). Las plantillas NO tienen logica de negocio, solo muestran datos.

class Vista                                                                             // Define la clase Vista.
{                                                                                       // Abre la clase.
    private static $compartidos = [];                                                   // Datos disponibles en TODAS las vistas (ej: el usuario logueado).

    public static function compartir($nombre, $valor)                                   // Agrega un dato para todas las vistas.
    {                                                                                   // Abre el metodo.
        self::$compartidos[$nombre] = $valor;                                           // Lo guarda.
    }                                                                                   // Cierra el metodo.

    // Muestra la plantilla pedida. $datos son las variables que la plantilla puede usar.
    public static function render($vista, $datos = [])                                  // Recibe el nombre de la plantilla y sus datos.
    {                                                                                   // Abre el metodo.
        extract(array_merge(self::$compartidos, $datos));                               // Convierte cada dato en una variable ($datos["x"] pasa a ser $x).
        require __DIR__ . "/../Views/" . $vista . ".php";                               // Carga la plantilla.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
