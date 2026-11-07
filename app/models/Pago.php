<?php                                                                                   // Abre el bloque de codigo PHP.
// Modelo Pago: toda consulta a la tabla "pagos" pasa por aca.
// IMPORTANTE: los pagos son FICTICIOS, solo se simulan para el proyecto.

class Pago                                                                              // Define la clase Pago.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Guarda la conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // Guarda la conexion dentro del objeto.
    }                                                                                   // Cierra el constructor.

    // Registra el pago de una reserva.
    public function crear($reservaId, $monto, $metodo, $ultimos4, $codigo)              // Recibe los datos del pago.
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO pagos (reserva_id, monto, metodo, ultimos4, codigo, estado)
                VALUES (:reserva, :monto, :metodo, :ultimos4, :codigo, 'aprobado')";    // Inserta el pago como "aprobado".
        $stmt = $this->conexion->prepare($sql);                                         // Prepara la consulta.
        return $stmt->execute([":reserva" => $reservaId, ":monto" => $monto, ":metodo" => $metodo,
                               ":ultimos4" => $ultimos4, ":codigo" => $codigo]);        // La ejecuta con los valores.
    }                                                                                   // Cierra el metodo.

    // Marca el pago como reembolsado (cuando se cancela la reserva).
    public function reembolsar($reservaId)                                              // Recibe el id de la reserva.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE pagos SET estado = 'reembolsado' WHERE reserva_id = :reserva"); // Prepara el UPDATE.
        return $stmt->execute([":reserva" => $reservaId]);                              // Lo ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
