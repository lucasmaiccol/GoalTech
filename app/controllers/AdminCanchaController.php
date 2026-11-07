<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de administracion de canchas (alta, modificacion y baja). El router ya verifico que sea admin.

class AdminCanchaController extends Controlador                                         // Hereda las herramientas de Controlador.
{                                                                                       // Abre la clase.
    private $canchas;                                                                   // Servicio de canchas.

    public function __construct(CanchaService $canchas)                                 // Recibe el servicio.
    {                                                                                   // Abre el constructor.
        $this->canchas = $canchas;                                                      // Lo guarda.
    }                                                                                   // Cierra el constructor.

    public function listar()                                                            // Lista de canchas.
    {                                                                                   // Abre el metodo.
        $this->render("admin/canchas", ["canchas" => $this->canchas->listarTodas()]);   // Muestra la plantilla.
    }                                                                                   // Cierra el metodo.

    public function eliminar()                                                          // Elimina una cancha.
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo por formulario.
            $this->avisar($this->canchas->eliminar((int)$this->post("cancha_id")));     // Pide eliminar y guarda el mensaje.
        }                                                                               // Cierra el if.
        redirigir("admin_canchas");                                                     // Vuelve a la lista.
    }                                                                                   // Cierra el metodo.

    // Formulario de ALTA (sin id) o MODIFICACION (con id).
    public function formulario()                                                        // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $id = (int)($this->consulta("id") !== "" ? $this->consulta("id") : $this->post("id", "0")); // Si viene un id es modificacion; si no, alta.
        $cancha = $id > 0 ? $this->canchas->buscarPorId($id) : null;                    // Busca la cancha si estamos editando.
        if ($id > 0 && !$cancha) {                                                      // Si no existe...
            Sesion::mensaje("error", "Cancha no encontrada");                           // ...guarda el error...
            redirigir("admin_canchas");                                                 // ...y vuelve a la lista.
        }                                                                               // Cierra el if.

        $error = "";                                                                    // Error del formulario.
        if ($this->esPost()) {                                                          // Si envio el formulario...
            $nombre = $this->post("nombre");                                            // Lee el nombre.
            $tipo = $this->post("tipo");                                                // Lee el tipo.
            $precio = $this->post("precio_hora");                                       // Lee el precio.
            $estado = $this->post("estado", "activa");                                  // Lee el estado.
            $descripcion = $this->post("descripcion");                                  // Lee la descripcion.
            $archivo = $_FILES["imagen"] ?? null;                                       // Lee la foto subida (si eligio una).
            $predefinida = $this->post("imagen_predefinida");                           // Lee la imagen de ejemplo elegida.
            if ($cancha) {                                                              // Si estamos modificando...
                $resultado = $this->canchas->actualizar($id, $nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida); // ...pide actualizar.
            } else {                                                                    // Si estamos dando de alta...
                $resultado = $this->canchas->crear($nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida); // ...pide crear.
            }                                                                           // Cierra el if/else.
            if ($resultado["exito"]) {                                                  // Si salio bien...
                $this->avisar($resultado);                                              // ...guarda el mensaje...
                redirigir("admin_canchas");                                             // ...y vuelve a la lista.
            }                                                                           // Cierra el if.
            $error = $resultado["mensaje"];                                             // Si salio mal, guarda el error.
        }                                                                               // Cierra el if del POST.

        $this->render("admin/cancha_form", [                                            // Muestra la plantilla con estos datos:
            "cancha" => $cancha,                                                        // La cancha (o null si es alta).
            "id" => $id,                                                                // El id (0 si es alta).
            "error" => $error,                                                          // Error (si hay).
            "imagenes" => CanchaService::IMAGENES,                                      // Imagenes de ejemplo para elegir.
            "vNombre" => $this->post("nombre", $cancha ? $cancha->nombre : ""),         // Valor a mostrar en nombre.
            "vTipo" => $this->post("tipo", $cancha ? $cancha->tipo : ""),               // Valor a mostrar en tipo.
            "vPrecio" => $this->post("precio_hora", $cancha ? (string)$cancha->precioHora : ""), // Valor a mostrar en precio.
            "vEstado" => $this->post("estado", $cancha ? $cancha->estado : "activa"),   // Valor a mostrar en estado.
            "vDescripcion" => $this->post("descripcion", $cancha ? (string)$cancha->descripcion : ""), // Valor a mostrar en descripcion.
        ]);                                                                             // Cierra los datos.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
