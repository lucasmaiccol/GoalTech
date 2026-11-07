<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de administracion: alta, modificacion y baja de canchas y usuarios (solo para admin).

require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo Cancha.
require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo Usuario.

class AdminController                                                                   // Define la clase del controlador.
{                                                                                       // Abre la clase.
    private $canchaModelo;                                                              // Modelo de canchas.
    private $usuarioModelo;                                                             // Modelo de usuarios.

    // Imagenes de ejemplo que el admin puede elegir para una cancha (link o ruta => nombre que se muestra).
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

    public function __construct($conexion)                                              // Constructor: recibe la conexion.
    {                                                                                   // Abre el constructor.
        $this->canchaModelo = new Cancha($conexion);                                    // Crea el modelo de canchas.
        $this->usuarioModelo = new Usuario($conexion);                                  // Crea el modelo de usuarios.
    }                                                                                   // Cierra el constructor.

    // Valida los datos de una cancha. Devuelve texto de error o "" si esta todo bien.
    private function validarCancha($nombre, $tipo, $precio, $estado, $descripcion)      // Recibe los datos de la cancha.
    {                                                                                   // Abre el metodo.
        if ($nombre === "" || $tipo === "") {                                           // Si falta nombre o tipo...
            return "Nombre y tipo son obligatorios";                                    // ...error.
        }                                                                               // Cierra el if.
        if (!is_numeric($precio) || $precio <= 0) {                                     // Si el precio no es un numero mayor a cero...
            return "El precio por hora debe ser un numero mayor a 0";                   // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($estado, ["activa", "inactiva"], true)) {                         // Si el estado no es uno de los permitidos...
            return "Estado invalido";                                                   // ...error.
        }                                                                               // Cierra el if.
        if (strlen($descripcion) > 150) {                                               // Si la descripcion supera los 150 caracteres...
            return "La descripcion no puede superar los 150 caracteres";                // ...error (es el limite de la columna).
        }                                                                               // Cierra el if.
        return "";                                                                      // Todo bien.
    }                                                                                   // Cierra el metodo.

    // Procesa la foto de una cancha. Devuelve ["error" => texto, "imagen" => ruta o null].
    // Prioridad: 1) foto subida, 2) imagen predefinida elegida, 3) null (se deja la actual o la de por defecto).
    private function procesarImagen($archivo, $predefinida)                             // Recibe el archivo de $_FILES (o null) y la imagen predefinida elegida.
    {                                                                                   // Abre el metodo.
        if ($archivo && $archivo["error"] !== UPLOAD_ERR_NO_FILE) {                     // Si el admin eligio un archivo para subir...
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
            $carpeta = __DIR__ . "/../../uploads/canchas/";                             // Carpeta donde se guardan las fotos subidas.
            if (!is_dir($carpeta)) {                                                    // Si la carpeta no existe...
                mkdir($carpeta, 0755, true);                                            // ...la crea.
            }                                                                           // Cierra el if.
            $nombreArchivo = bin2hex(random_bytes(8)) . "." . $tipos[$mime];            // Nombre aleatorio (evita pisar archivos y nombres raros).
            if (!move_uploaded_file($archivo["tmp_name"], $carpeta . $nombreArchivo)) { // Mueve el archivo a su carpeta definitiva...
                return ["error" => "No se pudo guardar la imagen en el servidor", "imagen" => null]; // ...si falla, error.
            }                                                                           // Cierra el if.
            return ["error" => "", "imagen" => "uploads/canchas/" . $nombreArchivo];    // Devuelve la ruta guardada.
        }                                                                               // Cierra el if de subida.
        if ($predefinida !== "" && isset(self::IMAGENES[$predefinida])) {               // Si eligio una imagen predefinida valida...
            return ["error" => "", "imagen" => $predefinida];                           // ...devuelve esa ruta.
        }                                                                               // Cierra el if.
        return ["error" => "", "imagen" => null];                                       // No hay imagen nueva.
    }                                                                                   // Cierra el metodo.

