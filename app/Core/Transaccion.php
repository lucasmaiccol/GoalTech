<?php                                                                                   // Abre el bloque de codigo PHP.
// Transaccion: ejecuta varias operaciones de base de datos como UNA sola (o se guardan todas, o ninguna).

class Transaccion                                                                       // Define la clase Transaccion.
{                                                                                       // Abre la clase.
    // Recibe una funcion con las operaciones. Si todo sale bien hace commit; si algo falla, deshace todo (rollback).
    public static function ejecutar($operaciones)                                       // Recibe la funcion a ejecutar.
    {                                                                                   // Abre el metodo.
        $conexion = Database::conexion();                                               // Toma la conexion.
        $conexion->beginTransaction();                                                  // Inicia la transaccion.
        try {                                                                           // Intenta ejecutar las operaciones.
            $resultado = $operaciones();                                                // Ejecuta la funcion recibida.
            $conexion->commit();                                                        // Si no hubo errores, guarda todo definitivamente.
            return $resultado;                                                          // Devuelve lo que devolvio la funcion.
        } catch (Exception $e) {                                                        // Si algo fallo...
            if ($conexion->inTransaction()) {                                           // ...y la transaccion sigue abierta...
                $conexion->rollBack();                                                  // ...deshace todos los cambios.
            }                                                                           // Cierra el if.
            throw $e;                                                                   // Vuelve a lanzar el error para que lo maneje quien llamo.
        }                                                                               // Cierra el catch.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
