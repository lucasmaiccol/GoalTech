<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/ReservaController.php";                         // Carga el controlador de reservas.
require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo de canchas.
require_once __DIR__ . "/../models/Reserva.php";                                        // Carga el modelo de reservas.

requerirCliente();                                                                      // Solo entran clientes con sesion (el admin va a su panel).
unset($_SESSION["reserva_pendiente"]);                                                  // Si habia un pago a medias, lo descarta al volver aca.

$reservaCtrl = new ReservaController($conexion);                                        // Crea el controlador de reservas.
$canchaModelo = new Cancha($conexion);                                                  // Crea el modelo de canchas.
$reservaModelo = new Reserva($conexion);                                                // Crea el modelo de reservas.

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "cancelar") { // Si el cliente apreto "Cancelar" en una reserva...
    $resultado = $reservaCtrl->cancelarPropia((int)($_POST["reserva_id"] ?? 0), $_SESSION["usuario_id"]); // ...pide cancelarla (el controlador verifica que sea suya).
    setMensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]);         // Guarda el mensaje para mostrarlo en la proxima carga.
    header("Location: dashboard.php");                                                  // Recarga la pagina (evita reenviar el formulario al refrescar).
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$canchas = $canchaModelo->listarActivas();                                              // Trae las canchas que se pueden reservar.
$horas = $reservaCtrl->horasDisponibles();                                              // Trae la lista de horarios disponibles.
$reservas = $reservaModelo->listarPorUsuario($_SESSION["usuario_id"]);                  // Trae las reservas de este usuario.
$ahora = date("Y-m-d H:i:s");                                                           // Fecha y hora actual (para saber si una reserva ya paso).

$tituloPagina = "Mis reservas";                                                         // Titulo que usa el header.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba (menu y mensajes).
?>
<section class="hero-panel">
    <!-- Banner de bienvenida con imagen de fondo -->
    <h1>Hola, <?php echo h($_SESSION["usuario_nombre"]); ?></h1>
    <!-- Saludo con el nombre del usuario -->
    <p>Elegí tu cancha, el día y el horario. Pagás y queda reservada.</p>
    <!-- Texto de ayuda -->
</section>

<h2 class="panel-subtitulo">Canchas disponibles</h2>
<!-- Titulo de la primera seccion -->

<?php if (count($canchas) === 0) { ?>
    <!-- Si no hay canchas activas, avisa -->
    <section class="tarjeta"><p>Por el momento no hay canchas disponibles.</p></section>
    <!-- Mensaje informativo -->
<?php } else { ?>
    <!-- Si hay canchas, las muestra como tarjetas -->
    <div class="grilla-canchas">
        <!-- Grilla que acomoda las tarjetas segun el ancho de la pantalla -->
        <?php foreach ($canchas as $c) { ?>
            <!-- Recorre cada cancha activa -->
            <article class="cancha-card">
                <!-- Tarjeta de una cancha -->
                <?php echo htmlImagenCancha($c, "cancha-foto", "lazy"); ?>
                <!-- Foto de la cancha -->
                <div class="cancha-info">
                    <!-- Zona de texto de la tarjeta -->
                    <div class="cancha-titulo">
                        <!-- Nombre y precio en la misma fila -->
                        <h3><?php echo h($c["nombre"]); ?></h3>
                        <!-- Nombre de la cancha -->
                        <span class="cancha-precio">$<?php echo number_format($c["precio_hora"], 0, ",", "."); ?><small>/hora</small></span>
                        <!-- Precio por hora -->
                    </div>
                    <span class="etiqueta etiqueta-tipo"><?php echo h($c["tipo"]); ?></span>
                    <!-- Tipo de cancha (Futbol 5, 7 u 11) -->
                    <p class="cancha-desc"><?php echo h($c["descripcion"] ?? ""); ?></p>
                    <!-- Descripcion corta de la cancha -->
                    <form class="cancha-form" action="pago.php" method="post">
                        <!-- Formulario para reservar esta cancha; al enviarlo va a pago.php -->
                        <input type="hidden" name="paso" value="resumen">
                        <!-- Indica a pago.php que es el primer paso -->
                        <input type="hidden" name="cancha_id" value="<?php echo (int)$c["id"]; ?>">
                        <!-- Id de la cancha de esta tarjeta -->
                        <input type="date" name="dia_reserva" autocomplete="off" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>" required>
                        <!-- Fecha; min impide elegir dias pasados -->
                        <select name="turno_reserva" autocomplete="off" required>
                            <!-- Lista desplegable de horarios -->
                            <?php foreach ($horas as $hora) { ?>
                                <!-- Recorre cada horario posible -->
                                <option value="<?php echo h($hora); ?>"><?php echo h($hora); ?></option>
                                <!-- Una opcion por horario -->
                            <?php } ?>
                        </select>
                        <button class="boton-panel" type="submit">Reservar</button>
                        <!-- Boton que continua al pago -->
                    </form>
                </div>
            </article>
        <?php } ?>
    </div>
<?php } ?>

