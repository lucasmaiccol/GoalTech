<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = "Administrar reservas";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Reservas</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="<?php echo h(url('admin_reserva_form')); ?>">+ Nueva reserva</a>
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
                            <td><?php echo (int)$r->id; ?></td>
                            <!-- Numero de reserva -->
                            <td><?php echo h($r->usuarioNombre); ?><br><small><?php echo h($r->usuarioEmail); ?></small></td>
                            <!-- Nombre y email del cliente -->
                            <td><?php echo h($r->canchaNombre); ?></td>
                            <!-- Cancha reservada -->
                            <td><?php echo h(date("d/m/Y", strtotime($r->fecha))); ?></td>
                            <!-- Fecha dia/mes/año -->
                            <td><?php echo h(substr($r->horaInicio, 0, 5) . " a " . substr($r->horaFin, 0, 5)); ?></td>
                            <!-- Horario -->
                            <td>$<?php echo dinero($r->monto); ?></td>
                            <!-- Monto -->
                            <td><?php echo h(($r->pagoCodigo ?? "-") . ($r->pagoEstado ? " (" . $r->pagoEstado . ")" : "")); ?></td>
                            <!-- Codigo del comprobante y estado del pago -->
                            <td><span class="etiqueta etiqueta-<?php echo h($r->estado); ?>"><?php echo h(ucfirst($r->estado)); ?></span></td>
                            <!-- Estado con color -->
                            <td class="acciones">
                                <!-- Botones de accion -->
                                <a class="boton-chico" href="<?php echo h(url('admin_reserva_form', ['id' => $r->id])); ?>">Editar</a>
                                <!-- Enlace a la modificacion -->
                                <?php if ($r->estaConfirmada()) { ?>
                                    <!-- Solo se puede cancelar si esta confirmada -->
                                    <form action="<?php echo h(url('admin_reserva_cancelar')); ?>" method="post" onsubmit="return confirm(\'¿Cancelar esta reserva?');">
                                        <!-- Formulario con confirmacion -->
                                        <?php echo campoCsrf(); ?>
                                        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                                        <input type="hidden" name="reserva_id" value="<?php echo (int)$r->id; ?>">
                                        <!-- Id de la reserva -->
                                        <button class="boton-chico" type="submit">Cancelar</button>
                                        <!-- Boton cancelar -->
                                    </form>
                                <?php } ?>
                                <form action="<?php echo h(url('admin_reserva_eliminar')); ?>" method="post" onsubmit="return confirm(\'¿Eliminar definitivamente esta reserva? No se puede deshacer.');">
                                    <!-- Formulario de baja definitiva con confirmacion -->
                                    <?php echo campoCsrf(); ?>
                                    <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                                    <input type="hidden" name="reserva_id" value="<?php echo (int)$r->id; ?>">
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
<?php require __DIR__ . "/../layout/pie.php"; ?>
