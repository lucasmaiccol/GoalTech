<?php                                                                                   // Abre el bloque de codigo PHP.
// Implementacion con PDO/MySQL del repositorio de canchas. Unico lugar con SQL sobre la tabla canchas.

class PdoCanchaRepository implements CanchaRepositoryInterface                          // Cumple con la interfaz.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // La guarda.
    }                                                                                   // Cierra el constructor.

    public function listarTodas()                                                       // Todas las canchas.
    {                                                                                   // Abre el metodo.
        $filas = $this->conexion->query("SELECT * FROM canchas ORDER BY id")->fetchAll(); // Trae las filas.
        return array_map(["Cancha", "desdeFila"], $filas);                              // Las convierte en objetos Cancha.
    }                                                                                   // Cierra el metodo.

    public function listarActivas()                                                     // Solo las activas.
    {                                                                                   // Abre el metodo.
        $filas = $this->conexion->query("SELECT * FROM canchas WHERE estado = 'activa' ORDER BY id")->fetchAll(); // Trae las filas activas.
        return array_map(["Cancha", "desdeFila"], $filas);                              // Las convierte en objetos Cancha.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Busca una cancha.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("SELECT * FROM canchas WHERE id = :id");       // Consulta preparada.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta.
        $fila = $stmt->fetch();                                                         // Toma la fila (o false).
        return $fila ? Cancha::desdeFila($fila) : null;                                 // Devuelve una Cancha o null.
    }                                                                                   // Cierra el metodo.

    public function crear(Cancha $cancha)                                               // ALTA.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("INSERT INTO canchas (nombre, tipo, precio_hora, descripcion, imagen, estado) VALUES (:nombre, :tipo, :precio, :descripcion, :imagen, :estado)"); // Consulta preparada.
        return $stmt->execute([":nombre" => $cancha->nombre, ":tipo" => $cancha->tipo, ":precio" => $cancha->precioHora, ":descripcion" => $cancha->descripcion, ":imagen" => $cancha->imagen, ":estado" => $cancha->estado]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function actualizar(Cancha $cancha)                                          // MODIFICACION.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE canchas SET nombre = :nombre, tipo = :tipo, precio_hora = :precio, descripcion = :descripcion, imagen = :imagen, estado = :estado WHERE id = :id"); // Consulta preparada.
        return $stmt->execute([":nombre" => $cancha->nombre, ":tipo" => $cancha->tipo, ":precio" => $cancha->precioHora, ":descripcion" => $cancha->descripcion, ":imagen" => $cancha->imagen, ":estado" => $cancha->estado, ":id" => $cancha->id]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function eliminar($id)                                                       // BAJA.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM canchas WHERE id = :id");         // Consulta preparada.
        return $stmt->execute([":id" => $id]);                                          // La ejecuta (falla si tiene reservas).
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
