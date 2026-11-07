<?php                                                                                   // Abre el bloque de codigo PHP.
// Fabrica: el UNICO lugar donde se "arman" las piezas: conecta controladores -> servicios -> repositorios -> base de datos.
// Si manana se cambia MySQL por otra base, solo se toca este archivo (y los repositorios).

class Fabrica                                                                           // Define la clase.
{                                                                                       // Abre la clase.
    public static function authService()                                                // Crea el servicio de autenticacion.
    {                                                                                   // Abre el metodo.
        $c = Database::conexion();                                                      // Toma la conexion.
        return new AuthService(new PdoUsuarioRepository($c), new PdoIntentoLoginRepository($c)); // Le pasa los repositorios que necesita.
    }                                                                                   // Cierra el metodo.

    public static function reservaService()                                             // Crea el servicio de reservas.
    {                                                                                   // Abre el metodo.
        $c = Database::conexion();                                                      // Toma la conexion.
        return new ReservaService(new PdoReservaRepository($c), new PdoCanchaRepository($c), new PdoPagoRepository($c), new PdoUsuarioRepository($c)); // Le pasa los repositorios.
    }                                                                                   // Cierra el metodo.

    public static function canchaService()                                              // Crea el servicio de canchas.
    {                                                                                   // Abre el metodo.
        return new CanchaService(new PdoCanchaRepository(Database::conexion()));       // Le pasa el repositorio de canchas.
    }                                                                                   // Cierra el metodo.

    public static function usuarioService()                                             // Crea el servicio de usuarios.
    {                                                                                   // Abre el metodo.
        return new UsuarioService(new PdoUsuarioRepository(Database::conexion()));     // Le pasa el repositorio de usuarios.
    }                                                                                   // Cierra el metodo.

    // Crea el controlador pedido con los servicios que necesita (inyeccion de dependencias).
    public static function crearControlador($clase)                                     // Recibe el nombre de la clase.
    {                                                                                   // Abre el metodo.
        switch ($clase) {                                                               // Segun el controlador pedido...
            case "AuthController":                                                      // Autenticacion.
                return new AuthController(self::authService());                         // Necesita el servicio de autenticacion.
            case "ClienteController":                                                   // Panel del cliente.
                return new ClienteController(self::reservaService(), self::canchaService()); // Necesita reservas y canchas.
            case "AdminReservaController":                                              // Admin de reservas.
                return new AdminReservaController(self::reservaService(), self::canchaService(), self::usuarioService()); // Necesita reservas, canchas y usuarios.
            case "AdminCanchaController":                                               // Admin de canchas.
                return new AdminCanchaController(self::canchaService());                // Necesita canchas.
            case "AdminUsuarioController":                                              // Admin de usuarios.
                return new AdminUsuarioController(self::usuarioService());              // Necesita usuarios.
        }                                                                               // Cierra el switch.
        throw new RuntimeException("Controlador desconocido");                          // Si no existe, error.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
