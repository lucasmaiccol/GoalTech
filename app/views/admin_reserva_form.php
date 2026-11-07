<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/ReservaController.php";                         // Carga el controlador de reservas.
require_once __DIR__ . "/../models/Reserva.php";                                        // Carga el modelo de reservas.
require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo de canchas.
require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo de usuarios.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$reservaCtrl = new ReservaController($conexion);                                        // Crea el controlador de reservas.
$reservaModelo = new Reserva($conexion);                                                // Crea el modelo de reservas.
$canchaModelo = new Cancha($conexion);                                                  // Crea el modelo de canchas.
$usuarioModelo = new Usuario($conexion);                                                // Crea el modelo de usuarios.

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);                                          // Si viene un id, es MODIFICACION; si no, es ALTA.
$reserva = $id > 0 ? $reservaModelo->buscarPorId($id) : null;                           // Busca la reserva si estamos editando.
if ($id > 0 && !$reserva) {                                                             // Si pidio editar una reserva que no existe...
    setMensaje("error", "Reserva no encontrada");                                       // ...guarda el error.
    header("Location: admin_reservas.php");                                             // ...y vuelve a la lista.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$error = "";                                                                            // Aca se guarda el error del formulario.

if ($_SERVER["REQUEST_METHOD"] === "POST") {                                            // Si el admin envio el formulario...
    $canchaId = (int)($_POST["cancha_id"] ?? 0);                                        // Cancha elegida.
    $fecha = trim($_POST["fecha"] ?? "");                                               // Fecha elegida.
    $hora = trim($_POST["hora"] ?? "");                                                 // Hora elegida.
    if ($reserva) {                                                                     // Si estamos modificando...
        $estado = $_POST["estado"] ?? "confirmada";                                     // ...lee tambien el estado.
        $resultado = $reservaCtrl->adminModificar($id, $canchaId, $fecha, $hora, $estado); // ...pide modificar la reserva.
    } else {                                                                            // Si estamos dando de alta...
        $usuarioId = (int)($_POST["usuario_id"] ?? 0);                                  // ...lee el cliente elegido.
        $resultado = $reservaCtrl->adminCrear($usuarioId, $canchaId, $fecha, $hora);    // ...pide crear la reserva.
    }                                                                                   // Cierra el if/else.
    if ($resultado["exito"]) {                                                          // Si salio bien...
        setMensaje("exito", $resultado["mensaje"]);                                     // ...guarda el mensaje de exito.
        header("Location: admin_reservas.php");                                         // ...y vuelve a la lista.
        exit;                                                                           // Corta la ejecucion.
    }                                                                                   // Cierra el if.
    $error = $resultado["mensaje"];                                                     // Si salio mal, guarda el error para mostrarlo.
}                                                                                       // Cierra el if del POST.

$canchas = $reserva ? $canchaModelo->listarTodas() : $canchaModelo->listarActivas();    // Al editar muestra todas las canchas; al crear, solo las activas.
$clientes = $usuarioModelo->listarTodos();                                              // Lista de usuarios para el desplegable del alta.
$horas = $reservaCtrl->horasDisponibles();                                              // Lista de horarios posibles.

$vCancha = $_POST["cancha_id"] ?? ($reserva["cancha_id"] ?? "");                        // Valor a mostrar en cancha (lo enviado, o el actual).
$vFecha = $_POST["fecha"] ?? ($reserva["fecha"] ?? date("Y-m-d"));                      // Valor a mostrar en fecha.
$vHora = $_POST["hora"] ?? ($reserva ? substr($reserva["hora_inicio"], 0, 5) : "");     // Valor a mostrar en hora.
$vEstado = $_POST["estado"] ?? ($reserva["estado"] ?? "confirmada");                    // Valor a mostrar en estado.
$vUsuario = $_POST["usuario_id"] ?? "";                                                 // Valor a mostrar en cliente (solo alta).

$tituloPagina = $reserva ? "Editar reserva" : "Nueva reserva";                          // Titulo segun el modo.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
?>
<h1 class="panel-titulo"><?php echo $reserva ? "Editar reserva #" . (int)$id : "Nueva reserva"; ?></h1>
<!-- Titulo segun alta o modificacion -->

<?php if ($error !== "") { ?>
    <!-- Si hubo error, lo muestra -->
    <p class="error"><?php echo h($error); ?></p>
    <!-- Cuadro rojo con el error -->
<?php } ?>

<section class="tarjeta tarjeta-angosta">
    <!-- Caja blanca con el formulario -->
    <form class="formulario-panel" action="admin_reserva_form.php" method="post">
        <!-- Formulario que se envia a esta misma pagina -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->

        <?php if ($reserva) { ?>
            <!-- Al editar, el cliente no se puede cambiar: solo se muestra -->
            <div class="campo">
                <!-- Grupo del cliente -->
                <label>Cliente</label>
                <!-- Etiqueta -->
                <input type="text" value="<?php echo h($reserva["usuario_nombre"] . " (" . $reserva["usuario_email"] . ")"); ?>" disabled>
                <!-- Campo deshabilitado, solo informativo -->
            </div>
        <?php } else { ?>
            <!-- Al crear, se elige el cliente -->
            <div class="campo">
                <!-- Grupo del cliente -->
                <label for="usuario_id">Cliente</label>
                <!-- Etiqueta -->
                <select id="usuario_id" name="usuario_id" required>
                    <!-- Lista desplegable de usuarios -->
                    <option value="">Elegir cliente...</option>
                    <!-- Opcion vacia inicial -->
                    <?php foreach ($clientes as $u) { ?>
                        <!-- Recorre cada usuario -->
                        <option value="<?php echo (int)$u["id"]; ?>" <?php echo (string)$vUsuario === (string)$u["id"] ? "selected" : ""; ?>><?php echo h($u["nombre"] . " (" . $u["email"] . ")"); ?></option>
                        <!-- Una opcion por usuario; "selected" deja marcado el elegido -->
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <div class="campo">
            <!-- Grupo de la cancha -->
            <label for="cancha_id">Cancha</label>
            <!-- Etiqueta -->
            <select id="cancha_id" name="cancha_id" required>
                <!-- Lista desplegable de canchas -->
                <?php foreach ($canchas as $c) { ?>
                    <!-- Recorre cada cancha -->
                    <option value="<?php echo (int)$c["id"]; ?>" <?php echo (string)$vCancha === (string)$c["id"] ? "selected" : ""; ?>><?php echo h($c["nombre"] . " - " . $c["tipo"] . ($c["estado"] === "inactiva" ? " (inactiva)" : "")); ?></option>
                    <!-- Una opcion por cancha, marcando la actual -->
                <?php } ?>
            </select>
        </div>
        <div class="campo">
            <!-- Grupo de la fecha -->
            <label for="fecha">Fecha</label>
            <!-- Etiqueta -->
            <input type="date" id="fecha" name="fecha" value="<?php echo h($vFecha); ?>" required>
            <!-- Selector de fecha -->
        </div>
        <div class="campo">
            <!-- Grupo del horario -->
            <label for="hora">Horario (1 hora)</label>
            <!-- Etiqueta -->
            <select id="hora" name="hora" required>
                <!-- Lista desplegable de horarios -->
                <?php foreach ($horas as $hr) { ?>
                    <!-- Recorre cada horario -->
                    <option value="<?php echo h($hr); ?>" <?php echo $vHora === $hr ? "selected" : ""; ?>><?php echo h($hr); ?></option>
                    <!-- Una opcion por horario, marcando el actual -->
                <?php } ?>
            </select>
        </div>

        <?php if ($reserva) { ?>
            <!-- El estado solo se edita al modificar -->
            <div class="campo">
                <!-- Grupo del estado -->
                <label for="estado">Estado</label>
                <!-- Etiqueta -->
                <select id="estado" name="estado">
                    <!-- Lista desplegable de estados -->
                    <option value="confirmada" <?php echo $vEstado === "confirmada" ? "selected" : ""; ?>>Confirmada</option>
                    <!-- Opcion confirmada -->
                    <option value="cancelada" <?php echo $vEstado === "cancelada" ? "selected" : ""; ?>>Cancelada</option>
                    <!-- Opcion cancelada -->
                </select>
            </div>
        <?php } else { ?>
            <!-- Al crear, se aclara como se registra el pago -->
            <p class="aviso">La reserva se registra con pago en efectivo (monto segun el precio de la cancha).</p>
            <!-- Aviso informativo -->
        <?php } ?>

        <div class="fila">
            <!-- Fila con los botones -->
            <button class="boton-panel" type="submit"><?php echo $reserva ? "Guardar cambios" : "Crear reserva"; ?></button>
            <!-- Boton de guardar -->
            <a class="boton-chico" href="admin_reservas.php">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../includes/footer.php"; ?>
