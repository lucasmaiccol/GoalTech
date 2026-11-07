<?php                                                                                   // Abre el bloque de codigo PHP.
// Modelo Reserva: toda consulta a la tabla "reservas" pasa por aca.

class Reserva                                                                           // Define la clase Reserva.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Guarda la conexion PDO.

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
        $this->conexion = $conexion;                                                    // Guarda la conexion dentro del objeto.
    }                                                                                   // Cierra el constructor.

    // Reservas de un usuario (para su dashboard), las mas recientes primero.
    public function listarPorUsuario($usuarioId)                                        // Recibe el id del usuario.
    {                                                                                   // Abre el metodo.
        $sql = $this->consultaBase . " WHERE r.usuario_id = :uid ORDER BY r.fecha DESC, r.hora_inicio DESC"; // Filtra por usuario y ordena.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        $stmt->execute([":uid" => $usuarioId]);                                         // La ejecuta con el id.
        return $stmt->fetchAll();                                                       // Devuelve todas las filas.
    }                                                                                   // Cierra el metodo.

    // Todas las reservas del sistema (para el panel de admin).
    public function listarTodas()                                                       // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $sql = $this->consultaBase . " ORDER BY r.fecha DESC, r.hora_inicio DESC";      // Sin filtro, ordenadas por fecha.
        return $this->conexion->query($sql)->fetchAll();                                // Ejecuta y devuelve todas las filas.
    }                                                                                   // Cierra el metodo.

    // Busca una reserva por id.
    public function buscarPorId($id)                                                    // Recibe el id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare($this->consultaBase . " WHERE r.id = :id");    // Prepara la consulta base filtrada por id.
        $stmt->execute([":id" => $id]);                                                 // La ejecuta.
        return $stmt->fetch();                                                          // Devuelve la fila o false.
    }                                                                                   // Cierra el metodo.

    // Devuelve true si ya hay una reserva CONFIRMADA en esa cancha, fecha y hora.
    // $excluirId sirve al editar: para que una reserva no choque consigo misma.
    public function hayConflicto($canchaId, $fecha, $horaInicio, $excluirId = 0)        // Recibe cancha, fecha, hora y (opcional) id a ignorar.
    {                                                                                   // Abre el metodo.
        $sql = "SELECT COUNT(*) FROM reservas
                WHERE cancha_id = :cancha AND fecha = :fecha AND hora_inicio = :hora
                AND estado = 'confirmada' AND id <> :excluir";                          // Cuenta reservas confirmadas en ese mismo turno.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        $stmt->execute([":cancha" => $canchaId, ":fecha" => $fecha, ":hora" => $horaInicio, ":excluir" => $excluirId]); // La ejecuta.
        return $stmt->fetchColumn() > 0;                                                // Si la cuenta es mayor a 0, hay conflicto.
    }                                                                                   // Cierra el metodo.

    // ALTA: inserta la reserva y devuelve el id nuevo.
    public function crear($usuarioId, $canchaId, $fecha, $horaInicio, $horaFin, $monto) // Recibe todos los datos de la reserva.
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, monto, estado)
                VALUES (:usuario, :cancha, :fecha, :inicio, :fin, :monto, 'confirmada')"; // Toda reserva nace confirmada (porque ya se pago).
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        $stmt->execute([":usuario" => $usuarioId, ":cancha" => $canchaId, ":fecha" => $fecha,
                        ":inicio" => $horaInicio, ":fin" => $horaFin, ":monto" => $monto]); // La ejecuta con los valores.
        return $this->conexion->lastInsertId();                                         // Devuelve el id que MySQL le asigno.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION: cambia cancha, fecha, horario y estado de una reserva.
    public function actualizar($id, $canchaId, $fecha, $horaInicio, $horaFin, $estado)  // Recibe el id y los datos nuevos.
    {                                                                                   // Abre el metodo.
        $sql = "UPDATE reservas SET cancha_id = :cancha, fecha = :fecha, hora_inicio = :inicio,
                hora_fin = :fin, estado = :estado WHERE id = :id";                      // Actualiza solo esa reserva.
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([":cancha" => $canchaId, ":fecha" => $fecha, ":inicio" => $horaInicio,
                               ":fin" => $horaFin, ":estado" => $estado, ":id" => $id]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    // Marca la reserva como cancelada (no la borra, queda en el historial).
    public function cancelar($id)                                                       // Recibe el id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = :id"); // Prepara el UPDATE.
        return $stmt->execute([":id" => $id]);                                          // Lo ejecuta.
    }                                                                                   // Cierra el metodo.

    // BAJA definitiva: borra la reserva (su pago se borra solo por ON DELETE CASCADE).
    public function eliminar($id)                                                       // Recibe el id.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("DELETE FROM reservas WHERE id = :id");        // Prepara el DELETE.
        return $stmt->execute([":id" => $id]);                                          // Lo ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
