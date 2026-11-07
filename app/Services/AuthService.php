<?php                                                                                   // Abre el bloque de codigo PHP.
// Servicio de autenticacion: reglas del login, el registro y "quien es el usuario actual".
// No sabe nada de HTTP ($_POST) ni de SQL: solo usa repositorios y devuelve resultados.

class AuthService                                                                       // Define la clase.
{                                                                                       // Abre la clase.
    private $usuarios;                                                                  // Repositorio de usuarios.
    private $intentos;                                                                  // Repositorio de intentos de login fallidos.

    // Hash "de relleno": se usa cuando el correo no existe, para que el login tarde LO MISMO exista o no el usuario.
    const HASH_RELLENO = '$2y$10$jBW1YWePd6HGdQ5b88Z0oOAwifD4VBTBmKddsmObOcU8MFLjIB/D2';                                              // (Si no, midiendo el tiempo de respuesta se podria saber que correos estan registrados.)

    public function __construct(UsuarioRepositoryInterface $usuarios, IntentoLoginRepositoryInterface $intentos) // Recibe lo que necesita (inyeccion de dependencias).
    {                                                                                   // Abre el constructor.
        $this->usuarios = $usuarios;                                                    // Guarda el repositorio de usuarios.
        $this->intentos = $intentos;                                                    // Guarda el repositorio de intentos.
    }                                                                                   // Cierra el constructor.

    // LOGIN: valida el limite de intentos, el correo y la contraseña. Si estan bien, abre la sesion.
    public function iniciarSesion($email, $password, $ip)                               // Recibe correo, contraseña e IP del visitante.
    {                                                                                   // Abre el metodo.
        $email = mb_strtolower(trim($email));                                           // Normaliza el correo a minusculas.
        $config = Config::get("seguridad");                                             // Lee los limites de seguridad.
        $max = $config["intentos_login_max"];                                           // Intentos permitidos.
        $minutos = $config["minutos_bloqueo"];                                          // Minutos de bloqueo.

        if ($this->intentos->contarPorEmail($email, $minutos) >= $max || $this->intentos->contarPorIp($ip, $minutos) >= $max * 4) { // Si ese correo (o esa IP) fallo demasiadas veces...
            return ["exito" => false, "mensaje" => "Demasiados intentos fallidos. Espera " . $minutos . " minutos e intenta de nuevo."]; // ...bloquea el login un rato (frena la fuerza bruta).
        }                                                                               // Cierra el if.

        if (mt_rand(1, 20) === 1) {                                                     // De vez en cuando (1 de cada 20 logins)...
            $this->intentos->limpiarAntiguos();                                         // ...borra los intentos viejos para que la tabla no crezca sin limite.
        }                                                                               // Cierra el if.

        $usuario = mb_strlen($email) <= 100 ? $this->usuarios->buscarPorEmail($email) : null; // Busca el usuario (si el correo es absurdamente largo ni consulta).
        $hash = $usuario ? $usuario->passwordHash : self::HASH_RELLENO;                 // Si no existe, compara contra el hash de relleno (mismo tiempo de respuesta).
        $claveCorrecta = password_verify(is_string($password) ? $password : "", $hash); // Verifica la contraseña (siempre se ejecuta, exista o no el usuario).
        if (!$usuario || !$claveCorrecta) {                                             // Si no existe o la contraseña no coincide...
            $this->intentos->registrar(mb_substr($email, 0, 100), $ip);                 // ...anota el intento fallido...
            return ["exito" => false, "mensaje" => "Correo o contraseña incorrectos"];  // ...y responde con un mensaje generico (no dice cual fallo).
        }                                                                               // Cierra el if.

        $this->intentos->limpiar($email);                                               // Entro bien: borra sus intentos fallidos.
        Sesion::guardarUsuario($usuario->id);                                           // Abre la sesion (guarda solo el id).
        return ["exito" => true, "mensaje" => "Bienvenido, " . $usuario->nombre, "usuario" => $usuario]; // Devuelve exito y el usuario.
    }                                                                                   // Cierra el metodo.

    // REGISTRO: valida los datos (incluido el LARGO de cada campo) y crea un usuario con rol "cliente".
    public function registrar($nombre, $email, $telefono, $password, $confirmar)        // Recibe los datos del formulario.
    {                                                                                   // Abre el metodo.
        $error = Validador::texto($nombre, 1, 50, "El nombre");                         // Nombre: obligatorio, maximo 50 caracteres.
        if ($error === "") {                                                            // Si esta bien...
            $error = Validador::email($email);                                          // ...valida el correo (formato y maximo 100).
        }                                                                               // Cierra el if.
        if ($error === "") {                                                            // Si esta bien...
            $error = Validador::telefono($telefono);                                    // ...valida el telefono (formato y 6 a 20 caracteres).
        }                                                                               // Cierra el if.
        if ($error === "") {                                                            // Si esta bien...
            $error = Validador::password($password);                                    // ...valida la contraseña (8 a 72).
        }                                                                               // Cierra el if.
        if ($error === "" && $password !== $confirmar) {                                // Si las dos contraseñas no son iguales...
            $error = "Las contraseñas no coinciden";                                    // ...error.
        }                                                                               // Cierra el if.
        if ($error === "" && $this->usuarios->buscarPorEmail($email)) {                 // Si ya existe un usuario con ese correo...
            $error = "Ese correo ya esta registrado";                                   // ...error.
        }                                                                               // Cierra el if.
        if ($error !== "") {                                                            // Si hubo algun error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.

        $usuario = new Usuario();                                                       // Crea la entidad.
        $usuario->nombre = $nombre;                                                     // Le pone el nombre.
        $usuario->email = mb_strtolower($email);                                        // Le pone el correo en minusculas.
        $usuario->telefono = $telefono;                                                 // Le pone el telefono.
        $usuario->passwordHash = password_hash($password, PASSWORD_DEFAULT);            // Guarda el HASH, nunca la contraseña.
        $usuario->rol = "cliente";                                                      // Todo registro publico es "cliente" (solo un admin puede crear otro admin).
        $this->usuarios->crear($usuario);                                               // Lo guarda.
        return ["exito" => true, "mensaje" => "Usuario registrado correctamente. Ya podes iniciar sesion."]; // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // Devuelve el usuario logueado LEYENDO SU DATOS ACTUALES DE LA BASE (asi, si le quitan el rol de admin, se nota enseguida).
    public function usuarioActual()                                                     // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $id = Sesion::usuarioId();                                                      // Id guardado en la sesion.
        if ($id === null) {                                                             // Si no hay nadie logueado...
            return null;                                                                // ...devuelve null.
        }                                                                               // Cierra el if.
        $usuario = $this->usuarios->buscarPorId($id);                                   // Busca al usuario en la base.
        if (!$usuario) {                                                                // Si ya no existe (lo borraron)...
            Sesion::cerrar();                                                           // ...cierra su sesion.
            return null;                                                                // ...y devuelve null.
        }                                                                               // Cierra el if.
        return $usuario;                                                                // Devuelve el usuario con su rol actual.
    }                                                                                   // Cierra el metodo.

    public function cerrarSesion()                                                      // Cierra la sesion.
    {                                                                                   // Abre el metodo.
        Sesion::cerrar();                                                               // Borra los datos de la sesion.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
