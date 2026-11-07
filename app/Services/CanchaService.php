<?php                                                                                   // Abre el bloque de codigo PHP.
// Servicio de canchas: reglas para dar de alta, modificar y borrar canchas (incluida la foto).

class CanchaService                                                                     // Define la clase.
{                                                                                       // Abre la clase.
    private $canchas;                                                                   // Repositorio de canchas.

    // Imagenes de ejemplo que el admin puede elegir (link o ruta => nombre que se muestra).
    const IMAGENES = [                                                                  // Constante: lista fija de imagenes predefinidas.
        "https://images.unsplash.com/photo-1600529533273-938db2591dc4?auto=format&fit=crop&w=1200&q=80" => "Foto: cancha sintetica",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1632300951015-42d7df909581?auto=format&fit=crop&w=1200&q=80" => "Foto: partido entre amigos",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1604412132568-9d5c14de13c4?auto=format&fit=crop&w=1200&q=80" => "Foto: cancha entre arboles",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?auto=format&fit=crop&w=1200&q=80" => "Foto: jugando al futbol",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1510526292299-20af3f62d453?auto=format&fit=crop&w=1200&q=80" => "Foto: vista aerea",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1625923541779-630119dfa15d?auto=format&fit=crop&w=1200&q=80" => "Foto: vista aerea del cesped",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1630259900856-86e94006646e?auto=format&fit=crop&w=1200&q=80" => "Foto: cesped de dia",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1493538706211-316874a1e8b4?auto=format&fit=crop&w=1200&q=80" => "Foto: vista aerea de la cancha",   // Foto real de Unsplash.
        "https://images.unsplash.com/photo-1621844816188-c249945fe28e?auto=format&fit=crop&w=1200&q=80" => "Foto: cancha verde",   // Foto real de Unsplash.
        "img/canchas/dia.svg" => "Ilustracion: de dia",                                 // Dibujo guardado dentro del proyecto (funciona sin internet).
    ];                                                                                  // Cierra la constante.

    public function __construct(CanchaRepositoryInterface $canchas)                     // Recibe el repositorio.
    {                                                                                   // Abre el constructor.
        $this->canchas = $canchas;                                                      // Lo guarda.
    }                                                                                   // Cierra el constructor.

