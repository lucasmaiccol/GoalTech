<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = "Pago";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<h1 class="panel-titulo">Confirmar y pagar</h1>
<!-- Titulo de la pagina -->

<?php if ($errorPago !== "") { ?>
    <!-- Si la tarjeta tuvo un error, lo muestra -->
    <p class="error"><?php echo h($errorPago); ?></p>
    <!-- Cuadro rojo con el error -->
<?php } ?>

<div class="dos-columnas">
    <!-- Dos cajas lado a lado: resumen y tarjeta -->
    <section class="tarjeta">
        <!-- Caja del resumen de la reserva -->
        <?php echo htmlImagenCancha($cancha, "foto-resumen"); ?>
        <!-- Foto de la cancha elegida -->
        <h2 class="panel-subtitulo">Resumen</h2>
        <!-- Titulo de la caja -->
        <dl class="resumen">
            <!-- Lista de datos (termino y valor) -->
            <dt>Cancha</dt><dd><?php echo h($cancha->nombre . " (" . $cancha->tipo . ")"); ?></dd>
            <!-- Nombre y tipo de cancha -->
            <dt>Fecha</dt><dd><?php echo h(date("d/m/Y", strtotime($pendiente["fecha"]))); ?></dd>
            <!-- Fecha en formato dia/mes/año -->
            <dt>Horario</dt><dd><?php echo h($pendiente["hora"] . " a " . $horaFin); ?></dd>
            <!-- Hora de inicio y de fin (1 hora despues) -->
            <dt>Total</dt><dd class="total">$<?php echo dinero($cancha->precioHora); ?></dd>
            <!-- Precio a pagar -->
        </dl>
        <a class="volver-inicio" href="<?php echo h(url('dashboard')); ?>">← Elegir otro horario</a>
        <!-- Enlace para volver sin pagar -->
    </section>

    <section class="tarjeta">
        <!-- Caja del formulario de tarjeta -->
        <h2 class="panel-subtitulo">Pago con tarjeta</h2>
        <!-- Titulo de la caja -->
        <p class="aviso">Pago <strong>simulado</strong>: no se cobra nada ni se guarda el numero de tarjeta. Para probar usa 4242 4242 4242 4242, un vencimiento futuro y cualquier CVV de 3 digitos.</p>
        <!-- Aviso de que el pago es ficticio -->
        <form class="formulario-panel" action="<?php echo h(url('pago')); ?>" method="post" autocomplete="off">
            <!-- Formulario de la tarjeta; autocomplete off evita que el navegador sugiera datos -->
            <?php echo campoCsrf(); ?>
            <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
            <div class="campo">
                <!-- Grupo del titular -->
                <label for="pg_titular">Titular de la tarjeta</label>
                <!-- Etiqueta -->
                <input type="text" id="pg_titular" name="pg_titular" maxlength="60" required autocomplete="off" placeholder="Como figura en la tarjeta">
                <!-- Campo de texto del titular -->
            </div>
            <div class="campo">
                <!-- Grupo del numero -->
                <label for="pg_numero">Numero de tarjeta</label>
                <!-- Etiqueta -->
                <input type="text" id="pg_numero" name="pg_numero" required autocomplete="off" inputmode="numeric" maxlength="19" placeholder="4242 4242 4242 4242">
                <!-- Campo del numero (maximo 19 caracteres con espacios) -->
            </div>
            <div class="fila">
                <!-- Fila para poner vencimiento y CVV lado a lado -->
                <div class="campo">
                    <!-- Grupo del vencimiento -->
                    <label for="pg_venc">Vencimiento</label>
                    <!-- Etiqueta -->
                    <input type="text" id="pg_venc" name="pg_venc" required autocomplete="off" maxlength="5" placeholder="MM/AA">
                    <!-- Campo del vencimiento con formato MM/AA -->
                </div>
                <div class="campo">
                    <!-- Grupo del CVV -->
                    <label for="pg_codigo">CVV</label>
                    <!-- Etiqueta -->
                    <input class="campo-oculto" type="text" id="pg_codigo" name="pg_codigo" required autocomplete="off" inputmode="numeric" maxlength="3" placeholder="123">
                    <!-- Campo del CVV: es de texto pero se muestra con puntos (clase campo-oculto) para que el navegador no lo trate como contrasena -->
                </div>
            </div>
            <button class="boton-panel" type="submit">Pagar $<?php echo dinero($cancha->precioHora); ?> y reservar</button>
            <!-- Boton que envia el pago -->
        </form>
    </section>
</div>
<?php require __DIR__ . "/../layout/pie.php"; ?>
