<?php                                                                                   // Abre el bloque de codigo PHP.
// config.example.php: PLANTILLA de configuracion. Se sube a GitHub.
// Para usar el proyecto: copiar este archivo como config/config.php y completar los datos reales.
// config/config.php esta en .gitignore, asi la contraseña de la base NUNCA se sube al repositorio.

return [                                                                                // Devuelve un array con toda la configuracion.
    "bd" => [                                                                           // Datos de la base de datos.
        "host" => "localhost",                                                          // Servidor de MySQL (en XAMPP es la misma PC).
        "nombre" => "sistema_canchas",                                                  // Nombre de la base de datos.
        "usuario" => "goaltech_app",                                                    // Usuario propio de la app (solo permisos SELECT, INSERT, UPDATE y DELETE).
        "password" => "goaltech_2026_clave",                                            // Contraseña de ese usuario (se crea en sistema_canchas.sql). Cambiarla en produccion.
        "charset" => "utf8mb4",                                                         // Codificacion (tildes y ñ).
    ],                                                                                  // Cierra los datos de la base.
    "seguridad" => [                                                                    // Parametros de seguridad.
        "intentos_login_max" => 5,                                                      // Intentos fallidos permitidos antes de bloquear el login.
        "minutos_bloqueo" => 10,                                                        // Minutos que dura el bloqueo (y la ventana en que se cuentan los intentos).
        "inactividad_segundos" => 1800,                                                 // Segundos sin actividad antes de cerrar la sesion sola (1800 = 30 minutos).
    ],                                                                                  // Cierra seguridad.
];                                                                                      // Cierra el array.
