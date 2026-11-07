<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = "Administrar usuarios";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Usuarios</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="<?php echo h(url('admin_usuario_form')); ?>">+ Nuevo usuario</a>
    <!-- Enlace al formulario de alta -->
</div>

<section class="tarjeta">
    <!-- Caja blanca con la tabla -->
    <div class="tabla-scroll">
        <!-- Permite deslizar la tabla en celulares -->
        <table class="tabla">
            <!-- Tabla de usuarios -->
            <thead>
                <!-- Encabezado -->
                <tr><th>#</th><th>Nombre</th><th>Email</th><th>Telefono</th><th>Rol</th><th>Acciones</th></tr>
                <!-- Nombres de las columnas -->
            </thead>
            <tbody>
                <!-- Cuerpo -->
                <?php foreach ($usuarios as $u) { ?>
                    <!-- Recorre cada usuario -->
                    <tr>
                        <!-- Una fila por usuario -->
                        <td><?php echo (int)$u->id; ?></td>
                        <!-- Numero de usuario -->
                        <td><?php echo h($u->nombre); ?></td>
                        <!-- Nombre -->
                        <td><?php echo h($u->email); ?></td>
                        <!-- Email -->
                        <td><?php echo h($u->telefono); ?></td>
                        <!-- Telefono -->
                        <td><span class="etiqueta etiqueta-<?php echo $u->rol === "admin" ? "admin" : "confirmada"; ?>"><?php echo h(ucfirst($u->rol)); ?></span></td>
                        <!-- Rol con color -->
                        <td class="acciones">
                            <!-- Botones de accion -->
                            <a class="boton-chico" href="<?php echo h(url('admin_usuario_form', ['id' => $u->id])); ?>">Editar</a>
                            <!-- Enlace a la modificacion -->
                            <?php if ((int)$u->id !== (int)$usuarioActual->id) { ?>
                                <!-- El admin no ve el boton Eliminar en su propia fila -->
                                <form action="<?php echo h(url('admin_usuario_eliminar')); ?>" method="post" onsubmit="return confirm('¿Eliminar este usuario?');">
                                    <!-- Formulario de baja con confirmacion -->
                                    <?php echo campoCsrf(); ?>
                                    <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
                                    <input type="hidden" name="usuario_id" value="<?php echo (int)$u->id; ?>">
                                    <!-- Id del usuario -->
                                    <button class="boton-chico boton-peligro" type="submit">Eliminar</button>
                                    <!-- Boton eliminar -->
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . "/../layout/pie.php"; ?>
