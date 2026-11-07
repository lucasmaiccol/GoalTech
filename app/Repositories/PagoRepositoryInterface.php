<?php                                                                                   // Abre el bloque de codigo PHP.
// Interfaz: operaciones que debe tener cualquier repositorio de pagos.

interface PagoRepositoryInterface                                                       // Define la interfaz.
{                                                                                       // Abre la interfaz.
    public function crear(Pago $pago);                                                  // Registra un pago.
    public function reembolsar($reservaId);                                             // Pasa el pago de la reserva a "reembolsado".
    public function aprobar($reservaId);                                                // Pasa el pago de la reserva a "aprobado" (al reactivar una reserva).
}                                                                                       // Cierra la interfaz.
