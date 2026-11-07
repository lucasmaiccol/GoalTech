<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador del cliente: dashboard, pago simulado y cancelacion de reservas propias.

class ClienteController extends Controlador                                             // Hereda las herramientas de Controlador.
{                                                                                       // Abre la clase.
    private $reservas;                                                                  // Servicio de reservas.
    private $canchas;                                                                   // Servicio de canchas.

    public function __construct(ReservaService $reservas, CanchaService $canchas)       // Recibe los servicios.
    {                                                                                   // Abre el constructor.
        $this->reservas = $reservas;                                                    // Guarda el servicio de reservas.
        $this->canchas = $canchas;                                                      // Guarda el servicio de canchas.
    }                                                                                   // Cierra el constructor.

    public function dashboard()                                                         // Panel del cliente.
    {                                                                                   // Abre el metodo.
        Sesion::borrar("reserva_pendiente");                                            // Si habia un pago a medias, lo descarta.
        $this->render("cliente/dashboard", [                                            // Muestra la plantilla con estos datos:
            "canchas" => $this->canchas->listarActivas(),                               // Canchas que se pueden reservar.
            "horas" => $this->reservas->horasDisponibles(),                             // Horarios disponibles.
            "reservas" => $this->reservas->listarPorUsuario($this->usuario->id),        // Reservas de este usuario.
            "ahora" => date("Y-m-d H:i:s"),                                             // Fecha y hora actual (para saber si una reserva ya empezo).
            "hoy" => date("Y-m-d"),                                                     // Fecha de hoy (minimo del selector de fecha).
        ]);                                                                             // Cierra los datos.
    }                                                                                   // Cierra el metodo.

    // PASO 1 del pago: viene del dashboard con cancha, fecha y hora.
    public function pagoIniciar()                                                       // No recibe parametros.
    {                                                                                   // Abre el metodo.
        if (!$this->esPost()) {                                                         // Si no vino de un formulario...
            redirigir("dashboard");                                                     // ...vuelve al dashboard.
        }                                                                               // Cierra el if.
        $canchaId = (int)$this->post("cancha_id");                                      // Id de la cancha elegida.
        $fecha = $this->post("dia_reserva");                                            // Fecha elegida.
        $hora = $this->post("turno_reserva");                                           // Hora elegida.
        $validacion = $this->reservas->validarSolicitud($canchaId, $fecha, $hora);      // Verifica que se pueda reservar eso.
        if ($validacion["error"] === "") {                                              // Si el turno se puede reservar...
            $validacion["error"] = $this->reservas->validarLimiteReservas($this->usuario->id); // ...verifica tambien que no haya llegado al maximo de reservas activas.
        }                                                                               // Cierra el if.
        if ($validacion["error"] !== "") {                                              // Si no se puede...
            Sesion::mensaje("error", $validacion["error"]);                             // ...guarda el error...
            redirigir("dashboard");                                                     // ...y vuelve al dashboard.
        }                                                                               // Cierra el if.
        Sesion::guardar("reserva_pendiente", ["cancha_id" => $canchaId, "fecha" => $fecha, "hora" => $hora]); // Guarda la reserva "en espera de pago" (todavia NO esta en la base).
        redirigir("pago");                                                              // Pasa a la pantalla de pago.
    }                                                                                   // Cierra el metodo.

    // PASO 2 del pago: muestra el formulario de tarjeta y procesa el pago simulado.
    public function pago()                                                              // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $pendiente = Sesion::leer("reserva_pendiente");                                 // Lee la reserva en espera.
        if (!$pendiente) {                                                              // Si no hay nada para pagar...
            redirigir("dashboard");                                                     // ...vuelve al dashboard.
        }                                                                               // Cierra el if.

        $errorPago = "";                                                                // Error del formulario de tarjeta.
        if ($this->esPost()) {                                                          // Si envio los datos de la tarjeta...
            $errorPago = $this->reservas->validarTarjeta($this->post("pg_titular"), $this->post("pg_numero"), $this->post("pg_venc"), $this->post("pg_codigo")); // Valida la tarjeta ficticia.
            if ($errorPago === "") {                                                    // Si la tarjeta esta bien...
                $resultado = $this->reservas->confirmarReservaConPago($this->usuario->id, $pendiente["cancha_id"], $pendiente["fecha"], $pendiente["hora"], $this->post("pg_numero")); // ...registra reserva + pago.
                Sesion::borrar("reserva_pendiente");                                    // Borra la reserva en espera (ya se proceso).
                $this->avisar($resultado);                                              // Guarda el mensaje de resultado.
                redirigir("dashboard");                                                 // Vuelve al dashboard.
            }                                                                           // Cierra el if (si hubo error, sigue y muestra el formulario).
        }                                                                               // Cierra el if del POST.

        $validacion = $this->reservas->validarSolicitud($pendiente["cancha_id"], $pendiente["fecha"], $pendiente["hora"]); // Revalida: alguien pudo reservar ese turno mientras tanto.
        if ($validacion["error"] !== "") {                                              // Si ya no se puede...
            Sesion::borrar("reserva_pendiente");                                        // ...descarta la reserva en espera...
            Sesion::mensaje("error", $validacion["error"]);                             // ...guarda el motivo...
            redirigir("dashboard");                                                     // ...y vuelve al dashboard.
        }                                                                               // Cierra el if.
        $this->render("cliente/pago", [                                                 // Muestra la plantilla con estos datos:
            "cancha" => $validacion["cancha"],                                          // La cancha elegida.
            "pendiente" => $pendiente,                                                  // Fecha y hora elegidas.
            "horaFin" => date("H:i", strtotime($pendiente["hora"] . " +1 hour")),       // Hora de fin (1 hora despues).
            "errorPago" => $errorPago,                                                  // Error de la tarjeta (si hay).
        ]);                                                                             // Cierra los datos.
    }                                                                                   // Cierra el metodo.

    // Cancela una reserva PROPIA (el servicio verifica que sea del usuario logueado).
    public function cancelar()                                                          // No recibe parametros.
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo por formulario.
            $this->avisar($this->reservas->cancelarPropia((int)$this->post("reserva_id"), $this->usuario->id)); // Pide cancelar y guarda el mensaje.
        }                                                                               // Cierra el if.
        redirigir("dashboard");                                                         // Vuelve al dashboard.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
