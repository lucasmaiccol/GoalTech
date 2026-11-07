<?php                                                                                   // Abre el bloque de codigo PHP.
// Servicio de usuarios: reglas para que el ADMIN dé de alta, modifique y borre usuarios.

class UsuarioService                                                                    // Define la clase.
{                                                                                       // Abre la clase.
    private $usuarios;                                                                  // Repositorio de usuarios.

    public function __construct(UsuarioRepositoryInterface $usuarios)                   // Recibe el repositorio.
    {                                                                                   // Abre el constructor.
        $this->usuarios = $usuarios;                                                    // Lo guarda.
    }                                                                                   // Cierra el constructor.

    public function listarTodos()                                                       // Todos los usuarios.
    {                                                                                   // Abre el metodo.
        return $this->usuarios->listarTodos();                                          // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Un usuario por id.
    {                                                                                   // Abre el metodo.
        return $this->usuarios->buscarPorId($id);                                       // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    // Valida nombre, telefono y correo con el Validador central. Devuelve texto de error o "".
    private function validarDatos($nombre, $telefono)                                   // Recibe nombre y telefono.
    {                                                                                   // Abre el metodo.
        $error = Validador::texto($nombre, 1, 50, "El nombre");                         // Nombre: obligatorio, maximo 50.
        return $error !== "" ? $error : Validador::telefono($telefono);                 // Si el nombre esta bien, valida el telefono.
    }                                                                                   // Cierra el metodo.

    // ALTA de usuario hecha por el admin (puede elegir el rol).
    public function crear($nombre, $email, $telefono, $password, $rol)                  // Recibe los datos del formulario.
    {                                                                                   // Abre el metodo.
        $error = $this->validarDatos($nombre, $telefono);                               // Valida nombre y telefono.
        if ($error === "") {                                                            // Si estan bien...
            $error = Validador::email($email);                                          // ...valida el correo.
        }                                                                               // Cierra el if.
        if ($error === "") {                                                            // Si esta bien...
            $error = Validador::password($password);                                    // ...valida la contraseña (8 a 72).
        }                                                                               // Cierra el if.
        if ($error === "" && !in_array($rol, ["cliente", "admin"], true)) {             // Si el rol no es uno de los dos...
            $error = "Rol invalido";                                                    // ...error.
        }                                                                               // Cierra el if.
        if ($error === "" && $this->usuarios->buscarPorEmail($email)) {                 // Si el correo ya existe...
            $error = "Ese correo ya esta registrado";                                   // ...error.
        }                                                                               // Cierra el if.
        if ($error !== "") {                                                            // Si hubo algun error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $usuario = new Usuario();                                                       // Crea la entidad.
        $usuario->nombre = $nombre;                                                     // Nombre.
        $usuario->email = mb_strtolower($email);                                        // Correo en minusculas.
        $usuario->telefono = $telefono;                                                 // Telefono.
        $usuario->passwordHash = password_hash($password, PASSWORD_DEFAULT);            // Hash de la contraseña.
        $usuario->rol = $rol;                                                           // Rol elegido.
        $this->usuarios->crear($usuario);                                               // Lo guarda.
        return ["exito" => true, "mensaje" => "Usuario creado correctamente"];          // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION de usuario. Si $password viene vacia, no se cambia la contraseña.
    public function actualizar($id, $nombre, $telefono, $rol, $password, $idLogueado)   // Recibe el id, los datos y el id del admin actual.
    {                                                                                   // Abre el metodo.
        $usuario = $this->usuarios->buscarPorId($id);                                   // Busca el usuario.
        if (!$usuario) {                                                                // Si no existe...
            return ["exito" => false, "mensaje" => "Usuario no encontrado"];            // ...error.
        }                                                                               // Cierra el if.
        $error = $this->validarDatos($nombre, $telefono);                               // Valida nombre y telefono.
        if ($error === "" && !in_array($rol, ["cliente", "admin"], true)) {             // Si el rol no es valido...
            $error = "Rol invalido";                                                    // ...error.
        }                                                                               // Cierra el if.
        if ($error === "" && (int)$id === (int)$idLogueado && $rol !== "admin") {       // Si el admin intenta quitarse su propio rol...
            $error = "No podes quitarte el rol de admin a vos mismo";                   // ...lo impide (se quedaria sin acceso).
        }                                                                               // Cierra el if.
        if ($error === "" && $password !== "") {                                        // Si escribio una contraseña nueva...
            $error = Validador::password($password);                                    // ...la valida (8 a 72).
        }                                                                               // Cierra el if.
        if ($error !== "") {                                                            // Si hubo algun error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $usuario->nombre = $nombre;                                                     // Nombre nuevo.
        $usuario->telefono = $telefono;                                                 // Telefono nuevo.
        $usuario->rol = $rol;                                                           // Rol nuevo.
        $this->usuarios->actualizar($usuario);                                          // Guarda nombre, telefono y rol.
        if ($password !== "") {                                                         // Si escribio una contraseña nueva...
            $this->usuarios->actualizarPassword($id, password_hash($password, PASSWORD_DEFAULT)); // ...la guarda hasheada.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Usuario actualizado correctamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // BAJA de usuario: no deja borrarse a si mismo ni borrar usuarios con reservas.
    public function eliminar($id, $idLogueado)                                          // Recibe el id a borrar y el id del admin actual.
    {                                                                                   // Abre el metodo.
        if ((int)$id === (int)$idLogueado) {                                            // Si intenta borrarse a si mismo...
            return ["exito" => false, "mensaje" => "No podes eliminar tu propia cuenta"]; // ...error.
        }                                                                               // Cierra el if.
        try {                                                                           // Intenta borrar.
            $this->usuarios->eliminar($id);                                             // Ejecuta el DELETE.
        } catch (PDOException $e) {                                                     // Si la base rechaza el borrado...
            return ["exito" => false, "mensaje" => "No se puede eliminar: el usuario tiene reservas asociadas"]; // ...avisa el motivo.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Usuario eliminado correctamente"];       // Devuelve exito.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
