<?php                                                                                   // Abre el bloque de codigo PHP.
// Implementacion con PDO/MySQL del repositorio de intentos de login fallidos.

class PdoIntentoLoginRepository implements IntentoLoginRepositoryInterface              // Cumple con la interfaz.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // La guarda.
    }                                                                                   // Cierra el constructor.

    public function registrar($email, $ip)                                              // Guarda un intento fallido.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("INSERT INTO intentos_login (email, ip) VALUES (:email, :ip)"); // Consulta preparada.
        $stmt->execute([":email" => $email, ":ip" => $ip]);                             // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function contarPorEmail($email, $minutos)                                    // Intentos de un correo en los ultimos minutos.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM intentos_login WHERE email = :email AND fecha > (NOW() - INTERVAL :minutos MINUTE)"); // Cuenta los intentos recientes.
        $stmt->execute([":email" => $email, ":minutos" => (int)$minutos]);             // La ejecuta.
        return (int)$stmt->fetchColumn();                                               // Devuelve la cantidad.
    }                                                                                   // Cierra el metodo.

    public function contarPorIp($ip, $minutos)                                          // Intentos de una IP en los ultimos minutos.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM intentos_login WHERE ip = :ip AND fecha > (NOW() - INTERVAL :minutos MINUTE)"); // Cuenta los intentos recientes.
        $stmt->execute([":ip" => $ip, ":minutos" => (int)$minutos]);                   // La ejecuta.
        return (int)$stmt->fetchColumn();                                               // Devuelve la cantidad.
    }                                                                                   // Cierra el metodo.

    public function limpiarAntiguos()                                                   // Borra los intentos viejos.
    {                                                                                   // Abre el metodo.
        $this->conexion->exec("DELETE FROM intentos_login WHERE fecha < (NOW() - INTERVAL 1 DAY)"); // Elimina los de mas de 1 dia.
    }                                                                                   // Cierra el metodo.

    public function limpiar($email)                                                     // Borra los intentos de un correo.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM intentos_login WHERE email = :email"); // Consulta preparada.
        $stmt->execute([":email" => $email]);                                           // La ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