<h2 class="panel-subtitulo">Mis reservas</h2>
<!-- Titulo de la segunda seccion -->

<section class="tarjeta">
    <!-- Caja blanca con la tabla de reservas -->
    <?php if (count($reservas) === 0) { ?>
        <!-- Si el usuario no tiene reservas, avisa -->
        <p>Todavia no tenes reservas. ¡Hace la primera arriba!</p>
        <!-- Mensaje informativo -->
    <?php } else { ?>
        <!-- Si tiene reservas, las lista -->
        <div class="tabla-scroll">
            <!-- Permite deslizar la tabla en celulares -->
            <table class="tabla">
                <!-- Tabla de reservas -->
                <thead>
                    <!-- Encabezado de la tabla -->
                    <tr><th>Cancha</th><th>Fecha</th><th>Horario</th><th>Monto</th><th>Comprobante</th><th>Estado</th><th></th></tr>
                    <!-- Nombres de las columnas -->
                </thead>
                <tbody>
                    <!-- Cuerpo de la tabla -->
                    <?php foreach ($reservas as $r) { ?>
                        <!-- Recorre cada reserva -->
                        <tr>
                            <!-- Una fila por reserva -->
                            <td><?php echo h($r["cancha_nombre"] . " (" . $r["cancha_tipo"] . ")"); ?></td>
                            <!-- Cancha y su tipo -->
                            <td><?php echo h(date("d/m/Y", strtotime($r["fecha"]))); ?></td>
                            <!-- Fecha en formato dia/mes/año -->
                            <td><?php echo h(substr($r["hora_inicio"], 0, 5) . " a " . substr($r["hora_fin"], 0, 5)); ?></td>
                            <!-- Horario de inicio y fin -->
                            <td>$<?php echo number_format($r["monto"], 0, ",", "."); ?></td>
                            <!-- Monto pagado -->
                            <td><?php echo h($r["pago_codigo"] ?? "-"); ?></td>
                            <!-- Codigo del comprobante de pago -->
                            <td><span class="etiqueta etiqueta-<?php echo h($r["estado"]); ?>"><?php echo h(ucfirst($r["estado"])); ?></span></td>
                            <!-- Estado con color: confirmada (verde) o cancelada (rojo) -->
                            <td>
                                <!-- Celda de acciones -->
                                <?php if ($r["estado"] === "confirmada" && $r["fecha"] . " " . $r["hora_inicio"] > $ahora) { ?>
                                    <!-- Solo se puede cancelar si esta confirmada y todavia no empezo -->
                                    <form action="dashboard.php" method="post" onsubmit="return confirm('¿Cancelar esta reserva?');">
                                        <!-- Formulario con confirmacion del navegador -->
                                        <input type="hidden" name="accion" value="cancelar">
                                        <!-- Indica la accion a realizar -->
                                        <input type="hidden" name="reserva_id" value="<?php echo (int)$r["id"]; ?>">
                                        <!-- Indica que reserva cancelar -->
                                        <button class="boton-chico boton-peligro" type="submit">Cancelar</button>
                                        <!-- Boton de cancelar -->
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>
<?php require __DIR__ . "/../includes/footer.php"; ?>
