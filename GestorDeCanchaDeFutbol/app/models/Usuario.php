<?php
// Modelo Usuario: toda consulta a la tabla "usuarios" pasa por aca.
// Ni la vista (login.php) ni el controlador escriben SQL directamente.

class Usuario
{
    private $conexion;
    // Guarda la conexion PDO para usarla en los metodos de abajo.

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Busca un usuario por email. Devuelve el array con sus datos
    // (incluido el hash de la contraseña) o false si no existe.
    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([":email" => $email]);

        // fetch() con FETCH_ASSOC trae la fila como array asociativo,
        // por ejemplo: ["id" => 1, "nombre" => "Lucas", "email" => "...", ...]
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