    public function listarTodas()                                                       // Todas las canchas.
    {                                                                                   // Abre el metodo.
        return $this->canchas->listarTodas();                                           // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    public function listarActivas()                                                     // Solo las activas.
    {                                                                                   // Abre el metodo.
        return $this->canchas->listarActivas();                                         // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    public function buscarPorId($id)                                                    // Una cancha por id.
    {                                                                                   // Abre el metodo.
        return $this->canchas->buscarPorId($id);                                        // Delega en el repositorio.
    }                                                                                   // Cierra el metodo.

    // Valida los datos de una cancha con el Validador central. Devuelve texto de error o "".
    private function validar($nombre, $tipo, $precio, $estado, $descripcion)            // Recibe los datos.
    {                                                                                   // Abre el metodo.
        $error = Validador::texto($nombre, 1, 50, "El nombre");                         // Nombre: obligatorio, maximo 50 caracteres.
        if ($error === "") {                                                            // Si el nombre esta bien...
            $error = Validador::texto($tipo, 1, 30, "El tipo");                         // ...valida el tipo (maximo 30).
        }                                                                               // Cierra el if.
        if ($error === "") {                                                            // Si va todo bien...
            $error = Validador::precio($precio);                                        // ...valida el precio (estricto, entre 1 y 100.000).
        }                                                                               // Cierra el if.
        if ($error === "" && !in_array($estado, ["activa", "inactiva"], true)) {        // Si el estado no es uno de los dos permitidos...
            $error = "Estado invalido";                                                 // ...error.
        }                                                                               // Cierra el if.
        if ($error === "") {                                                            // Si va todo bien...
            $error = Validador::texto($descripcion, 0, 150, "La descripcion");          // ...valida la descripcion (opcional, maximo 150).
        }                                                                               // Cierra el if.
        return $error;                                                                  // Devuelve "" o el primer error.
    }                                                                                   // Cierra el metodo.

    // Procesa la foto. Devuelve ["error" => texto, "imagen" => ruta o null].
    // Prioridad: 1) foto subida, 2) imagen de ejemplo elegida, 3) null (se deja la actual).
    private function procesarImagen($archivo, $predefinida)                             // Recibe el archivo de $_FILES (o null) y la imagen elegida.
    {                                                                                   // Abre el metodo.
        if ($archivo && isset($archivo["error"]) && $archivo["error"] !== UPLOAD_ERR_NO_FILE) { // Si el admin eligio un archivo...
            if (!is_int($archivo["error"]) || !isset($archivo["tmp_name"]) || !is_string($archivo["tmp_name"]) || !isset($archivo["size"]) || !is_int($archivo["size"])) { // Si el dato de $_FILES esta manipulado (ej: llega como lista)...
                return ["error" => "Archivo no valido", "imagen" => null];             // ...lo rechaza.
            }                                                                           // Cierra el if.
            if ($archivo["error"] !== UPLOAD_ERR_OK) {                                  // Si hubo un problema al subirlo...
                return ["error" => "No se pudo subir la imagen (maximo 2 MB)", "imagen" => null]; // ...error.
            }                                                                           // Cierra el if.
            if ($archivo["size"] > 2 * 1024 * 1024) {                                   // Si pesa mas de 2 MB...
                return ["error" => "La imagen no puede pesar mas de 2 MB", "imagen" => null]; // ...error.
            }                                                                           // Cierra el if.
            $tipos = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"]; // Tipos permitidos y su extension.
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo["tmp_name"]);        // Detecta el tipo REAL del archivo (no se fia del nombre).
            if (!isset($tipos[$mime])) {                                                // Si no es JPG, PNG ni WEBP...
                return ["error" => "La imagen debe ser JPG, PNG o WEBP", "imagen" => null]; // ...error.
            }                                                                           // Cierra el if.
            $dimensiones = @getimagesize($archivo["tmp_name"]);                         // Lee las dimensiones (solo funciona con imagenes reales).
            if (!$dimensiones || $dimensiones[0] < 1 || $dimensiones[1] < 1 || $dimensiones[0] > 4000 || $dimensiones[1] > 4000) { // Si no es una imagen valida o supera 4000x4000 pixeles...
                return ["error" => "La imagen no es valida o supera los 4000 x 4000 pixeles", "imagen" => null]; // ...error (evita imagenes "bomba" que consumen toda la memoria).
            }                                                                           // Cierra el if.
            $carpeta = __DIR__ . "/../../uploads/canchas/";                             // Carpeta donde se guardan las fotos subidas.
            if (!is_dir($carpeta)) {                                                    // Si no existe...
                mkdir($carpeta, 0755, true);                                            // ...la crea.
            }                                                                           // Cierra el if.
            $nombreArchivo = bin2hex(random_bytes(8)) . "." . $tipos[$mime];            // Nombre aleatorio (evita pisar archivos y nombres raros).
            if (!move_uploaded_file($archivo["tmp_name"], $carpeta . $nombreArchivo)) { // Mueve el archivo a su carpeta...
                return ["error" => "No se pudo guardar la imagen en el servidor", "imagen" => null]; // ...si falla, error.
            }                                                                           // Cierra el if.
            return ["error" => "", "imagen" => "uploads/canchas/" . $nombreArchivo];    // Devuelve la ruta guardada.
        }                                                                               // Cierra el if de subida.
        if ($predefinida !== "" && isset(self::IMAGENES[$predefinida])) {               // Si eligio una imagen de ejemplo valida...
            return ["error" => "", "imagen" => $predefinida];                           // ...devuelve esa.
        }                                                                               // Cierra el if.
        return ["error" => "", "imagen" => null];                                       // No hay imagen nueva.
    }                                                                                   // Cierra el metodo.

    // Borra del disco una foto subida por el admin (las imagenes de ejemplo nunca se borran).
    private function borrarImagenSubida($ruta)                                          // Recibe la ruta guardada en la base.
    {                                                                                   // Abre el metodo.
        if ($ruta && strpos($ruta, "uploads/canchas/") === 0) {                         // Solo si es una foto subida...
            $archivo = __DIR__ . "/../../" . $ruta;                                     // ...arma la ruta real...
            if (is_file($archivo)) {                                                    // ...y si existe...
                unlink($archivo);                                                       // ...la borra.
            }                                                                           // Cierra el if interno.
        }                                                                               // Cierra el if externo.
    }                                                                                   // Cierra el metodo.

    // ALTA de cancha.
    public function crear($nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida) // Recibe los datos y la imagen.
    {                                                                                   // Abre el metodo.
        $error = $this->validar($nombre, $tipo, $precio, $estado, $descripcion);        // Valida los datos.
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $img = $this->procesarImagen($archivo, $predefinida);                           // Procesa la imagen.
        if ($img["error"] !== "") {                                                     // Si la imagen fallo...
            return ["exito" => false, "mensaje" => $img["error"]];                      // ...devuelve el error.
        }                                                                               // Cierra el if.
        $cancha = new Cancha();                                                         // Crea la entidad.
        $cancha->nombre = $nombre;                                                      // Nombre.
        $cancha->tipo = $tipo;                                                          // Tipo.
        $cancha->precioHora = $precio;                                                  // Precio.
        $cancha->estado = $estado;                                                      // Estado.
        $cancha->descripcion = $descripcion !== "" ? $descripcion : null;               // Descripcion (null si esta vacia).
        $cancha->imagen = $img["imagen"];                                               // Foto.
        $this->canchas->crear($cancha);                                                 // La guarda.
        return ["exito" => true, "mensaje" => "Cancha creada correctamente"];           // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION de cancha.
    public function actualizar($id, $nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida) // Recibe el id, los datos y la imagen.
    {                                                                                   // Abre el metodo.
        $actual = $this->canchas->buscarPorId($id);                                     // Busca la cancha actual.
        if (!$actual) {                                                                 // Si no existe...
            return ["exito" => false, "mensaje" => "Cancha no encontrada"];             // ...error.
        }                                                                               // Cierra el if.
        $error = $this->validar($nombre, $tipo, $precio, $estado, $descripcion);        // Valida los datos.
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $img = $this->procesarImagen($archivo, $predefinida);                           // Procesa la imagen nueva (si hay).
        if ($img["error"] !== "") {                                                     // Si la imagen fallo...
            return ["exito" => false, "mensaje" => $img["error"]];                      // ...devuelve el error.
        }                                                                               // Cierra el if.
        $imagenAnterior = $actual->imagen;                                              // Recuerda la foto que tenia.
        $actual->nombre = $nombre;                                                      // Nombre nuevo.
        $actual->tipo = $tipo;                                                          // Tipo nuevo.
        $actual->precioHora = $precio;                                                  // Precio nuevo.
        $actual->estado = $estado;                                                      // Estado nuevo.
        $actual->descripcion = $descripcion !== "" ? $descripcion : null;               // Descripcion nueva.
        if ($img["imagen"] !== null) {                                                  // Si hay una foto nueva...
            $actual->imagen = $img["imagen"];                                           // ...la usa (si no, conserva la que tenia).
        }                                                                               // Cierra el if.
        $this->canchas->actualizar($actual);                                            // Guarda los cambios.
        if ($img["imagen"] !== null && $img["imagen"] !== $imagenAnterior) {            // Si se reemplazo la foto...
            $this->borrarImagenSubida($imagenAnterior);                                 // ...borra la foto subida anterior.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Cancha actualizada correctamente"];      // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // BAJA de cancha: no deja borrarla si tiene reservas (en ese caso conviene ponerla "inactiva").
    public function eliminar($id)                                                       // Recibe el id.
    {                                                                                   // Abre el metodo.
        $actual = $this->canchas->buscarPorId($id);                                     // Busca la cancha (para saber su foto).
        try {                                                                           // Intenta borrar.
            $this->canchas->eliminar($id);                                              // Ejecuta el DELETE.
        } catch (PDOException $e) {                                                     // Si la base rechaza el borrado...
            return ["exito" => false, "mensaje" => "No se puede eliminar: la cancha tiene reservas. Ponela como inactiva."]; // ...avisa el motivo.
        }                                                                               // Cierra el catch.
        if ($actual) {                                                                  // Si la cancha existia...
            $this->borrarImagenSubida($actual->imagen);                                 // ...borra su foto subida.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Cancha eliminada correctamente"];        // Devuelve exito.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
