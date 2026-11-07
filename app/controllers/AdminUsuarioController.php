<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de administracion de usuarios (alta, modificacion y baja). El router ya verifico que sea admin.

class AdminUsuarioController extends Controlador                                        // Hereda las herramientas de Controlador.
{                                                                                       // Abre la clase.
    private $usuarios;                                                                  // Servicio de usuarios.

    public function __construct(UsuarioService $usuarios)                               // Recibe el servicio.
    {                                                                                   // Abre el constructor.
        $this->usuarios = $usuarios;                                                    // Lo guarda.
    }                                                                                   // Cierra el constructor.

    public function listar()                                                            // Lista de usuarios.
    {                                                                                   // Abre el metodo.
        $this->render("admin/usuarios", ["usuarios" => $this->usuarios->listarTodos()]); // Muestra la plantilla.
    }                                                                                   // Cierra el metodo.

    public function eliminar()                                                          // Elimina un usuario.
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo por formulario.
            $this->avisar($this->usuarios->eliminar((int)$this->post("usuario_id"), $this->usuario->id)); // Pide eliminar (no deja borrarse a si mismo).
        }                                                                               // Cierra el if.
        redirigir("admin_usuarios");                                                    // Vuelve a la lista.
    }                                                                                   // Cierra el metodo.

    // Formulario de ALTA (sin id) o MODIFICACION (con id).
    public function formulario()                                                        // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $id = (int)($this->consulta("id") !== "" ? $this->consulta("id") : $this->post("id", "0")); // Si viene un id es modificacion; si no, alta.
        $editado = $id > 0 ? $this->usuarios->buscarPorId($id) : null;                  // Busca el usuario si estamos editando.
        if ($id > 0 && !$editado) {                                                     // Si no existe...
            Sesion::mensaje("error", "Usuario no encontrado");                          // ...guarda el error...
            redirigir("admin_usuarios");                                                // ...y vuelve a la lista.
        }                                                                               // Cierra el if.

        $error = "";                                                                    // Error del formulario.
        if ($this->esPost()) {                                                          // Si envio el formulario...
            $nombre = $this->post("nombre");                                            // Lee el nombre.
            $telefono = $this->post("telefono");                                        // Lee el telefono.
            $rol = $this->post("rol", "cliente");                                       // Lee el rol.
            $password = $this->postCrudo("password");                                   // Lee la contraseña (sin recortar).
            if ($editado) {                                                             // Si estamos modificando...
                $resultado = $this->usuarios->actualizar($id, $nombre, $telefono, $rol, $password, $this->usuario->id); // ...pide actualizar.
            } else {                                                                    // Si estamos dando de alta...
                $resultado = $this->usuarios->crear($nombre, $this->post("email"), $telefono, $password, $rol); // ...pide crear.
            }                                                                           // Cierra el if/else.
            if ($resultado["exito"]) {                                                  // Si salio bien...
                $this->avisar($resultado);                                              // ...guarda el mensaje...
                redirigir("admin_usuarios");                                            // ...y vuelve a la lista.
            }                                                                           // Cierra el if.
            $error = $resultado["mensaje"];                                             // Si salio mal, guarda el error.
        }                                                                               // Cierra el if del POST.

        $this->render("admin/usuario_form", [                                           // Muestra la plantilla con estos datos:
            "editado" => $editado,                                                      // El usuario que se edita (o null si es alta).
            "id" => $id,                                                                // El id (0 si es alta).
            "error" => $error,                                                          // Error (si hay).
            "vNombre" => $this->post("nombre", $editado ? $editado->nombre : ""),       // Valor a mostrar en nombre.
            "vEmail" => $this->post("email", $editado ? $editado->email : ""),          // Valor a mostrar en email.
            "vTelefono" => $this->post("telefono", $editado ? $editado->telefono : ""), // Valor a mostrar en telefono.
            "vRol" => $this->post("rol", $editado ? $editado->rol : "cliente"),         // Valor a mostrar en rol.
        ]);                                                                             // Cierra los datos.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
