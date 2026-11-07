<?php                                                                                   // Abre el bloque de codigo PHP.
// Interfaz: lista de operaciones que cualquier repositorio de usuarios debe tener.
// Los servicios dependen de ESTA interfaz, no de MySQL: asi se podria cambiar la base sin tocar las reglas del negocio.

interface UsuarioRepositoryInterface                                                    // Define la interfaz.
{                                                                                       // Abre la interfaz.
    public function buscarPorEmail($email);                                             // Devuelve un Usuario (con hash) o null.
    public function buscarPorId($id);                                                   // Devuelve un Usuario o null.
    public function listarTodos();                                                      // Devuelve un array de Usuario.
    public function crear(Usuario $usuario);                                            // ALTA: guarda un usuario nuevo.
    public function actualizar(Usuario $usuario);                                       // MODIFICACION: cambia nombre, telefono y rol.
    public function actualizarPassword($id, $passwordHash);                             // Cambia solo la contraseña (hash).
    public function eliminar($id);                                                      // BAJA: borra un usuario.
}                                                                                       // Cierra la interfaz.
