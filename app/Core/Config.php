<?php                                                                                   // Abre el bloque de codigo PHP.
// Config: lee el archivo config/config.php (que NO se sube a GitHub) y entrega sus valores.

class Config                                                                            // Define la clase Config.
{                                                                                       // Abre la clase.
    private static $datos = null;                                                       // Aca se guarda la configuracion una vez leida.

    // Devuelve un valor de la configuracion. Ej: Config::get("bd") devuelve el array de la base de datos.
    public static function get($clave)                                                  // Recibe el nombre de la seccion.
    {                                                                                   // Abre el metodo.
        if (self::$datos === null) {                                                    // Si todavia no se leyo el archivo...
            $archivo = __DIR__ . "/../../config/config.php";                            // Ruta del archivo de configuracion.
            if (!is_file($archivo)) {                                                   // Si no existe...
                throw new RuntimeException("Falta config/config.php (copiar config.example.php)"); // ...avisa que hay que crearlo.
            }                                                                           // Cierra el if.
            self::$datos = require $archivo;                                            // Lee el archivo (devuelve un array).
        }                                                                               // Cierra el if.
        return self::$datos[$clave];                                                    // Devuelve la seccion pedida.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
