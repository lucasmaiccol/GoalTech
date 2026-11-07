<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = "Administrar canchas";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Canchas</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="<?php echo h(url('admin_cancha_form')); ?>">+ Nueva cancha</a>
    <!-- Enlace al formulario de alta -->
</div>

<section class="tarjeta">
    <!-- Caja blanca con la tabla -->
    <?php if (count($canchas) === 0) { ?>
        <!-- Si no hay canchas, avisa -->
        <p>No hay canchas cargadas.</p>
        <!-- Mensaje informativo -->
    <?php } else { ?>
        <!-- Si hay canchas, las lista -->
        <div class="tabla-scroll">
            <!-- Permite deslizar la tabla en celulares -->
            <table class="tabla">
                <!-- Tabla de canchas -->
                <thead>
                    <!-- Encabezado -->
                    <tr><th>#</th><th>Foto</th><th>Nombre</th><th>Tipo</th><th>Precio por hora</th><th>Estado</th><th>Acciones</th></tr>
                    <!-- Nombres de las columnas -->
                </thead>
                <tbody>
                    <!-- Cuerpo -->
                    <?php foreach ($canchas as $c) { ?>
                        <!-- Recorre cada cancha -->
                        <tr>
                            <!-- Una fila por cancha -->
                            <td><?php echo (int)$c->id; ?></td>
                            <!-- Numero de cancha -->
                            <td><?php echo htmlImagenCancha($c, "miniatura", "lazy"); ?></td>
                            <!-- Miniatura de la foto de la cancha -->
                            <td><?php echo h($c->nombre); ?></td>
                            <!-- Nombre -->
                            <td><?php echo h($c->tipo); ?></td>
                            <!-- Tipo -->
                            <td>$<?php echo dinero($c->precioHora); ?></td>
                            <!-- Precio -->
                            <td><span class="etiqueta etiqueta-<?php echo $c->estado === "activa" ? "confirmada" : "cancelada"; ?>"><?php echo h(ucfirst($c->estado)); ?></span></td>
                            <!-- Estado con color: activa (verde) o inactiva (rojo) -->
                            <td class="acciones">
                                <!-- Botones de accion -->
                                <a class="boton-chico" href="<?php echo h(url('admin_cancha_form', ['id' => $c->id])); ?>">Editar</a>
                                <!-- Enlace a la modificacion -->
                                <form action="<?php echo h(url('admin_cancha_eliminar')); ?>" method="post" onsubmit="return confirm('¿Eliminar esta cancha?');">
                                    <!-- Formulario de baja con confirmacion -->
                                    <?php echo campoCsrf(); ?>
                                    <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                                    <input type="hidden" name="cancha_id" value="<?php echo (int)$c->id; ?>">
                                    <!-- Id de la cancha -->
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
