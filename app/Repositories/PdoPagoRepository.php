<?php                                                                                   // Abre el bloque de codigo PHP.
// Implementacion con PDO/MySQL del repositorio de pagos. Unico lugar con SQL sobre la tabla pagos.

class PdoPagoRepository implements PagoRepositoryInterface                              // Cumple con la interfaz.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // La guarda.
    }                                                                                   // Cierra el constructor.

    public function crear(Pago $pago)                                                   // Registra un pago.
    {                                                                                   // Abre el metodo.
        $sql = "INSERT INTO pagos (reserva_id, monto, metodo, ultimos4, codigo, estado) VALUES (:reserva, :monto, :metodo, :ultimos4, :codigo, 'aprobado')"; // Consulta preparada.
        $stmt = $this->conexion->prepare($sql);                                         // La prepara.
        return $stmt->execute([":reserva" => $pago->reservaId, ":monto" => $pago->monto, ":metodo" => $pago->metodo, ":ultimos4" => $pago->ultimos4, ":codigo" => $pago->codigo]); // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function reembolsar($reservaId)                                              // Pago => reembolsado.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE pagos SET estado = 'reembolsado' WHERE reserva_id = :reserva"); // Consulta preparada.
        return $stmt->execute([":reserva" => $reservaId]);                              // La ejecuta.
    }                                                                                   // Cierra el metodo.

    public function aprobar($reservaId)                                                 // Pago => aprobado.
    {                                                                                   // Abre el metodo.
        $stmt = $this->conexion->prepare("UPDATE pagos SET estado = 'aprobado' WHERE reserva_id = :reserva"); // Consulta preparada.
        return $stmt->execute([":reserva" => $reservaId]);                              // La ejecuta.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
