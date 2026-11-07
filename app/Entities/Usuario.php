<?php                                                                                   // Abre el bloque de codigo PHP.
// Entidad Usuario: representa a UNA persona del sistema (una fila de la tabla usuarios).

class Usuario                                                                           // Define la clase.
{                                                                                       // Abre la clase.
    public $id;                                                                         // Identificador.
    public $nombre;                                                                     // Nombre.
    public $email;                                                                      // Correo (unico).
    public $telefono;                                                                   // Telefono.
    public $passwordHash;                                                               // Hash de la contraseña (nunca la contraseña real).
    public $rol = "cliente";                                                            // Rol: "cliente" o "admin".
    public $creadoEn;                                                                   // Fecha de creacion.

    // Crea un Usuario a partir de una fila de la base de datos.
    public static function desdeFila($fila)                                             // Recibe el array de la fila.
    {                                                                                   // Abre el metodo.
        $u = new Usuario();                                                             // Crea el objeto vacio.
        $u->id = (int)$fila["id"];                                                      // Copia el id.
        $u->nombre = $fila["nombre"];                                                   // Copia el nombre.
        $u->email = $fila["email"];                                                     // Copia el correo.
        $u->telefono = $fila["telefono"];                                               // Copia el telefono.
        $u->passwordHash = $fila["password"] ?? null;                                   // Copia el hash (si vino en la consulta).
        $u->rol = $fila["rol"];                                                         // Copia el rol.
        $u->creadoEn = $fila["created_at"] ?? null;                                     // Copia la fecha.
        return $u;                                                                      // Devuelve el objeto.
    }                                                                                   // Cierra el metodo.

    public function esAdmin()                                                           // Pregunta si el usuario es administrador.
    {                                                                                   // Abre el metodo.
        return $this->rol === "admin";                                                  // true si su rol es "admin".
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
