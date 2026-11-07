<?php                                                                                   // Abre el bloque de codigo PHP.
// Implementacion con PDO/MySQL del repositorio de usuarios. Es el UNICO lugar que escribe SQL sobre la tabla usuarios.

class PdoUsuarioRepository implements UsuarioRepositoryInterface                        // Cumple con la interfaz.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // La guarda.
    }                                                                                   // Cierra el constructor.

    public function buscarPorEmail($email)                                              // Busca por correo.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1"); // Consulta preparada.
        $stmt->execute([":email" => $email]);                                           // La ejecuta con el correo.
        $fila = $stmt->fetch();                                                         // Toma la fila (o false).
        return $fila ? Usuario::desdeFila($fila) : null;                                // Devuelve un Usuario o null.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Busca por id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE id = :id");      // Consulta preparada.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta con el id.
        $fila = $stmt->fetch();                                                         // Toma la fila (o false).
        return $fila ? Usuario::desdeFila($fila) : null;                                // Devuelve un Usuario o null.
    }                                                                                   // Cierra el metodo.

    public function listarTodos()                                                       // Lista todos los usuarios.
    {                                                                                   // Abre el metodo.
        $filas = $this->conexion->query("SELECT id, nombre, email, telefono, rol, created_at FROM usuarios ORDER BY id")->fetchAll(); // Trae las filas (sin el hash).
        return array_map(["Usuario", "desdeFila"], $filas);                             // Convierte cada fila en un objeto Usuario.
    }                                                                                   // Cierra el metodo.

    public function crear(Usuario $usuario)                                             // ALTA.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (:nombre, :email, :telefono, :password, :rol)"); // Consulta preparada.
        return $stmt->execute([":nombre" => $usuario->nombre, ":email" => $usuario->email, ":telefono" => $usuario->telefono, ":password" => $usuario->passwordHash, ":rol" => $usuario->rol]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function actualizar(Usuario $usuario)                                        // MODIFICACION.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE usuarios SET nombre = :nombre, telefono = :telefono, rol = :rol WHERE id = :id"); // Consulta preparada.
        return $stmt->execute([":nombre" => $usuario->nombre, ":telefono" => $usuario->telefono, ":rol" => $usuario->rol, ":id" => $usuario->id]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function actualizarPassword($id, $passwordHash)                              // Cambia la contraseña.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE usuarios SET password = :password WHERE id = :id"); // Consulta preparada.
        return $stmt->execute([":password" => $passwordHash, ":id" => $id]);            // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function eliminar($id)                                                       // BAJA.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id = :id");        // Consulta preparada.
        return $stmt->execute([":id" => $id]);                                          // La ejecuta (falla si tiene reservas).
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
