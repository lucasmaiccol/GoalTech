<?php                                                                                   // Abre el bloque de codigo PHP.
// rutas.php: tabla de rutas. Cada linea dice: "nombre" => [Controlador, metodo, permiso].
// Permisos: publica (todos), invitado (solo sin sesion), logueado (cualquier usuario), cliente, admin.
// Las paginas se abren como index.php?r=nombre (ej: index.php?r=login).

return [                                                                                // Devuelve el array de rutas.
    "inicio"               => ["AuthController", "inicio", "publica"],                  // Portada.
    "login"                => ["AuthController", "login", "invitado"],                  // Iniciar sesion.
    "registro"             => ["AuthController", "registro", "invitado"],               // Registrarse.
    "logout"               => ["AuthController", "logout", "logueado"],                 // Cerrar sesion (solo por POST).

    "dashboard"            => ["ClienteController", "dashboard", "cliente"],            // Panel del cliente.
    "pago_iniciar"         => ["ClienteController", "pagoIniciar", "cliente"],          // Elegir cancha/horario (POST) y pasar al pago.
    "pago"                 => ["ClienteController", "pago", "cliente"],                 // Pantalla de pago simulado.
    "cancelar_reserva"     => ["ClienteController", "cancelar", "cliente"],             // Cancelar una reserva propia (POST).

    "admin_reservas"       => ["AdminReservaController", "listar", "admin"],            // Lista de reservas.
    "admin_reserva_form"   => ["AdminReservaController", "formulario", "admin"],        // Alta/modificacion de reserva.
    "admin_reserva_cancelar" => ["AdminReservaController", "cancelar", "admin"],        // Cancelar una reserva (POST).
    "admin_reserva_eliminar" => ["AdminReservaController", "eliminar", "admin"],        // Eliminar una reserva (POST).

    "admin_canchas"        => ["AdminCanchaController", "listar", "admin"],             // Lista de canchas.
    "admin_cancha_form"    => ["AdminCanchaController", "formulario", "admin"],         // Alta/modificacion de cancha.
    "admin_cancha_eliminar" => ["AdminCanchaController", "eliminar", "admin"],          // Eliminar una cancha (POST).

    "admin_usuarios"       => ["AdminUsuarioController", "listar", "admin"],            // Lista de usuarios.
    "admin_usuario_form"   => ["AdminUsuarioController", "formulario", "admin"],        // Alta/modificacion de usuario.
    "admin_usuario_eliminar" => ["AdminUsuarioController", "eliminar", "admin"],        // Eliminar un usuario (POST).
];                                                                                      // Cierra el array.
