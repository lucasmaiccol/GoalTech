<?php                                                                                   // Abre el bloque de codigo PHP.
// Validador: reglas de validacion REUTILIZABLES. Todos los servicios validan con estas funciones,
// asi hay un solo lugar donde se definen los limites (y no se olvida validar algo).
// Cada metodo devuelve "" si el dato es valido, o un texto con el error.

class Validador                                                                         // Define la clase.
{                                                                                       // Abre la clase.
    const PRECIO_MAX = 100000;                                                          // Precio maximo por hora permitido (en pesos).

    // Texto comun: UTF-8 valido, sin caracteres de control, y con un largo minimo y maximo (en CARACTERES, no bytes).
    public static function texto($valor, $min, $max, $campo)                            // Recibe el valor, largo minimo, largo maximo y nombre del campo.
    {                                                                                   // Abre el metodo.
        if (!is_string($valor) || !mb_check_encoding($valor, "UTF-8")) {                // Si no es un texto o tiene bytes invalidos...
            return $campo . " contiene caracteres no validos";                          // ...error (evita que MySQL falle con un error 500).
        }                                                                               // Cierra el if.
        if (preg_match('/[\x00-\x1F\x7F]/', $valor)) {                                  // Si tiene caracteres de control (saltos de linea, tabulaciones, etc.)...
            return $campo . " contiene caracteres no validos";                          // ...error.
        }                                                                               // Cierra el if.
        $largo = mb_strlen($valor);                                                     // Cuenta los caracteres (mb_strlen cuenta bien las tildes y la ñ).
        if ($largo < $min) {                                                            // Si es mas corto que el minimo...
            return $min === 1 ? $campo . " es obligatorio" : $campo . " debe tener al menos " . $min . " caracteres"; // ...error.
        }                                                                               // Cierra el if.
        if ($largo > $max) {                                                            // Si es mas largo que el maximo...
            return $campo . " no puede superar los " . $max . " caracteres";            // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Correo electronico: formato valido y maximo 100 caracteres.
    public static function email($valor)                                                // Recibe el correo.
    {                                                                                   // Abre el metodo.
        $error = self::texto($valor, 1, 100, "El correo");                              // Primero valida como texto comun.
        if ($error !== "") {                                                            // Si ya hay error...
            return $error;                                                              // ...lo devuelve.
        }                                                                               // Cierra el if.
        return filter_var($valor, FILTER_VALIDATE_EMAIL) ? "" : "Debe ingresar un correo electronico valido"; // Verifica el formato.
    }                                                                                   // Cierra el metodo.

    // Telefono: solo numeros, espacios, +, - y parentesis; entre 6 y 20 caracteres.
    public static function telefono($valor)                                             // Recibe el telefono.
    {                                                                                   // Abre el metodo.
        if (!is_string($valor) || !preg_match('/^[0-9+\-\s()]{6,20}$/', $valor)) {      // Si no cumple el formato...
            return "El telefono solo puede tener numeros, espacios, + - ( ) y entre 6 y 20 caracteres"; // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Contraseña: entre 8 y 72 bytes (72 es el limite de bcrypt).
    public static function password($valor)                                             // Recibe la contraseña.
    {                                                                                   // Abre el metodo.
        if (!is_string($valor) || strlen($valor) < 8 || strlen($valor) > 72) {          // Si no tiene entre 8 y 72 caracteres...
            return "La contraseña debe tener entre 8 y 72 caracteres";                  // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Precio: formato ESTRICTO (solo digitos y hasta 2 decimales con punto), entre 1 y PRECIO_MAX.
    // Rechaza notacion cientifica (1e5), espacios, hexadecimal, 0.001, 12.345 y numeros gigantes.
    public static function precio($valor)                                               // Recibe el precio como texto.
    {                                                                                   // Abre el metodo.
        if (!is_string($valor) || !preg_match('/^\d{1,6}(\.\d{1,2})?$/', $valor)) {     // Solo 1 a 6 digitos enteros y hasta 2 decimales.
            return "El precio debe ser un numero con hasta 2 decimales (ej: 1200 o 1200.50)"; // ...error.
        }                                                                               // Cierra el if.
        if ((float)$valor < 1 || (float)$valor > self::PRECIO_MAX) {                    // Si es menor a 1 o mayor al maximo...
            return "El precio por hora debe estar entre 1 y " . number_format(self::PRECIO_MAX, 0, ",", ".") . " pesos"; // ...error.
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
