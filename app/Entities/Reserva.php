<?php                                                                                   // Abre el bloque de codigo PHP.
// Entidad Reserva: representa UNA reserva (una fila de la tabla reservas, con algunos datos extra para mostrar).

class Reserva                                                                           // Define la clase.
{                                                                                       // Abre la clase.
    public $id;                                                                         // Identificador.
    public $usuarioId;                                                                  // Quien reservo.
    public $canchaId;                                                                   // Que cancha.
    public $fecha;                                                                      // Dia (AAAA-MM-DD).
    public $horaInicio;                                                                 // Hora de inicio (HH:MM:SS).
    public $horaFin;                                                                    // Hora de fin (HH:MM:SS).
    public $monto;                                                                      // Precio cobrado.
    public $estado = "confirmada";                                                      // "confirmada" o "cancelada".
    // Datos extra que vienen de otras tablas (solo para mostrar en pantalla):
    public $canchaNombre;                                                               // Nombre de la cancha.
    public $canchaTipo;                                                                 // Tipo de la cancha.
    public $usuarioNombre;                                                              // Nombre del cliente.
    public $usuarioEmail;                                                               // Correo del cliente.
    public $pagoCodigo;                                                                 // Codigo del comprobante de pago.
    public $pagoEstado;                                                                 // Estado del pago.

    // Crea una Reserva a partir de una fila de la base de datos.
    public static function desdeFila($fila)                                             // Recibe el array de la fila.
    {                                                                                   // Abre el metodo.
        $r = new Reserva();                                                             // Crea el objeto vacio.
        $r->id = (int)$fila["id"];                                                      // Copia el id.
        $r->usuarioId = (int)$fila["usuario_id"];                                       // Copia el usuario.
        $r->canchaId = (int)$fila["cancha_id"];                                         // Copia la cancha.
        $r->fecha = $fila["fecha"];                                                     // Copia la fecha.
        $r->horaInicio = $fila["hora_inicio"];                                          // Copia la hora de inicio.
        $r->horaFin = $fila["hora_fin"];                                                // Copia la hora de fin.
        $r->monto = $fila["monto"];                                                     // Copia el monto.
        $r->estado = $fila["estado"];                                                   // Copia el estado.
        $r->canchaNombre = $fila["cancha_nombre"] ?? null;                              // Copia el nombre de la cancha (si vino).
        $r->canchaTipo = $fila["cancha_tipo"] ?? null;                                  // Copia el tipo de la cancha (si vino).
        $r->usuarioNombre = $fila["usuario_nombre"] ?? null;                            // Copia el nombre del cliente (si vino).
        $r->usuarioEmail = $fila["usuario_email"] ?? null;                              // Copia el correo del cliente (si vino).
        $r->pagoCodigo = $fila["pago_codigo"] ?? null;                                  // Copia el codigo de pago (si vino).
        $r->pagoEstado = $fila["pago_estado"] ?? null;                                  // Copia el estado del pago (si vino).
        return $r;                                                                      // Devuelve el objeto.
    }                                                                                   // Cierra el metodo.

    public function estaConfirmada()                                                    // Pregunta si la reserva esta vigente.
    {                                                                                   // Abre el metodo.
        return $this->estado === "confirmada";                                          // true si esta confirmada.
    }                                                                                   // Cierra el metodo.

    public function inicio()                                                            // Fecha y hora de inicio juntas ("AAAA-MM-DD HH:MM:SS").
    {                                                                                   // Abre el metodo.
        return $this->fecha . " " . $this->horaInicio;                                  // Une fecha y hora.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
