<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = "Mis reservas";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<section class="hero-panel">
    <!-- Banner de bienvenida con imagen de fondo -->
    <h1>Hola, <?php echo h($usuarioActual->nombre); ?></h1>
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
                        <h3><?php echo h($c->nombre); ?></h3>
                        <!-- Nombre de la cancha -->
                        <span class="cancha-precio">$<?php echo dinero($c->precioHora); ?><small>/hora</small></span>
                        <!-- Precio por hora -->
                    </div>
                    <span class="etiqueta etiqueta-tipo"><?php echo h($c->tipo); ?></span>
                    <!-- Tipo de cancha (Futbol 5, 7 u 11) -->
                    <p class="cancha-desc"><?php echo h($c->descripcion); ?></p>
                    <!-- Descripcion corta de la cancha -->
                    <form class="cancha-form" action="<?php echo h(url('pago_iniciar')); ?>" method="post">
                        <!-- Formulario para reservar esta cancha; al enviarlo va a pago.php -->
                        <?php echo campoCsrf(); ?>
                        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                        <input type="hidden" name="cancha_id" value="<?php echo (int)$c->id; ?>">
                        <!-- Id de la cancha de esta tarjeta -->
                        <input type="date" name="dia_reserva" autocomplete="off" min="<?php echo h($hoy); ?>" value="<?php echo h($hoy); ?>" required>
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
                            <td><?php echo h($r->canchaNombre . " (" . $r->canchaTipo . ")"); ?></td>
                            <!-- Cancha y su tipo -->
                            <td><?php echo h(date("d/m/Y", strtotime($r->fecha))); ?></td>
                            <!-- Fecha en formato dia/mes/año -->
                            <td><?php echo h(substr($r->horaInicio, 0, 5) . " a " . substr($r->horaFin, 0, 5)); ?></td>
                            <!-- Horario de inicio y fin -->
                            <td>$<?php echo dinero($r->monto); ?></td>
                            <!-- Monto pagado -->
                            <td><?php echo h($r->pagoCodigo ?? "-"); ?></td>
                            <!-- Codigo del comprobante de pago -->
                            <td><span class="etiqueta etiqueta-<?php echo h($r->estado); ?>"><?php echo h(ucfirst($r->estado)); ?></span></td>
                            <!-- Estado con color: confirmada (verde) o cancelada (rojo) -->
                            <td>
                                <!-- Celda de acciones -->
                                <?php if ($r->estaConfirmada() && $r->inicio() > $ahora) { ?>
                                    <!-- Solo se puede cancelar si esta confirmada y todavia no empezo -->
                                    <form action="<?php echo h(url('cancelar_reserva')); ?>" method="post" onsubmit="return confirm('¿Cancelar esta reserva?');">
                                        <!-- Formulario con confirmacion del navegador -->
                                        <?php echo campoCsrf(); ?>
                                        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                                        <input type="hidden" name="reserva_id" value="<?php echo (int)$r->id; ?>">
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
<?php require __DIR__ . "/../layout/pie.php"; ?>
