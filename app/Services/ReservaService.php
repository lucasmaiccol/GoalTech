<?php                                                                                   // Abre el bloque de codigo PHP.
// Servicio de reservas: las reglas del negocio ("reservar y pagar", "cancelar y reembolsar", etc.).
// No lee $_POST ni escribe SQL: recibe datos simples, usa repositorios y devuelve resultados.

class ReservaService                                                                    // Define la clase.
{                                                                                       // Abre la clase.
    private $reservas;                                                                  // Repositorio de reservas.
    private $canchas;                                                                   // Repositorio de canchas.
    private $pagos;                                                                     // Repositorio de pagos.
    private $usuarios;                                                                  // Repositorio de usuarios.

    const MAX_RESERVAS_ACTIVAS = 5;                                                     // Maximo de reservas futuras confirmadas por cliente (evita que alguien acapare todos los turnos).
    const DIAS_ADELANTE = 60;                                                           // Hasta cuantos dias en el futuro se puede reservar.

    public function __construct(ReservaRepositoryInterface $reservas, CanchaRepositoryInterface $canchas, PagoRepositoryInterface $pagos, UsuarioRepositoryInterface $usuarios) // Recibe sus dependencias.
    {                                                                                   // Abre el constructor.
        $this->reservas = $reservas;                                                    // Guarda el repositorio de reservas.
        $this->canchas = $canchas;                                                      // Guarda el repositorio de canchas.
        $this->pagos = $pagos;                                                          // Guarda el repositorio de pagos.
        $this->usuarios = $usuarios;                                                    // Guarda el repositorio de usuarios.
    }                                                                                   // Cierra el constructor.

