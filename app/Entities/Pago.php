<?php                                                                                   // Abre el bloque de codigo PHP.
// Entidad Pago: representa el pago FICTICIO de una reserva (una fila de la tabla pagos).

class Pago                                                                              // Define la clase.
{                                                                                       // Abre la clase.
    public $id;                                                                         // Identificador.
    public $reservaId;                                                                  // Reserva a la que pertenece.
    public $monto;                                                                      // Importe "pagado".
    public $metodo;                                                                     // Metodo (ej: "Tarjeta (simulada)").
    public $ultimos4;                                                                   // Ultimos 4 digitos de la tarjeta.
    public $codigo;                                                                     // Codigo del comprobante.
    public $estado = "aprobado";                                                        // "aprobado" o "reembolsado".

    // Crea un Pago nuevo (todavia sin guardar) con los datos necesarios.
    public static function nuevo($reservaId, $monto, $metodo, $ultimos4, $codigo)       // Recibe los datos del pago.
    {                                                                                   // Abre el metodo.
        $p = new Pago();                                                                // Crea el objeto vacio.
        $p->reservaId = $reservaId;                                                     // Asigna la reserva.
        $p->monto = $monto;                                                             // Asigna el monto.
        $p->metodo = $metodo;                                                           // Asigna el metodo.
        $p->ultimos4 = $ultimos4;                                                       // Asigna los ultimos 4 digitos.
        $p->codigo = $codigo;                                                           // Asigna el codigo.
        return $p;                                                                      // Devuelve el objeto.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
