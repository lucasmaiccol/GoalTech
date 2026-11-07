<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = $editado ? "Editar usuario" : "Nuevo usuario";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<h1 class="panel-titulo"><?php echo $editado ? "Editar usuario" : "Nuevo usuario"; ?></h1>
<!-- Titulo segun alta o modificacion -->

<?php if ($error !== "") { ?>
    <!-- Si hubo error, lo muestra -->
    <p class="error"><?php echo h($error); ?></p>
    <!-- Cuadro rojo con el error -->
<?php } ?>

<section class="tarjeta tarjeta-angosta">
    <!-- Caja blanca con el formulario -->
    <form class="formulario-panel" action="<?php echo h(url('admin_usuario_form')); ?>" method="post">
        <!-- Formulario que se envia a esta misma pagina -->
        <?php echo campoCsrf(); ?>
        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->
        <div class="campo">
            <!-- Grupo del nombre -->
            <label for="nombre">Nombre</label>
            <!-- Etiqueta -->
            <input type="text" id="nombre" name="nombre" maxlength="50" value="<?php echo h($vNombre); ?>" required>
            <!-- Campo de texto del nombre -->
        </div>
        <div class="campo">
            <!-- Grupo del email -->
            <label for="email">Correo electronico</label>
            <!-- Etiqueta -->
            <input type="email" id="email" name="email" maxlength="100" value="<?php echo h($vEmail); ?>" <?php echo $editado ? "disabled" : "required"; ?>>
            <!-- Al editar el email no se puede cambiar (disabled); al crear es obligatorio -->
        </div>
        <div class="campo">
            <!-- Grupo del telefono -->
            <label for="telefono">Telefono</label>
            <!-- Etiqueta -->
            <input type="text" id="telefono" name="telefono" maxlength="20" minlength="6" pattern="[0-9+\-\s()]{6,20}" title="Solo numeros, espacios, + - ( ). Entre 6 y 20 caracteres." value="<?php echo h($vTelefono); ?>" required>
            <!-- Campo de texto del telefono -->
        </div>
        <div class="campo">
            <!-- Grupo del rol -->
            <label for="rol">Rol</label>
            <!-- Etiqueta -->
            <select id="rol" name="rol">
                <!-- Lista desplegable de roles -->
                <option value="cliente" <?php echo $vRol === "cliente" ? "selected" : ""; ?>>Cliente</option>
                <!-- Puede reservar canchas -->
                <option value="admin" <?php echo $vRol === "admin" ? "selected" : ""; ?>>Admin</option>
                <!-- Puede administrar todo el sistema -->
            </select>
        </div>
        <div class="campo">
            <!-- Grupo de la contraseña -->
            <label for="password"><?php echo $editado ? "Contraseña nueva (dejar vacio para no cambiarla)" : "Contraseña"; ?></label>
            <!-- Etiqueta que cambia segun el modo -->
            <input type="password" id="password" name="password" maxlength="72" <?php echo $editado ? "" : "required"; ?> minlength="8" placeholder="Minimo 8 caracteres">
            <!-- Obligatoria solo al crear -->
        </div>
        <div class="fila">
            <!-- Fila con los botones -->
            <button class="boton-panel" type="submit"><?php echo $editado ? "Guardar cambios" : "Crear usuario"; ?></button>
            <!-- Boton de guardar -->
            <a class="boton-chico" href="<?php echo h(url('admin_usuarios')); ?>">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../layout/pie.php"; ?>
