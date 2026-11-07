<?php                                                                                   // Abre el bloque de codigo PHP.
// Entidad Cancha: representa UNA cancha (una fila de la tabla canchas).

class Cancha                                                                            // Define la clase.
{                                                                                       // Abre la clase.
    public $id;                                                                         // Identificador.
    public $nombre;                                                                     // Nombre (ej: "Cancha 1").
    public $tipo;                                                                       // Tipo (ej: "Futbol 5").
    public $precioHora;                                                                 // Precio de una hora.
    public $descripcion;                                                                // Texto corto (puede ser null).
    public $imagen;                                                                     // Foto: link o ruta (puede ser null).
    public $estado = "activa";                                                          // "activa" o "inactiva".

    // Crea una Cancha a partir de una fila de la base de datos.
    public static function desdeFila($fila)                                             // Recibe el array de la fila.
    {                                                                                   // Abre el metodo.
        $c = new Cancha();                                                              // Crea el objeto vacio.
        $c->id = (int)$fila["id"];                                                      // Copia el id.
        $c->nombre = $fila["nombre"];                                                   // Copia el nombre.
        $c->tipo = $fila["tipo"];                                                       // Copia el tipo.
        $c->precioHora = $fila["precio_hora"];                                          // Copia el precio.
        $c->descripcion = $fila["descripcion"];                                         // Copia la descripcion.
        $c->imagen = $fila["imagen"];                                                   // Copia la foto.
        $c->estado = $fila["estado"];                                                   // Copia el estado.
        return $c;                                                                      // Devuelve el objeto.
    }                                                                                   // Cierra el metodo.

    public function estaActiva()                                                        // Pregunta si se puede reservar.
    {                                                                                   // Abre el metodo.
        return $this->estado === "activa";                                              // true si esta activa.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
