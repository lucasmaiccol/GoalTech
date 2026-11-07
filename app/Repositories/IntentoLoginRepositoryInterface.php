<?php                                                                                   // Abre el bloque de codigo PHP.
// Interfaz: operaciones para registrar y contar los intentos de login fallidos.

interface IntentoLoginRepositoryInterface                                               // Define la interfaz.
{                                                                                       // Abre la interfaz.
    public function registrar($email, $ip);                                             // Guarda un intento fallido.
    public function contarPorEmail($email, $minutos);                                   // Cuenta los intentos de un correo en los ultimos minutos.
    public function contarPorIp($ip, $minutos);                                         // Cuenta los intentos de una IP en los ultimos minutos.
    public function limpiarAntiguos();                                                  // Borra los intentos de mas de 1 dia (para que la tabla no crezca sin limite).
    public function limpiar($email);                                                    // Borra los intentos de un correo (al entrar bien).
}                                                                                       // Cierra la interfaz.
