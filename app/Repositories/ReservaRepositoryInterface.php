<?php                                                                                   // Abre el bloque de codigo PHP.
// Interfaz: operaciones que debe tener cualquier repositorio de reservas.

interface ReservaRepositoryInterface                                                    // Define la interfaz.
{                                                                                       // Abre la interfaz.
    public function listarPorUsuario($usuarioId);                                       // Reservas de un usuario.
    public function listarTodas();                                                      // Todas las reservas.
    public function buscarPorId($id);                                                   // Devuelve una Reserva o null.
    public function hayConflicto($canchaId, $fecha, $horaInicio, $excluirId = 0);       // Pregunta si el turno ya esta ocupado.
    public function contarFuturasConfirmadas($usuarioId, $ahora);                       // Cuenta las reservas confirmadas de un usuario que todavia no empezaron.
    public function crear(Reserva $reserva);                                            // ALTA: guarda y devuelve el id nuevo.
    public function actualizar(Reserva $reserva);                                       // MODIFICACION: cambia cancha, fecha, horario y estado.
    public function cancelar($id);                                                      // Marca la reserva como cancelada.
    public function eliminar($id);                                                      // BAJA: borra la reserva.
}                                                                                       // Cierra la interfaz.
