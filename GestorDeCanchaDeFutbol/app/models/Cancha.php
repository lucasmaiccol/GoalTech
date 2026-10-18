<?php                                                                                   // Abre el bloque de codigo PHP.
// Modelo Cancha: toda consulta a la tabla "canchas" pasa por aca.

class Cancha                                                                            // Define la clase Cancha.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Guarda la conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // Guarda la conexion dentro del objeto.
    }                                                                                   // Cierra el constructor.

    // Devuelve todas las canchas (para el panel de admin).
    public function listarTodas()                                                       // No recibe parametros.
    {                                                                                   // Abre el metodo.
        return $this->conexion->query("SELECT * FROM canchas ORDER BY id")->fetchAll(); // Ejecuta la consulta y devuelve todas las filas.
    }                                                                                   // Cierra el metodo.

    // Devuelve solo las canchas activas (las que el cliente puede reservar).
    public function listarActivas()                                                     // No recibe parametros.
    {                                                                                   // Abre el metodo.
        return $this->conexion->query("SELECT * FROM canchas WHERE estado = 'activa' ORDER BY nombre")->fetchAll(); // Solo estado "activa".
    }                                                                                   // Cierra el metodo.

    // Busca una cancha por id.
    public function buscarPorId($id)                                                    // Recibe el id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT * FROM canchas WHERE id = :id");       // Prepara la consulta.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta con el id.
        return $stmt->fetch();                                                          // Devuelve la fila o false.
    }                                                                                   // Cierra el metodo.

    // ALTA: crea una cancha nueva.
    public function crear($nombre, $tipo, $precioHora, $estado, $descripcion, $imagen)  // Recibe los datos de la cancha (descripcion e imagen pueden ser null).
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO canchas (nombre, tipo, precio_hora, descripcion, imagen, estado)
                VALUES (:nombre, :tipo, :precio, :descripcion, :imagen, :estado)";      // Consulta de insercion.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([":nombre" => $nombre, ":tipo" => $tipo, ":precio" => $precioHora,
                               ":descripcion" => $descripcion, ":imagen" => $imagen, ":estado" => $estado]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION: actualiza los datos de una cancha.
    public function actualizar($id, $nombre, $tipo, $precioHora, $estado, $descripcion, $imagen) // Recibe el id y los datos nuevos.
    {                                                                                   // Abre el metodo.
        $sql = "UPDATE canchas SET nombre = :nombre, tipo = :tipo, precio_hora = :precio,
                descripcion = :descripcion, imagen = :imagen, estado = :estado WHERE id = :id"; // Actualiza esa cancha.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([":nombre" => $nombre, ":tipo" => $tipo, ":precio" => $precioHora,
                               ":descripcion" => $descripcion, ":imagen" => $imagen, ":estado" => $estado, ":id" => $id]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    // BAJA: elimina una cancha. Falla si tiene reservas asociadas.
    public function eliminar($id)                                                       // Recibe el id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM canchas WHERE id = :id");         // Prepara el DELETE.
        return $stmt->execute([":id" => $id]);                                          // Lo ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
