<?php                                                                                   // Abre el bloque de codigo PHP.
// Interfaz: operaciones que debe tener cualquier repositorio de canchas.

interface CanchaRepositoryInterface                                                     // Define la interfaz.
{                                                                                       // Abre la interfaz.
    public function listarTodas();                                                      // Devuelve un array de Cancha.
    public function listarActivas();                                                    // Devuelve solo las canchas activas.
    public function buscarPorId($id);                                                   // Devuelve una Cancha o null.
    public function crear(Cancha $cancha);                                              // ALTA: guarda una cancha nueva.
    public function actualizar(Cancha $cancha);                                         // MODIFICACION: actualiza una cancha.
    public function eliminar($id);                                                      // BAJA: borra una cancha.
}                                                                                       // Cierra la interfaz.
