<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = $reserva ? "Editar reserva" : "Nueva reserva";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
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
    <form class="formulario-panel" action="<?php echo h(url('admin_reserva_form')); ?>" method="post">
        <!-- Formulario que se envia a esta misma pagina -->
        <?php echo campoCsrf(); ?>
        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->

        <?php if ($reserva) { ?>
            <!-- Al editar, el cliente no se puede cambiar: solo se muestra -->
            <div class="campo">
                <!-- Grupo del cliente -->
                <label>Cliente</label>
                <!-- Etiqueta -->
                <input type="text" value="<?php echo h($reserva->usuarioNombre . " (" . $reserva->usuarioEmail . ")"); ?>" disabled>
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
                        <option value="<?php echo (int)$u->id; ?>" <?php echo (string)$vUsuario === (string)$u->id ? "selected" : ""; ?>><?php echo h($u->nombre . " (" . $u->email . ")"); ?></option>
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
                    <option value="<?php echo (int)$c->id; ?>" <?php echo (string)$vCancha === (string)$c->id ? "selected" : ""; ?>><?php echo h($c->nombre . " - " . $c->tipo . ($c->estado === "inactiva" ? " (inactiva)" : "")); ?></option>
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
            <a class="boton-chico" href="<?php echo h(url('admin_reservas')); ?>">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../layout/pie.php"; ?>
