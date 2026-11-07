<?php                                                                                   // Abre el bloque de codigo PHP.
// Controlador de administracion de reservas (alta, modificacion y baja). El router ya verifico que sea admin.

class AdminReservaController extends Controlador                                        // Hereda las herramientas de Controlador.
{                                                                                       // Abre la clase.
    private $reservas;                                                                  // Servicio de reservas.
    private $canchas;                                                                   // Servicio de canchas.
    private $usuarios;                                                                  // Servicio de usuarios.

    public function __construct(ReservaService $reservas, CanchaService $canchas, UsuarioService $usuarios) // Recibe los servicios.
    {                                                                                   // Abre el constructor.
        $this->reservas = $reservas;                                                    // Guarda el servicio de reservas.
        $this->canchas = $canchas;                                                      // Guarda el servicio de canchas.
        $this->usuarios = $usuarios;                                                    // Guarda el servicio de usuarios.
    }                                                                                   // Cierra el constructor.

    public function listar()                                                            // Lista de todas las reservas.
    {                                                                                   // Abre el metodo.
        $this->render("admin/reservas", ["reservas" => $this->reservas->listarTodas()]); // Muestra la plantilla.
    }                                                                                   // Cierra el metodo.

    public function cancelar()                                                          // Cancela una reserva (baja logica).
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo por formulario.
            $this->avisar($this->reservas->adminCancelar((int)$this->post("reserva_id"))); // Pide cancelar y guarda el mensaje.
        }                                                                               // Cierra el if.
        redirigir("admin_reservas");                                                    // Vuelve a la lista.
    }                                                                                   // Cierra el metodo.

    public function eliminar()                                                          // Elimina una reserva (baja fisica).
    {                                                                                   // Abre el metodo.
        if ($this->esPost()) {                                                          // Solo por formulario.
            $this->avisar($this->reservas->adminEliminar((int)$this->post("reserva_id"))); // Pide eliminar y guarda el mensaje.
        }                                                                               // Cierra el if.
        redirigir("admin_reservas");                                                    // Vuelve a la lista.
    }                                                                                   // Cierra el metodo.

    // Formulario de ALTA (sin id) o MODIFICACION (con id).
    public function formulario()                                                        // No recibe parametros.
    {                                                                                   // Abre el metodo.
        $id = (int)($this->consulta("id") !== "" ? $this->consulta("id") : $this->post("id", "0")); // Si viene un id es modificacion; si no, alta.
        $reserva = $id > 0 ? $this->reservas->buscarPorId($id) : null;                  // Busca la reserva si estamos editando.
        if ($id > 0 && !$reserva) {                                                     // Si pidio editar una que no existe...
            Sesion::mensaje("error", "Reserva no encontrada");                          // ...guarda el error...
            redirigir("admin_reservas");                                                // ...y vuelve a la lista.
        }                                                                               // Cierra el if.

        $error = "";                                                                    // Error del formulario.
        if ($this->esPost()) {                                                          // Si envio el formulario...
            $canchaId = (int)$this->post("cancha_id");                                  // Cancha elegida.
            $fecha = $this->post("fecha");                                              // Fecha elegida.
            $hora = $this->post("hora");                                                // Hora elegida.
            if ($reserva) {                                                             // Si estamos modificando...
                $resultado = $this->reservas->adminModificar($id, $canchaId, $fecha, $hora, $this->post("estado", "confirmada")); // ...pide modificar.
            } else {                                                                    // Si estamos dando de alta...
                $resultado = $this->reservas->adminCrear((int)$this->post("usuario_id"), $canchaId, $fecha, $hora); // ...pide crear.
            }                                                                           // Cierra el if/else.
            if ($resultado["exito"]) {                                                  // Si salio bien...
                $this->avisar($resultado);                                              // ...guarda el mensaje...
                redirigir("admin_reservas");                                            // ...y vuelve a la lista.
            }                                                                           // Cierra el if.
            $error = $resultado["mensaje"];                                             // Si salio mal, guarda el error.
        }                                                                               // Cierra el if del POST.

        $this->render("admin/reserva_form", [                                           // Muestra la plantilla con estos datos:
            "reserva" => $reserva,                                                      // La reserva (o null si es alta).
            "id" => $id,                                                                // El id (0 si es alta).
            "canchas" => $reserva ? $this->canchas->listarTodas() : $this->canchas->listarActivas(), // Al editar, todas las canchas; al crear, solo las activas.
            "clientes" => $this->usuarios->listarTodos(),                               // Usuarios para el desplegable del alta.
            "horas" => $this->reservas->horasDisponibles(),                             // Horarios posibles.
            "error" => $error,                                                          // Error (si hay).
            "vCancha" => $this->post("cancha_id", $reserva ? (string)$reserva->canchaId : ""), // Valor a mostrar en cancha.
            "vFecha" => $this->post("fecha", $reserva ? $reserva->fecha : date("Y-m-d")), // Valor a mostrar en fecha.
            "vHora" => $this->post("hora", $reserva ? substr($reserva->horaInicio, 0, 5) : ""), // Valor a mostrar en hora.
            "vEstado" => $this->post("estado", $reserva ? $reserva->estado : "confirmada"), // Valor a mostrar en estado.
            "vUsuario" => $this->post("usuario_id"),                                    // Valor a mostrar en cliente (solo alta).
        ]);                                                                             // Cierra los datos.
    }                                                                                   // Cierra el metodo.
}                                                                                       // Cierra la clase.
