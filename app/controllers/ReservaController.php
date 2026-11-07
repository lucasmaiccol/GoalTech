<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de reservas: contiene las reglas del negocio (validaciones, pago simulado, cancelaciones).

require_once __DIR__ . "/../models/Reserva.php";                                        // Carga el modelo Reserva.
require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo Cancha.
require_once __DIR__ . "/../models/Pago.php";                                           // Carga el modelo Pago.
require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo Usuario.

class ReservaController                                                                 // Define la clase del controlador.
{                                                                                       // Abre la clase.
    private $conexion;                                                                  // Conexion PDO (la necesitamos para las transacciones).
    private $reservaModelo;                                                             // Modelo de reservas.
    private $canchaModelo;                                                              // Modelo de canchas.
    private $pagoModelo;                                                                // Modelo de pagos.
    private $usuarioModelo;                                                             // Modelo de usuarios.

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->conexion = $conexion;                                                    // Guarda la conexion.
        $this->reservaModelo = new Reserva($conexion);                                  // Crea el modelo de reservas.
        $this->canchaModelo = new Cancha($conexion);                                    // Crea el modelo de canchas.
        $this->pagoModelo = new Pago($conexion);                                        // Crea el modelo de pagos.
        $this->usuarioModelo = new Usuario($conexion);                                  // Crea el modelo de usuarios.
    }                                                                                   // Cierra el constructor.

    // Lista de horarios de inicio disponibles: turnos de 1 hora, de 08:00 a 22:00 (el ultimo termina 23:00).
    public function horasDisponibles()                                                  // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $horas = [];                                                                    // Array vacio donde se van agregando las horas.
        for ($h = 8; $h <= 22; $h++) {                                                  // Recorre desde las 8 hasta las 22.
            $horas[] = sprintf("%02d:00", $h);                                          // Agrega la hora con formato "08:00", "09:00", etc.
        }                                                                               // Cierra el for.
        return $horas;                                                                  // Devuelve la lista.
    }                                                                                   // Cierra el metodo.

    // Calcula la hora de fin: siempre 1 hora despues del inicio.
    private function calcularFin($hora)                                                 // Recibe "HH:MM".
    {                                                                                   // Abre el metodo.
        return (new DateTime($hora))->modify("+1 hour")->format("H:i:s");               // Suma 1 hora y devuelve "HH:MM:SS".
    }                                                                                   // Cierra el metodo.

    // Valida cancha, fecha y hora. Devuelve un texto de error, o "" si todo esta bien.
    private function validarDatos($canchaId, $fecha, $hora, $soloFuturo)                // $soloFuturo = true no deja elegir fechas pasadas.
    {                                                                                   // Abre el metodo.
        if (!$this->canchaModelo->buscarPorId($canchaId)) {                             // Si la cancha no existe...
            return "La cancha elegida no existe";                                       // ...error.
        }                                                                               // Cierra el if.
        $fechaObj = DateTime::createFromFormat("Y-m-d", $fecha);                        // Intenta convertir el texto a fecha.
        if (!$fechaObj || $fechaObj->format("Y-m-d") !== $fecha) {                      // Si no se pudo o la fecha no es real (ej: 2026-02-31)...
            return "La fecha no es valida";                                             // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($hora, $this->horasDisponibles(), true)) {                        // Si la hora no esta en la lista de turnos permitidos...
            return "El horario elegido no es valido";                                   // ...error.
        }                                                                               // Cierra el if.
        if ($soloFuturo) {                                                              // Si hay que validar que no sea pasado...
            if ($fecha < date("Y-m-d")) {                                               // Si la fecha es anterior a hoy...
                return "No se puede reservar en una fecha pasada";                      // ...error.
            }                                                                           // Cierra el if.
            if ($fecha === date("Y-m-d") && $hora <= date("H:i")) {                     // Si es hoy y esa hora ya paso...
                return "Ese horario de hoy ya paso";                                    // ...error.
            }                                                                           // Cierra el if.
        }                                                                               // Cierra el if externo.
        return "";                                                                      // Todo bien: devuelve texto vacio.
    }                                                                                   // Cierra el metodo.

    // Valida una solicitud de reserva nueva. Devuelve ["error" => texto, "cancha" => datos].
    public function validarSolicitud($canchaId, $fecha, $hora)                          // Recibe cancha, fecha y hora.
    {                                                                                   // Abre el metodo.
        $error = $this->validarDatos($canchaId, $fecha, $hora, true);                   // Aplica las validaciones generales (solo futuro).
        if ($error !== "") {                                                            // Si hubo error...
            return ["error" => $error, "cancha" => null];                               // ...lo devuelve.
        }                                                                               // Cierra el if.
        $cancha = $this->canchaModelo->buscarPorId($canchaId);                          // Trae los datos de la cancha.
        if ($cancha["estado"] !== "activa") {                                           // Si la cancha esta inactiva...
            return ["error" => "Esa cancha no esta disponible por el momento", "cancha" => null]; // ...error.
        }                                                                               // Cierra el if.
        if ($this->reservaModelo->hayConflicto($canchaId, $fecha, $hora . ":00")) {     // Si ya hay una reserva confirmada en ese turno...
            return ["error" => "Ese horario ya esta reservado, elegi otro", "cancha" => null]; // ...error.
        }                                                                               // Cierra el if.
        return ["error" => "", "cancha" => $cancha];                                    // Todo bien: devuelve la cancha.
    }                                                                                   // Cierra el metodo.

    // Valida los datos de la tarjeta FICTICIA. Devuelve texto de error o "" si esta bien.
    public function validarTarjeta($titular, $numero, $vencimiento, $cvv)               // Recibe los 4 datos de la tarjeta.
    {                                                                                   // Abre el metodo.
        if ($titular === "") {                                                          // Si el titular esta vacio...
            return "Debe ingresar el nombre del titular";                               // ...error.
        }                                                                               // Cierra el if.
        $soloDigitos = preg_replace('/\D/', '', $numero);                               // Saca espacios y guiones: deja solo numeros.
        if (strlen($soloDigitos) !== 16) {                                              // Si no tiene 16 digitos...
            return "El numero de tarjeta debe tener 16 digitos";                        // ...error.
        }                                                                               // Cierra el if.
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $vencimiento)) {                  // Si el vencimiento no tiene formato MM/AA...
            return "El vencimiento debe tener formato MM/AA (ej: 08/28)";               // ...error.
        }                                                                               // Cierra el if.
        $mes = (int)substr($vencimiento, 0, 2);                                         // Toma el mes (los 2 primeros caracteres).
        $anio = 2000 + (int)substr($vencimiento, 3, 2);                                 // Toma el año (los 2 ultimos) y lo pasa a 20XX.
        if ($anio < (int)date("Y") || ($anio === (int)date("Y") && $mes < (int)date("n"))) { // Si el mes/año ya paso...
            return "La tarjeta esta vencida";                                           // ...error.
        }                                                                               // Cierra el if.
        if (!preg_match('/^\d{3}$/', $cvv)) {                                           // Si el CVV no son exactamente 3 numeros...
            return "El CVV debe tener 3 digitos";                                       // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Guarda reserva + pago dentro de una TRANSACCION: o se guardan las dos cosas, o ninguna.
    private function registrarReservaYPago($usuarioId, $cancha, $fecha, $hora, $metodo, $ultimos4) // Recibe todo lo necesario.
    {                                                                                   // Abre el metodo.
        $codigo = "PAGO-" . strtoupper(bin2hex(random_bytes(4)));                       // Genera un comprobante aleatorio, ej: PAGO-A1B2C3D4.
        try {                                                                           // Intenta guardar.
            $this->conexion->beginTransaction();                                        // Inicia la transaccion.
            $reservaId = $this->reservaModelo->crear($usuarioId, $cancha["id"], $fecha, $hora . ":00", $this->calcularFin($hora), $cancha["precio_hora"]); // Crea la reserva.
            $this->pagoModelo->crear($reservaId, $cancha["precio_hora"], $metodo, $ultimos4, $codigo); // Crea el pago asociado.
            $this->conexion->commit();                                                  // Confirma: guarda todo definitivamente.
        } catch (Exception $e) {                                                        // Si algo fallo...
            if ($this->conexion->inTransaction()) {                                     // ...y la transaccion sigue abierta...
                $this->conexion->rollBack();                                            // ...deshace todo lo que se habia hecho.
            }                                                                           // Cierra el if.
            return ["exito" => false, "mensaje" => "No se pudo guardar la reserva, intenta de nuevo"]; // Devuelve error.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Pago aprobado. Reserva confirmada. Comprobante: " . $codigo]; // Devuelve exito con el comprobante.
    }                                                                                   // Cierra el metodo.

    // CLIENTE: confirma la reserva con el pago simulado.
    public function confirmarReservaConPago($usuarioId, $canchaId, $fecha, $hora, $numeroTarjeta) // Recibe los datos de la reserva y la tarjeta.
    {                                                                                   // Abre el metodo.
        $validacion = $this->validarSolicitud($canchaId, $fecha, $hora);                // Vuelve a validar (por si alguien reservo mientras tanto).
        if ($validacion["error"] !== "") {                                              // Si hay error...
            return ["exito" => false, "mensaje" => $validacion["error"]];               // ...lo devuelve.
        }                                                                               // Cierra el if.
        $ultimos4 = substr(preg_replace('/\D/', '', $numeroTarjeta), -4);               // Se queda solo con los ultimos 4 digitos (el resto NO se guarda).
        return $this->registrarReservaYPago($usuarioId, $validacion["cancha"], $fecha, $hora, "Tarjeta (simulada)", $ultimos4); // Guarda reserva + pago.
    }                                                                                   // Cierra el metodo.

    // CLIENTE: cancela una reserva propia y "reembolsa" el pago.
    public function cancelarPropia($reservaId, $usuarioId)                              // Recibe el id de la reserva y del usuario logueado.
    {                                                                                   // Abre el metodo.
        $reserva = $this->reservaModelo->buscarPorId($reservaId);                       // Busca la reserva.
        if (!$reserva || (int)$reserva["usuario_id"] !== (int)$usuarioId) {             // Si no existe o NO es de este usuario...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error (nadie puede cancelar reservas ajenas).
        }                                                                               // Cierra el if.
        if ($reserva["estado"] === "cancelada") {                                       // Si ya estaba cancelada...
            return ["exito" => false, "mensaje" => "Esa reserva ya estaba cancelada"];  // ...error.
        }                                                                               // Cierra el if.
        if ($reserva["fecha"] . " " . $reserva["hora_inicio"] <= date("Y-m-d H:i:s")) { // Si el turno ya empezo o paso...
            return ["exito" => false, "mensaje" => "No se puede cancelar una reserva que ya empezo o paso"]; // ...error.
        }                                                                               // Cierra el if.
        return $this->cancelarYReembolsar($reservaId);                                  // Cancela y reembolsa.
    }                                                                                   // Cierra el metodo.

    // Cancela la reserva y marca su pago como reembolsado (los dos pasos juntos).
    private function cancelarYReembolsar($reservaId)                                    // Recibe el id de la reserva.
    {                                                                                   // Abre el metodo.
        try {                                                                           // Intenta hacerlo.
            $this->conexion->beginTransaction();                                        // Inicia la transaccion.
            $this->reservaModelo->cancelar($reservaId);                                 // Marca la reserva como cancelada.
            $this->pagoModelo->reembolsar($reservaId);                                  // Marca el pago como reembolsado.
            $this->conexion->commit();                                                  // Guarda los dos cambios.
        } catch (Exception $e) {                                                        // Si fallo algo...
            if ($this->conexion->inTransaction()) {                                     // ...y sigue abierta la transaccion...
                $this->conexion->rollBack();                                            // ...deshace los cambios.
            }                                                                           // Cierra el if.
            return ["exito" => false, "mensaje" => "No se pudo cancelar la reserva"];   // Devuelve error.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Reserva cancelada. El pago fue reembolsado (simulado)."]; // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // ADMIN - ALTA: crea una reserva para un cliente (ej: reserva pedida por telefono, pago en efectivo).
    public function adminCrear($usuarioId, $canchaId, $fecha, $hora)                    // Recibe usuario, cancha, fecha y hora.
    {                                                                                   // Abre el metodo.
        if (!$this->usuarioModelo->buscarPorId($usuarioId)) {                           // Si el usuario elegido no existe...
            return ["exito" => false, "mensaje" => "El usuario elegido no existe"];     // ...error.
        }                                                                               // Cierra el if.
        $validacion = $this->validarSolicitud($canchaId, $fecha, $hora);                // Aplica las mismas reglas que a un cliente.
        if ($validacion["error"] !== "") {                                              // Si hay error...
            return ["exito" => false, "mensaje" => $validacion["error"]];               // ...lo devuelve.
        }                                                                               // Cierra el if.
        return $this->registrarReservaYPago($usuarioId, $validacion["cancha"], $fecha, $hora, "Efectivo (admin)", "----"); // Guarda reserva + pago en efectivo.
    }                                                                                   // Cierra el metodo.

    // ADMIN - MODIFICACION: cambia cancha, fecha, hora y estado de una reserva existente.
    public function adminModificar($id, $canchaId, $fecha, $hora, $estado)              // Recibe el id y los datos nuevos.
    {                                                                                   // Abre el metodo.
        $reserva = $this->reservaModelo->buscarPorId($id);                              // Busca la reserva.
        if (!$reserva) {                                                                // Si no existe...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($estado, ["confirmada", "cancelada"], true)) {                    // Si el estado no es uno de los dos permitidos...
            return ["exito" => false, "mensaje" => "Estado invalido"];                  // ...error.
        }                                                                               // Cierra el if.
        $error = $this->validarDatos($canchaId, $fecha, $hora, false);                  // Valida los datos (el admin puede corregir fechas pasadas).
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        if ($estado === "confirmada" && $this->reservaModelo->hayConflicto($canchaId, $fecha, $hora . ":00", $id)) { // Si queda confirmada y choca con otra...
            return ["exito" => false, "mensaje" => "Ese horario ya esta reservado por otra reserva"]; // ...error.
        }                                                                               // Cierra el if.
        $this->reservaModelo->actualizar($id, $canchaId, $fecha, $hora . ":00", $this->calcularFin($hora), $estado); // Guarda los cambios.
        if ($estado === "cancelada" && $reserva["estado"] === "confirmada") {           // Si el admin la paso de confirmada a cancelada...
            $this->pagoModelo->reembolsar($id);                                         // ...tambien se reembolsa el pago.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Reserva actualizada correctamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // ADMIN - BAJA (logica): cancela una reserva cualquiera.
    public function adminCancelar($id)                                                  // Recibe el id.
    {                                                                                   // Abre el metodo.
        $reserva = $this->reservaModelo->buscarPorId($id);                              // Busca la reserva.
        if (!$reserva) {                                                                // Si no existe...
            return ["exito" => false, "mensaje" => "Reserva no encontrada"];            // ...error.
        }                                                                               // Cierra el if.
        if ($reserva["estado"] === "cancelada") {                                       // Si ya estaba cancelada...
            return ["exito" => false, "mensaje" => "Esa reserva ya estaba cancelada"];  // ...error.
        }                                                                               // Cierra el if.
        return $this->cancelarYReembolsar($id);                                         // Cancela y reembolsa.
    }                                                                                   // Cierra el metodo.

    // ADMIN - BAJA (fisica): borra la reserva definitivamente de la base.
    public function adminEliminar($id)                                                  // Recibe el id.
    {                                                                                   // Abre el metodo.
        $this->reservaModelo->eliminar($id);                                            // Borra la reserva (su pago se borra solo).
        return ["exito" => true, "mensaje" => "Reserva eliminada definitivamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