    public function listarPorUsuario($usuarioId)                                        // Reservas de un usuario.
    {                                                                                   // Abre el metodo.
        return $this->reservas->listarPorUsuario($usuarioId);                           // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    public function listarTodas()                                                       // Todas las reservas.
    {                                                                                   // Abre el metodo.
        return $this->reservas->listarTodas();                                          // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Una reserva por id.
    {                                                                                   // Abre el metodo.
        return $this->reservas->buscarPorId($id);                                       // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    // Horarios de inicio disponibles: turnos de 1 hora, de 08:00 a 22:00 (el ultimo termina 23:00).
    public function horasDisponibles()                                                  // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $horas = [];                                                                    // Array vacio.
        for ($h = 8; $h <= 22; $h++) {                                                  // De las 8 a las 22.
            $horas[] = sprintf("%02d:00", $h);                                          // Agrega "08:00", "09:00", etc.
        }                                                                               // Cierra el for.
        return $horas;                                                                  // Devuelve la lista.
    }                                                                                   // Cierra el metodo.

    private function calcularFin($hora)                                                 // La hora de fin es 1 hora despues.
    {                                                                                   // Abre el metodo.
        return (new DateTime($hora))->modify("+1 hour")->format("H:i:s");               // Suma 1 hora.
    }                                                                                   // Cierra el metodo.

    private function esDuplicado(PDOException $e)                                       // Dice si el error de MySQL es "turno duplicado" (codigo 1062).
    {                                                                                   // Abre el metodo.
        return isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062;              // true si es el error del indice UNIQUE.
    }                                                                                   // Cierra el metodo.

    // Valida cancha, fecha y hora. Devuelve texto de error o "" si esta todo bien.
    private function validarDatos($canchaId, $fecha, $hora, $soloFuturo)                // $soloFuturo = true no deja elegir fechas pasadas.
    {                                                                                   // Abre el metodo.
        if (!$this->canchas->buscarPorId($canchaId)) {                                  // Si la cancha no existe...
            return "La cancha elegida no existe";                                       // ...error.
        }                                                                               // Cierra el if.
        $fechaObj = DateTime::createFromFormat("Y-m-d", $fecha);                        // Intenta convertir el texto a fecha.
        if (!$fechaObj || $fechaObj->format("Y-m-d") !== $fecha) {                      // Si no es una fecha real...
            return "La fecha no es valida";                                             // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($hora, $this->horasDisponibles(), true)) {                        // Si la hora no esta entre los turnos permitidos...
            return "El horario elegido no es valido";                                   // ...error.
        }                                                                               // Cierra el if.
        if (!$soloFuturo && ($fecha < "2020-01-01" || $fecha > date("Y-m-d", strtotime("+2 years")))) { // Si el admin edita: la fecha debe estar entre 2020 y dentro de 2 años...
            return "La fecha esta fuera del rango permitido";                           // ...error (evita fechas absurdas como el año 9999).
        }                                                                               // Cierra el if.
        if ($soloFuturo) {                                                              // Si hay que validar que no sea pasado...
            if ($fecha > date("Y-m-d", strtotime("+" . self::DIAS_ADELANTE . " days"))) { // Si la fecha esta mas lejos que el maximo permitido...
                return "Solo se puede reservar hasta " . self::DIAS_ADELANTE . " dias adelante"; // ...error.
            }                                                                           // Cierra el if.
            if ($fecha < date("Y-m-d")) {                                               // Si la fecha es anterior a hoy...
                return "No se puede reservar en una fecha pasada";                      // ...error.
            }                                                                           // Cierra el if.
            if ($fecha === date("Y-m-d") && $hora <= date("H:i")) {                     // Si es hoy y esa hora ya paso...
                return "Ese horario de hoy ya paso";                                    // ...error.
            }                                                                           // Cierra el if.
        }                                                                               // Cierra el if externo.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Verifica que el cliente no haya alcanzado el maximo de reservas activas. Devuelve texto de error o "".
    public function validarLimiteReservas($usuarioId)                                   // Recibe el id del cliente.
    {                                                                                   // Abre el metodo.
        if ($this->reservas->contarFuturasConfirmadas($usuarioId, date("Y-m-d H:i:s")) >= self::MAX_RESERVAS_ACTIVAS) { // Si ya tiene el maximo de reservas futuras confirmadas...
            return "Alcanzaste el maximo de " . self::MAX_RESERVAS_ACTIVAS . " reservas activas. Cancela alguna para poder reservar otra."; // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Valida una solicitud de reserva nueva. Devuelve ["error" => texto, "cancha" => Cancha].
    public function validarSolicitud($canchaId, $fecha, $hora)                          // Recibe cancha, fecha y hora.
    {                                                                                   // Abre el metodo.
        $error = $this->validarDatos($canchaId, $fecha, $hora, true);                   // Validaciones generales (solo futuro).
        if ($error !== "") {                                                            // Si hubo error...
            return ["error" => $error, "cancha" => null];                               // ...lo devuelve.
        }                                                                               // Cierra el if.
        $cancha = $this->canchas->buscarPorId($canchaId);                               // Trae la cancha.
        if (!$cancha->estaActiva()) {                                                   // Si esta inactiva...
            return ["error" => "Esa cancha no esta disponible por el momento", "cancha" => null]; // ...error.
        }                                                                               // Cierra el if.
        if ($this->reservas->hayConflicto($canchaId, $fecha, $hora . ":00")) {          // Si ya hay una reserva confirmada en ese turno...
            return ["error" => "Ese horario ya esta reservado, elegi otro", "cancha" => null]; // ...error.
        }                                                                               // Cierra el if.
        return ["error" => "", "cancha" => $cancha];                                    // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Valida los datos de la tarjeta FICTICIA. Devuelve texto de error o "".
    public function validarTarjeta($titular, $numero, $vencimiento, $cvv)               // Recibe los 4 datos.
    {                                                                                   // Abre el metodo.
        $errorTitular = Validador::texto($titular, 1, 60, "El titular");                // Titular: texto valido de 1 a 60 caracteres.
        if ($errorTitular !== "") {                                                     // Si hay error...
            return $errorTitular;                                                       // ...lo devuelve.
        }                                                                               // Cierra el if.
        $soloDigitos = preg_replace('/\D/', '', $numero);                               // Deja solo los numeros.
        if (strlen($soloDigitos) !== 16) {                                              // Si no son 16 digitos...
            return "El numero de tarjeta debe tener 16 digitos";                        // ...error.
        }                                                                               // Cierra el if.
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $vencimiento)) {                  // Si el vencimiento no tiene formato MM/AA...
            return "El vencimiento debe tener formato MM/AA (ej: 08/28)";               // ...error.
        }                                                                               // Cierra el if.
        $mes = (int)substr($vencimiento, 0, 2);                                         // Mes.
        $anio = 2000 + (int)substr($vencimiento, 3, 2);                                 // Año (20XX).
        if ($anio < (int)date("Y") || ($anio === (int)date("Y") && $mes < (int)date("n"))) { // Si ya vencio...
            return "La tarjeta esta vencida";                                           // ...error.
        }                                                                               // Cierra el if.
        if (!preg_match('/^\d{3}$/', $cvv)) {                                           // Si el CVV no son 3 numeros...
            return "El CVV debe tener 3 digitos";                                       // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Guarda reserva + pago en una TRANSACCION (o se guardan las dos cosas, o ninguna).
    private function registrarReservaYPago($usuarioId, Cancha $cancha, $fecha, $hora, $metodo, $ultimos4) // Recibe todo lo necesario.
    {                                                                                   // Abre el metodo.
        $codigo = "PAGO-" . strtoupper(bin2hex(random_bytes(4)));                       // Comprobante aleatorio, ej: PAGO-A1B2C3D4.
        try {                                                                           // Intenta guardar.
            Transaccion::ejecutar(function () use ($usuarioId, $cancha, $fecha, $hora, $metodo, $ultimos4, $codigo) { // Todo lo de adentro es una sola transaccion.
                if ($this->reservas->hayConflicto($cancha->id, $fecha, $hora . ":00")) { // Vuelve a verificar el turno DENTRO de la transaccion...
                    throw new DomainException("Ese horario ya esta reservado, elegi otro"); // ...si lo ocuparon justo ahora, corta.
                }                                                                       // Cierra el if.
                $reserva = new Reserva();                                               // Crea la entidad Reserva.
                $reserva->usuarioId = $usuarioId;                                       // Le pone el usuario.
                $reserva->canchaId = $cancha->id;                                       // Le pone la cancha.
                $reserva->fecha = $fecha;                                               // Le pone la fecha.
                $reserva->horaInicio = $hora . ":00";                                   // Le pone la hora de inicio.
                $reserva->horaFin = $this->calcularFin($hora);                          // Le pone la hora de fin.
                $reserva->monto = $cancha->precioHora;                                  // Le pone el precio de la cancha.
                $reserva->estado = "confirmada";                                        // Nace confirmada (ya se pago).
                $reservaId = $this->reservas->crear($reserva);                          // La guarda (el indice UNIQUE es la red de seguridad final).
                $this->pagos->crear(Pago::nuevo($reservaId, $cancha->precioHora, $metodo, $ultimos4, $codigo)); // Guarda el pago asociado.
            });                                                                         // Cierra la transaccion (commit automatico).
        } catch (DomainException $e) {                                                  // Si el turno ya estaba ocupado...
            return ["exito" => false, "mensaje" => $e->getMessage()];                   // ...avisa con el mensaje.
        } catch (PDOException $e) {                                                     // Si fallo la base de datos...
            if ($this->esDuplicado($e)) {                                               // ...y fue porque dos personas reservaron a la vez (indice UNIQUE)...
                return ["exito" => false, "mensaje" => "Ese horario acaba de ser reservado por otra persona, elegi otro"]; // ...avisa amablemente.
            }                                                                           // Cierra el if.
            error_log("GoalTech - error al reservar: " . $e->getMessage());             // Guarda el detalle tecnico en el log.
            return ["exito" => false, "mensaje" => "No se pudo guardar la reserva, intenta de nuevo"]; // Mensaje generico para el usuario.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Pago aprobado. Reserva confirmada. Comprobante: " . $codigo]; // Devuelve exito con el comprobante.
    }                                                                                   // Cierra el metodo.

    // CLIENTE: confirma la reserva con el pago simulado.
    public function confirmarReservaConPago($usuarioId, $canchaId, $fecha, $hora, $numeroTarjeta) // Recibe reserva y tarjeta.
    {                                                                                   // Abre el metodo.
        $validacion = $this->validarSolicitud($canchaId, $fecha, $hora);                // Vuelve a validar (alguien pudo reservar mientras tanto).
        if ($validacion["error"] !== "") {                                              // Si hay error...
            return ["exito" => false, "mensaje" => $validacion["error"]];               // ...lo devuelve.
        }                                                                               // Cierra el if.
        $errorLimite = $this->validarLimiteReservas($usuarioId);                        // Verifica el maximo de reservas activas del cliente.
        if ($errorLimite !== "") {                                                      // Si ya llego al maximo...
            return ["exito" => false, "mensaje" => $errorLimite];                       // ...lo devuelve.
        }                                                                               // Cierra el if.
        $ultimos4 = substr(preg_replace('/\D/', '', $numeroTarjeta), -4);               // Solo los ultimos 4 digitos (el resto NO se guarda).
        return $this->registrarReservaYPago($usuarioId, $validacion["cancha"], $fecha, $hora, "Tarjeta (simulada)", $ultimos4); // Guarda reserva + pago.
    }                                                                                   // Cierra el metodo.

    // CLIENTE: cancela una reserva propia y "reembolsa" el pago.
    public function cancelarPropia($reservaId, $usuarioId)                              // Recibe el id de la reserva y del usuario logueado.
    {                                                                                   // Abre el metodo.
        $reserva = $this->reservas->buscarPorId($reservaId);                            // Busca la reserva.
        if (!$reserva || $reserva->usuarioId !== (int)$usuarioId) {                     // Si no existe o NO es de este usuario...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error (nadie cancela reservas ajenas).
        }                                                                               // Cierra el if.
        if (!$reserva->estaConfirmada()) {                                              // Si ya estaba cancelada...
            return ["exito" => false, "mensaje" => "Esa reserva ya estaba cancelada"];  // ...error.
        }                                                                               // Cierra el if.
        if ($reserva->inicio() <= date("Y-m-d H:i:s")) {                                // Si el turno ya empezo o paso...
            return ["exito" => false, "mensaje" => "No se puede cancelar una reserva que ya empezo o paso"]; // ...error.
        }                                                                               // Cierra el if.
        return $this->cancelarYReembolsar($reservaId);                                  // Cancela y reembolsa.
    }                                                                                   // Cierra el metodo.

    // Cancela la reserva y marca su pago como reembolsado (los dos cambios juntos, en una transaccion).
    private function cancelarYReembolsar($reservaId)                                    // Recibe el id de la reserva.
    {                                                                                   // Abre el metodo.
        try {                                                                           // Intenta hacerlo.
            Transaccion::ejecutar(function () use ($reservaId) {                        // Una sola transaccion.
                $this->reservas->cancelar($reservaId);                                  // Marca la reserva como cancelada.
                $this->pagos->reembolsar($reservaId);                                   // Marca el pago como reembolsado.
            });                                                                         // Cierra la transaccion.
        } catch (PDOException $e) {                                                     // Si fallo la base...
            error_log("GoalTech - error al cancelar: " . $e->getMessage());            // ...guarda el detalle en el log...
            return ["exito" => false, "mensaje" => "No se pudo cancelar la reserva"];   // ...y devuelve un mensaje generico.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Reserva cancelada. El pago fue reembolsado (simulado)."]; // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // ADMIN - ALTA: crea una reserva para un cliente (ej: pedida por telefono, pago en efectivo).
    public function adminCrear($usuarioId, $canchaId, $fecha, $hora)                    // Recibe usuario, cancha, fecha y hora.
    {                                                                                   // Abre el metodo.
        if (!$this->usuarios->buscarPorId($usuarioId)) {                                // Si el usuario no existe...
            return ["exito" => false, "mensaje" => "El usuario elegido no existe"];     // ...error.
        }                                                                               // Cierra el if.
        $validacion = $this->validarSolicitud($canchaId, $fecha, $hora);                // Mismas reglas que para un cliente.
        if ($validacion["error"] !== "") {                                              // Si hay error...
            return ["exito" => false, "mensaje" => $validacion["error"]];               // ...lo devuelve.
        }                                                                               // Cierra el if.
        return $this->registrarReservaYPago($usuarioId, $validacion["cancha"], $fecha, $hora, "Efectivo (admin)", "----"); // Guarda reserva + pago en efectivo.
    }                                                                                   // Cierra el metodo.

    // ADMIN - MODIFICACION: cambia cancha, fecha, hora y estado. Mantiene el pago coherente con el estado.
    public function adminModificar($id, $canchaId, $fecha, $hora, $estado)              // Recibe el id y los datos nuevos.
    {                                                                                   // Abre el metodo.
        $actual = $this->reservas->buscarPorId($id);                                    // Busca la reserva.
        if (!$actual) {                                                                 // Si no existe...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($estado, ["confirmada", "cancelada"], true)) {                    // Si el estado no es valido...
            return ["exito" => false, "mensaje" => "Estado invalido"];                  // ...error.
        }                                                                               // Cierra el if.
        $error = $this->validarDatos($canchaId, $fecha, $hora, false);                  // Valida (el admin puede corregir fechas pasadas).
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        if ($estado === "confirmada" && $this->reservas->hayConflicto($canchaId, $fecha, $hora . ":00", $id)) { // Si va a quedar confirmada y choca con otra...
            return ["exito" => false, "mensaje" => "Ese horario ya esta reservado por otra reserva"]; // ...error.
        }                                                                               // Cierra el if.

        $nueva = new Reserva();                                                         // Arma la reserva con los datos nuevos.
        $nueva->id = (int)$id;                                                          // Mismo id.
        $nueva->canchaId = (int)$canchaId;                                              // Cancha nueva.
        $nueva->fecha = $fecha;                                                         // Fecha nueva.
        $nueva->horaInicio = $hora . ":00";                                             // Hora de inicio nueva.
        $nueva->horaFin = $this->calcularFin($hora);                                    // Hora de fin nueva.
        $nueva->estado = $estado;                                                       // Estado nuevo.

        try {                                                                           // Intenta guardar.
            Transaccion::ejecutar(function () use ($nueva, $actual) {                   // Reserva y pago cambian JUNTOS.
                $this->reservas->actualizar($nueva);                                    // Guarda los cambios de la reserva.
                if ($actual->estado === "confirmada" && $nueva->estado === "cancelada") { // Si pasa de confirmada a cancelada...
                    $this->pagos->reembolsar($nueva->id);                               // ...el pago pasa a "reembolsado".
                } elseif ($actual->estado === "cancelada" && $nueva->estado === "confirmada") { // Si pasa de cancelada a confirmada (reactivar)...
                    $this->pagos->aprobar($nueva->id);                                  // ...el pago vuelve a "aprobado".
                }                                                                       // Cierra el if/elseif.
            });                                                                         // Cierra la transaccion.
        } catch (PDOException $e) {                                                     // Si fallo la base...
            if ($this->esDuplicado($e)) {                                               // ...por turno duplicado...
                return ["exito" => false, "mensaje" => "Ese horario ya esta reservado por otra reserva"]; // ...avisa.
            }                                                                           // Cierra el if.
            error_log("GoalTech - error al modificar reserva: " . $e->getMessage());    // Guarda el detalle en el log.
            return ["exito" => false, "mensaje" => "No se pudo actualizar la reserva"]; // Mensaje generico.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Reserva actualizada correctamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // ADMIN - BAJA (logica): cancela una reserva cualquiera.
    public function adminCancelar($id)                                                  // Recibe el id.
    {                                                                                   // Abre el metodo.
        $reserva = $this->reservas->buscarPorId($id);                                   // Busca la reserva.
        if (!$reserva) {                                                                // Si no existe...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error.
        }                                                                               // Cierra el if.
        if (!$reserva->estaConfirmada()) {                                              // Si ya estaba cancelada...
            return ["exito" => false, "mensaje" => "Esa reserva ya estaba cancelada"];  // ...error.
        }                                                                               // Cierra el if.
        return $this->cancelarYReembolsar($id);                                         // Cancela y reembolsa.
    }                                                                                   // Cierra el metodo.

    // ADMIN - BAJA (fisica): borra la reserva definitivamente.
    public function adminEliminar($id)                                                  // Recibe el id.
    {                                                                                   // Abre el metodo.
        if (!$this->reservas->buscarPorId($id)) {                                       // Si no existe...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error.
        }                                                                               // Cierra el if.
        $this->reservas->eliminar($id);                                                 // La borra (su pago se borra solo).
        return ["exito" => true, "mensaje" => "Reserva eliminada definitivamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
