<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/ReservaController.php";                         // Carga el controlador de reservas.
require_once __DIR__ . "/../models/Reserva.php";                                        // Carga el modelo de reservas.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$reservaCtrl = new ReservaController($conexion);                                        // Crea el controlador de reservas.
$reservaModelo = new Reserva($conexion);                                                // Crea el modelo de reservas.

if ($_SERVER["REQUEST_METHOD"] === "POST") {                                            // Si el admin apreto un boton (cancelar o eliminar)...
    $accion = $_POST["accion"] ?? "";                                                   // Lee que accion pidio.
    $id = (int)($_POST["reserva_id"] ?? 0);                                             // Lee el id de la reserva.
    if ($accion === "cancelar") {                                                       // Si pidio cancelar...
        $resultado = $reservaCtrl->adminCancelar($id);                                  // ...cancela la reserva (queda en el historial).
    } elseif ($accion === "eliminar") {                                                 // Si pidio eliminar...
        $resultado = $reservaCtrl->adminEliminar($id);                                  // ...la borra definitivamente.
    } else {                                                                            // Si la accion no es ninguna conocida...
        $resultado = ["exito" => false, "mensaje" => "Accion no valida"];               // ...error.
    }                                                                                   // Cierra el if/elseif/else.
    setMensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]);         // Guarda el mensaje para mostrarlo.
    header("Location: admin_reservas.php");                                             // Recarga la pagina (evita reenviar el formulario).
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if del POST.

$reservas = $reservaModelo->listarTodas();                                              // Trae todas las reservas del sistema.

$tituloPagina = "Administrar reservas";                                                 // Titulo que usa el header.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Reservas</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="admin_reserva_form.php">+ Nueva reserva</a>
    <!-- Enlace al formulario de alta -->
</div>

<section class="tarjeta">
    <!-- Caja blanca con la tabla -->
    <?php if (count($reservas) === 0) { ?>
        <!-- Si no hay reservas, avisa -->
        <p>No hay reservas registradas.</p>
        <!-- Mensaje informativo -->
    <?php } else { ?>
        <!-- Si hay reservas, las lista -->
        <div class="tabla-scroll">
            <!-- Permite deslizar la tabla en celulares -->
            <table class="tabla">
                <!-- Tabla de reservas -->
                <thead>
                    <!-- Encabezado -->
                    <tr><th>#</th><th>Cliente</th><th>Cancha</th><th>Fecha</th><th>Horario</th><th>Monto</th><th>Pago</th><th>Estado</th><th>Acciones</th></tr>
                    <!-- Nombres de las columnas -->
                </thead>
                <tbody>
                    <!-- Cuerpo -->
                    <?php foreach ($reservas as $r) { ?>
                        <!-- Recorre cada reserva -->
                        <tr>
                            <!-- Una fila por reserva -->
                            <td><?php echo (int)$r["id"]; ?></td>
                            <!-- Numero de reserva -->
                            <td><?php echo h($r["usuario_nombre"]); ?><br><small><?php echo h($r["usuario_email"]); ?></small></td>
                            <!-- Nombre y email del cliente -->
                            <td><?php echo h($r["cancha_nombre"]); ?></td>
                            <!-- Cancha reservada -->
                            <td><?php echo h(date("d/m/Y", strtotime($r["fecha"]))); ?></td>
                            <!-- Fecha dia/mes/año -->
                            <td><?php echo h(substr($r["hora_inicio"], 0, 5) . " a " . substr($r["hora_fin"], 0, 5)); ?></td>
                            <!-- Horario -->
                            <td>$<?php echo number_format($r["monto"], 0, ",", "."); ?></td>
                            <!-- Monto -->
                            <td><?php echo h(($r["pago_codigo"] ?? "-") . ($r["pago_estado"] ? " (" . $r["pago_estado"] . ")" : "")); ?></td>
                            <!-- Codigo del comprobante y estado del pago -->
                            <td><span class="etiqueta etiqueta-<?php echo h($r["estado"]); ?>"><?php echo h(ucfirst($r["estado"])); ?></span></td>
                            <!-- Estado con color -->
                            <td class="acciones">
                                <!-- Botones de accion -->
                                <a class="boton-chico" href="admin_reserva_form.php?id=<?php echo (int)$r["id"]; ?>">Editar</a>
                                <!-- Enlace a la modificacion -->
                                <?php if ($r["estado"] === "confirmada") { ?>
                                    <!-- Solo se puede cancelar si esta confirmada -->
                                    <form action="admin_reservas.php" method="post" onsubmit="return confirm('¿Cancelar esta reserva?');">
                                        <!-- Formulario con confirmacion -->
                                        <input type="hidden" name="accion" value="cancelar">
                                        <!-- Accion: cancelar -->
                                        <input type="hidden" name="reserva_id" value="<?php echo (int)$r["id"]; ?>">
                                        <!-- Id de la reserva -->
                                        <button class="boton-chico" type="submit">Cancelar</button>
                                        <!-- Boton cancelar -->
                                    </form>
                                <?php } ?>
                                <form action="admin_reservas.php" method="post" onsubmit="return confirm('¿Eliminar definitivamente esta reserva? No se puede deshacer.');">
                                    <!-- Formulario de baja definitiva con confirmacion -->
                                    <input type="hidden" name="accion" value="eliminar">
                                    <!-- Accion: eliminar -->
                                    <input type="hidden" name="reserva_id" value="<?php echo (int)$r["id"]; ?>">
                                    <!-- Id de la reserva -->
                                    <button class="boton-chico boton-peligro" type="submit">Eliminar</button>
                                    <!-- Boton eliminar -->
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>
<?php require __DIR__ . "/../includes/footer.php"; ?>
