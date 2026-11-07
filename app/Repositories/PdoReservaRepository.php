<?php                                                                                   // Abre el bloque de codigo PHP.
// Implementacion con PDO/MySQL del repositorio de reservas. Unico lugar con SQL sobre la tabla reservas.

class PdoReservaRepository implements ReservaRepositoryInterface                        // Cumple con la interfaz.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO.

    // Consulta base: une la reserva con su cancha, su usuario y su pago para mostrar todo junto.
    private $consultaBase = "SELECT r.*, c.nombre AS cancha_nombre, c.tipo AS cancha_tipo,
                                    u.nombre AS usuario_nombre, u.email AS usuario_email,
                                    p.codigo AS pago_codigo, p.estado AS pago_estado
                             FROM reservas r
                             JOIN canchas c ON c.id = r.cancha_id
                             JOIN usuarios u ON u.id = r.usuario_id
                             LEFT JOIN pagos p ON p.reserva_id = r.id";                 // LEFT JOIN: la reserva sale aunque no tenga pago.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // La guarda.
    }                                                                                   // Cierra el constructor.

    public function listarPorUsuario($usuarioId)                                        // Reservas de un usuario.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare($this->consultaBase . " WHERE r.usuario_id = :uid ORDER BY r.fecha DESC, r.hora_inicio DESC"); // Filtra y ordena.
        $stmt->execute([":uid" => $usuarioId]);                                         // La ejecuta.
        return array_map(["Reserva", "desdeFila"], $stmt->fetchAll());                  // Devuelve objetos Reserva.
    }                                                                                   // Cierra el metodo.

    public function listarTodas()                                                       // Todas las reservas.
    {                                                                                   // Abre el metodo.
        $filas = $this->conexion->query($this->consultaBase . " ORDER BY r.fecha DESC, r.hora_inicio DESC")->fetchAll(); // Trae todas.
        return array_map(["Reserva", "desdeFila"], $filas);                             // Devuelve objetos Reserva.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Busca una reserva.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare($this->consultaBase . " WHERE r.id = :id");    // Consulta preparada.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta.
        $fila = $stmt->fetch();                                                         // Toma la fila (o false).
        return $fila ? Reserva::desdeFila($fila) : null;                                // Devuelve una Reserva o null.
    }                                                                                   // Cierra el metodo.

    // Pregunta si ya hay una reserva CONFIRMADA en ese turno (para dar un mensaje amable antes de que falle el indice unico).
    public function hayConflicto($canchaId, $fecha, $horaInicio, $excluirId = 0)        // Recibe cancha, fecha, hora e id a ignorar.
    {                                                                                   // Abre el metodo.
        $sql = "SELECT COUNT(*) FROM reservas WHERE cancha_id = :cancha AND fecha = :fecha AND hora_inicio = :hora AND estado = 'confirmada' AND id <> :excluir"; // Cuenta reservas confirmadas en ese turno.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        $stmt->execute([":cancha" => $canchaId, ":fecha" => $fecha, ":hora" => $horaInicio, ":excluir" => $excluirId]); // La ejecuta.
        return $stmt->fetchColumn() > 0;                                                // true si hay al menos una.
    }                                                                                   // Cierra el metodo.

    // Cuenta las reservas confirmadas de un usuario cuyo turno todavia no empezo ($ahora = "AAAA-MM-DD HH:MM:SS").
    public function contarFuturasConfirmadas($usuarioId, $ahora)                        // Recibe el usuario y la fecha/hora actual.
    {                                                                                   // Abre el metodo.
        $sql = "SELECT COUNT(*) FROM reservas WHERE usuario_id = :usuario AND estado = 'confirmada' AND CONCAT(fecha, ' ', hora_inicio) > :ahora"; // Cuenta las confirmadas que aun no empezaron.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        $stmt->execute([":usuario" => $usuarioId, ":ahora" => $ahora]);                 // La ejecuta.
        return (int)$stmt->fetchColumn();                                               // Devuelve la cantidad.
    }                                                                                   // Cierra el metodo.

    public function crear(Reserva $reserva)                                             // ALTA.
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, monto, estado) VALUES (:usuario, :cancha, :fecha, :inicio, :fin, :monto, :estado)"; // Consulta preparada.
        $stmt = $this->conexion->prepare($sql);                                         // La prepara.
        $stmt->execute([":usuario" => $reserva->usuarioId, ":cancha" => $reserva->canchaId, ":fecha" => $reserva->fecha, ":inicio" => $reserva->horaInicio, ":fin" => $reserva->horaFin, ":monto" => $reserva->monto, ":estado" => $reserva->estado]); // La ejecuta (si el turno esta ocupado, el indice UNIQUE lanza un error 1062).
        return (int)$this->conexion->lastInsertId();                                    // Devuelve el id nuevo.
    }                                                                                   // Cierra el metodo.

    public function actualizar(Reserva $reserva)                                        // MODIFICACION.
    {                                                                                   // Abre el metodo.
        $sql = "UPDATE reservas SET cancha_id = :cancha, fecha = :fecha, hora_inicio = :inicio, hora_fin = :fin, estado = :estado WHERE id = :id"; // Consulta preparada.
        $stmt = $this->conexion->prepare($sql);                                         // La prepara.
        return $stmt->execute([":cancha" => $reserva->canchaId, ":fecha" => $reserva->fecha, ":inicio" => $reserva->horaInicio, ":fin" => $reserva->horaFin, ":estado" => $reserva->estado, ":id" => $reserva->id]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function cancelar($id)                                                       // Cancela (no borra).
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = :id"); // Consulta preparada.
        return $stmt->execute([":id" => $id]);                                          // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function eliminar($id)                                                       // BAJA definitiva.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM reservas WHERE id = :id");        // Consulta preparada.
        return $stmt->execute([":id" => $id]);                                          // La ejecuta (su pago se borra solo por ON DELETE CASCADE).
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
