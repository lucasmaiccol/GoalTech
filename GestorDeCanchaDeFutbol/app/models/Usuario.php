<?php                                                                                   // Abre el bloque de codigo PHP.
// Modelo Usuario: toda consulta a la tabla "usuarios" pasa por aca.
// Ni las vistas ni los controladores escriben SQL directamente.

class Usuario                                                                           // Define la clase Usuario.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Guarda la conexion PDO para usarla en los metodos.

    public function __construct($conexion)                                              // Constructor: se ejecuta al hacer "new Usuario($conexion)".
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // Guarda la conexion recibida dentro del objeto.
    }                                                                                   // Cierra el constructor.

    // Busca un usuario por email. Devuelve sus datos (con el hash) o false si no existe.
    public function buscarPorEmail($email)                                              // Recibe el email a buscar.
    {                                                                                   // Abre el metodo.
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";                   // Consulta con un marcador :email (consulta preparada).
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta (evita SQL injection).
        $stmt->execute([":email" => $email]);                                           // La ejecuta reemplazando :email por el valor real.
        return $stmt->fetch();                                                          // Devuelve la fila encontrada o false.
    }                                                                                   // Cierra el metodo.

    // Busca un usuario por su id.
    public function buscarPorId($id)                                                    // Recibe el id a buscar.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE id = :id");      // Prepara la consulta.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta con el id.
        return $stmt->fetch();                                                          // Devuelve la fila o false.
    }                                                                                   // Cierra el metodo.

    // Lista todos los usuarios (sin el hash de la contraseña, no hace falta mostrarlo).
    public function listarTodos()                                                       // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $sql = "SELECT id, nombre, email, telefono, rol, created_at FROM usuarios ORDER BY id"; // Trae las columnas utiles ordenadas por id.
        return $this->conexion->query($sql)->fetchAll();                                // Ejecuta y devuelve todas las filas.
    }                                                                                   // Cierra el metodo.

    // ALTA: inserta un usuario nuevo. La contraseña ya debe venir hasheada.
    public function crear($nombre, $email, $telefono, $passwordHash, $rol = "cliente")  // Si no se indica rol, es "cliente".
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO usuarios (nombre, email, telefono, password, rol)
                VALUES (:nombre, :email, :telefono, :password, :rol)";                  // Consulta de insercion con marcadores.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([                                                         // La ejecuta y devuelve true/false.
            ":nombre" => $nombre,                                                       // Valor para :nombre.
            ":email" => $email,                                                         // Valor para :email.
            ":telefono" => $telefono,                                                   // Valor para :telefono.
            ":password" => $passwordHash,                                               // Valor para :password (el hash).
            ":rol" => $rol                                                              // Valor para :rol.
        ]);                                                                             // Cierra el array y la llamada.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION: cambia nombre, telefono y rol de un usuario.
    public function actualizar($id, $nombre, $telefono, $rol)                           // Recibe el id y los datos nuevos.
    {                                                                                   // Abre el metodo.
        $sql = "UPDATE usuarios SET nombre = :nombre, telefono = :telefono, rol = :rol WHERE id = :id"; // Actualiza solo ese usuario.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([":nombre" => $nombre, ":telefono" => $telefono, ":rol" => $rol, ":id" => $id]); // La ejecuta con los valores.
    }                                                                                   // Cierra el metodo.

    // Cambia solo la contraseña (recibe el hash nuevo).
    public function actualizarPassword($id, $passwordHash)                              // Recibe el id y el hash nuevo.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE usuarios SET password = :password WHERE id = :id"); // Prepara la consulta.
        return $stmt->execute([":password" => $passwordHash, ":id" => $id]);           // La ejecuta.
    }                                                                                   // Cierra el metodo.

    // BAJA: elimina un usuario. Falla si tiene reservas (por la clave foranea).
    public function eliminar($id)                                                       // Recibe el id a borrar.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id = :id");        // Prepara el DELETE.
        return $stmt->execute([":id" => $id]);                                          // Lo ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
