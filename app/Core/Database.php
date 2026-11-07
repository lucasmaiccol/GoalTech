<?php                                                                                   // Abre el bloque de codigo PHP.
// Database: crea UNA sola conexion PDO a MySQL y la reutiliza en todo el programa.

class Database                                                                          // Define la clase Database.
{                                                                                       // Abre la clase.
    private static $conexion = null;                                                    // Aca se guarda la conexion una vez creada.

    public static function conexion()                                                   // Devuelve la conexion (la crea la primera vez).
    {                                                                                   // Abre el metodo.
        if (self::$conexion === null) {                                                 // Si todavia no hay conexion...
            $c = Config::get("bd");                                                     // Lee los datos de la base desde la configuracion.
            try {                                                                       // Intenta conectarse.
                self::$conexion = new PDO(                                              // Crea la conexion PDO.
                    "mysql:host=" . $c["host"] . ";dbname=" . $c["nombre"] . ";charset=" . $c["charset"], // Servidor, base y codificacion.
                    $c["usuario"],                                                      // Usuario de MySQL.
                    $c["password"],                                                     // Contraseña de MySQL.
                    [                                                                   // Opciones de PDO.
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,                    // Si una consulta falla, lanza una excepcion.
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,               // Las filas vuelven como arrays con el nombre de la columna.
                        PDO::ATTR_EMULATE_PREPARES => false,                            // Consultas preparadas reales (protege de SQL injection).
                    ]                                                                   // Cierra las opciones.
                );                                                                      // Cierra la creacion de PDO.
            } catch (PDOException $e) {                                                 // Si no se pudo conectar...
                error_log("GoalTech - error de conexion: " . $e->getMessage());        // ...guarda el detalle tecnico en el log del servidor (el usuario NO lo ve)...
                throw new RuntimeException("Sin conexion a la base de datos");          // ...y lanza un error generico.
            }                                                                           // Cierra el catch.
        }                                                                               // Cierra el if.
        return self::$conexion;                                                         // Devuelve la conexion.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