    // Borra del disco una foto subida por el admin (las imagenes de ejemplo nunca se borran).
    private function borrarImagenSubida($ruta)                                          // Recibe la ruta guardada en la base.
    {                                                                                   // Abre el metodo.
        if ($ruta && strpos($ruta, "uploads/canchas/") === 0) {                         // Solo si es una foto subida (empieza con uploads/canchas/)...
            $archivo = __DIR__ . "/../../" . $ruta;                                     // ...arma la ruta real en el disco...
            if (is_file($archivo)) {                                                    // ...y si el archivo existe...
                unlink($archivo);                                                       // ...lo borra.
            }                                                                           // Cierra el if interno.
        }                                                                               // Cierra el if externo.
    }                                                                                   // Cierra el metodo.

    // ALTA de cancha.
    public function crearCancha($nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida) // Recibe los datos del formulario y la imagen.
    {                                                                                   // Abre el metodo.
        $error = $this->validarCancha($nombre, $tipo, $precio, $estado, $descripcion);  // Valida los datos.
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $img = $this->procesarImagen($archivo, $predefinida);                           // Procesa la imagen elegida o subida.
        if ($img["error"] !== "") {                                                     // Si la imagen fallo...
            return ["exito" => false, "mensaje" => $img["error"]];                      // ...devuelve el error.
        }                                                                               // Cierra el if.
        $this->canchaModelo->crear($nombre, $tipo, $precio, $estado, $descripcion !== "" ? $descripcion : null, $img["imagen"]); // Guarda la cancha.
        return ["exito" => true, "mensaje" => "Cancha creada correctamente"];           // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION de cancha.
    public function actualizarCancha($id, $nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida) // Recibe el id, los datos y la imagen.
    {                                                                                   // Abre el metodo.
        $actual = $this->canchaModelo->buscarPorId($id);                                // Busca la cancha actual.
        if (!$actual) {                                                                 // Si la cancha no existe...
            return ["exito" => false, "mensaje" => "Cancha no encontrada"];             // ...error.
        }                                                                               // Cierra el if.
        $error = $this->validarCancha($nombre, $tipo, $precio, $estado, $descripcion);  // Valida los datos.
        if ($error !== "") {                                                            // Si hay error...
            return ["exito" => false, "mensaje" => $error];                             // ...lo devuelve.
        }                                                                               // Cierra el if.
        $img = $this->procesarImagen($archivo, $predefinida);                           // Procesa la imagen nueva (si hay).
        if ($img["error"] !== "") {                                                     // Si la imagen fallo...
            return ["exito" => false, "mensaje" => $img["error"]];                      // ...devuelve el error.
        }                                                                               // Cierra el if.
        $imagenFinal = $img["imagen"] ?? $actual["imagen"];                             // Si no hay imagen nueva, conserva la que ya tenia.
        $this->canchaModelo->actualizar($id, $nombre, $tipo, $precio, $estado, $descripcion !== "" ? $descripcion : null, $imagenFinal); // Guarda los cambios.
        if ($img["imagen"] !== null && $img["imagen"] !== $actual["imagen"]) {          // Si se reemplazo la imagen...
            $this->borrarImagenSubida($actual["imagen"]);                               // ...borra la foto subida anterior (si era una subida).
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Cancha actualizada correctamente"];      // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // BAJA de cancha: no deja borrarla si tiene reservas (en ese caso conviene ponerla "inactiva").
    public function eliminarCancha($id)                                                 // Recibe el id.
    {                                                                                   // Abre el metodo.
        $actual = $this->canchaModelo->buscarPorId($id);                                // Busca la cancha (para saber su foto).
        try {                                                                           // Intenta borrar.
            $this->canchaModelo->eliminar($id);                                         // Ejecuta el DELETE.
        } catch (PDOException $e) {                                                     // Si la base rechaza el borrado...
            return ["exito" => false, "mensaje" => "No se puede eliminar: la cancha tiene reservas. Ponela como inactiva."]; // ...avisa el motivo.
        }                                                                               // Cierra el catch.
        if ($actual) {                                                                  // Si la cancha existia...
            $this->borrarImagenSubida($actual["imagen"]);                               // ...borra su foto subida del disco.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Cancha eliminada correctamente"];        // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // ALTA de usuario hecha por el admin (puede elegir el rol).
    public function crearUsuario($nombre, $email, $telefono, $password, $rol)           // Recibe los datos del formulario.
    {                                                                                   // Abre el metodo.
        if ($nombre === "" || $telefono === "") {                                       // Si falta nombre o telefono...
            return ["exito" => false, "mensaje" => "Nombre y telefono son obligatorios"]; // ...error.
        }                                                                               // Cierra el if.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {                               // Si el email no es valido...
            return ["exito" => false, "mensaje" => "El correo no es valido"];           // ...error.
        }                                                                               // Cierra el if.
        if (strlen($password) < 6) {                                                    // Si la contraseña es muy corta...
            return ["exito" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]; // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($rol, ["cliente", "admin"], true)) {                              // Si el rol no es valido...
            return ["exito" => false, "mensaje" => "Rol invalido"];                     // ...error.
        }                                                                               // Cierra el if.
        if ($this->usuarioModelo->buscarPorEmail($email)) {                             // Si el email ya existe...
            return ["exito" => false, "mensaje" => "Ese correo ya esta registrado"];    // ...error.
        }                                                                               // Cierra el if.
        $this->usuarioModelo->crear($nombre, $email, $telefono, password_hash($password, PASSWORD_DEFAULT), $rol); // Guarda con la contraseña hasheada.
        return ["exito" => true, "mensaje" => "Usuario creado correctamente"];          // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // MODIFICACION de usuario. Si $password viene vacia, no se cambia la contraseña.
    public function actualizarUsuario($id, $nombre, $telefono, $rol, $password, $idLogueado) // Recibe el id, los datos y el id del admin actual.
    {                                                                                   // Abre el metodo.
        if (!$this->usuarioModelo->buscarPorId($id)) {                                  // Si el usuario no existe...
            return ["exito" => false, "mensaje" => "Usuario no encontrado"];            // ...error.
        }                                                                               // Cierra el if.
        if ($nombre === "" || $telefono === "") {                                       // Si falta nombre o telefono...
            return ["exito" => false, "mensaje" => "Nombre y telefono son obligatorios"]; // ...error.
        }                                                                               // Cierra el if.
        if (!in_array($rol, ["cliente", "admin"], true)) {                              // Si el rol no es valido...
            return ["exito" => false, "mensaje" => "Rol invalido"];                     // ...error.
        }                                                                               // Cierra el if.
        if ((int)$id === (int)$idLogueado && $rol !== "admin") {                        // Si el admin intenta sacarse su propio rol de admin...
            return ["exito" => false, "mensaje" => "No podes quitarte el rol de admin a vos mismo"]; // ...lo impide (se quedaria sin acceso).
        }                                                                               // Cierra el if.
        if ($password !== "" && strlen($password) < 6) {                                // Si escribio contraseña nueva pero es corta...
            return ["exito" => false, "mensaje" => "La contraseña nueva debe tener al menos 6 caracteres"]; // ...error.
        }                                                                               // Cierra el if.
        $this->usuarioModelo->actualizar($id, $nombre, $telefono, $rol);                // Guarda nombre, telefono y rol.
        if ($password !== "") {                                                         // Si escribio una contraseña nueva...
            $this->usuarioModelo->actualizarPassword($id, password_hash($password, PASSWORD_DEFAULT)); // ...la guarda hasheada.
        }                                                                               // Cierra el if.
        return ["exito" => true, "mensaje" => "Usuario actualizado correctamente"];     // Devuelve exito.
    }                                                                                   // Cierra el metodo.

    // BAJA de usuario: no deja borrarse a si mismo ni borrar usuarios con reservas.
    public function eliminarUsuario($id, $idLogueado)                                   // Recibe el id a borrar y el id del admin actual.
    {                                                                                   // Abre el metodo.
        if ((int)$id === (int)$idLogueado) {                                            // Si intenta borrarse a si mismo...
            return ["exito" => false, "mensaje" => "No podes eliminar tu propia cuenta"]; // ...error.
        }                                                                               // Cierra el if.
        try {                                                                           // Intenta borrar.
            $this->usuarioModelo->eliminar($id);                                        // Ejecuta el DELETE.
        } catch (PDOException $e) {                                                     // Si la base rechaza el borrado...
            return ["exito" => false, "mensaje" => "No se puede eliminar: el usuario tiene reservas asociadas"]; // ...avisa el motivo.
        }                                                                               // Cierra el catch.
        return ["exito" => true, "mensaje" => "Usuario eliminado correctamente"];       // Devuelve exito.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
