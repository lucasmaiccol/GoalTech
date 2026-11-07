<?php                                                                                   // Abre el bloque de codigo PHP.
$tituloPagina = $cancha ? "Editar cancha" : "Nueva cancha";                                                  // Titulo que usa la cabecera.
require __DIR__ . "/../layout/cabecera.php";                                   // Dibuja la parte de arriba (menu y mensajes).
?>
<h1 class="panel-titulo"><?php echo $cancha ? "Editar cancha" : "Nueva cancha"; ?></h1>
<!-- Titulo segun alta o modificacion -->

<?php if ($error !== "") { ?>
    <!-- Si hubo error, lo muestra -->
    <p class="error"><?php echo h($error); ?></p>
    <!-- Cuadro rojo con el error -->
<?php } ?>

<section class="tarjeta tarjeta-angosta">
    <!-- Caja blanca con el formulario -->
    <form class="formulario-panel" action="<?php echo h(url('admin_cancha_form')); ?>" method="post" enctype="multipart/form-data">
        <!-- Formulario que se envia a esta misma pagina; multipart/form-data es necesario para poder subir archivos -->
        <?php echo campoCsrf(); ?>
        <!-- Token CSRF: protege el formulario contra envios desde otras paginas -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->
        <div class="campo">
            <!-- Grupo del nombre -->
            <label for="nombre">Nombre</label>
            <!-- Etiqueta -->
            <input type="text" id="nombre" name="nombre" maxlength="50" value="<?php echo h($vNombre); ?>" required placeholder="Cancha 1">
            <!-- Campo de texto del nombre -->
        </div>
        <div class="campo">
            <!-- Grupo del tipo -->
            <label for="tipo">Tipo</label>
            <!-- Etiqueta -->
            <input type="text" id="tipo" name="tipo" maxlength="30" list="tipos" value="<?php echo h($vTipo); ?>" required placeholder="Futbol 5">
            <!-- Campo de texto con sugerencias (datalist) -->
            <datalist id="tipos">
                <!-- Lista de sugerencias para el tipo -->
                <option value="Futbol 5"><option value="Futbol 7"><option value="Futbol 11">
                <!-- Las tres opciones mas comunes -->
            </datalist>
        </div>
        <div class="campo">
            <!-- Grupo del precio -->
            <label for="precio_hora">Precio por hora ($, entre 1 y 100.000)</label>
            <!-- Etiqueta -->
            <input type="number" id="precio_hora" name="precio_hora" value="<?php echo h($vPrecio); ?>" required min="1" max="100000" step="0.01" inputmode="decimal" placeholder="800">" required min="1" step="0.01" placeholder="800">
            <!-- Campo numerico del precio -->
        </div>
        <div class="campo">
            <!-- Grupo de la descripcion -->
            <label for="descripcion">Descripcion (opcional, maximo 150 caracteres)</label>
            <!-- Etiqueta -->
            <input type="text" id="descripcion" name="descripcion" maxlength="150" value="<?php echo h($vDescripcion); ?>" placeholder="Techada, con iluminacion LED...">
            <!-- Texto corto que se ve en la tarjeta de la cancha -->
        </div>
        <?php if ($cancha) { ?>
            <!-- Al editar, muestra la foto actual -->
            <div class="campo">
                <!-- Grupo de la foto actual -->
                <label>Foto actual</label>
                <!-- Etiqueta -->
                <?php echo htmlImagenCancha($cancha, "vista-previa"); ?>
                <!-- Muestra la foto que tiene hoy la cancha -->
            </div>
        <?php } ?>
        <div class="campo">
            <!-- Grupo de la imagen de ejemplo -->
            <label for="imagen_predefinida">Imagen de ejemplo</label>
            <!-- Etiqueta -->
            <select id="imagen_predefinida" name="imagen_predefinida">
                <!-- Lista desplegable de imagenes predefinidas -->
                <option value=""><?php echo $cancha ? "Mantener la actual" : "Imagen por defecto"; ?></option>
                <!-- Primera opcion: no cambiar nada -->
                <?php foreach ($imagenes as $ruta => $nombreImg) { ?>
                    <!-- Recorre cada imagen predefinida -->
                    <option value="<?php echo h($ruta); ?>"><?php echo h($nombreImg); ?></option>
                    <!-- Una opcion por imagen -->
                <?php } ?>
            </select>
        </div>
        <div class="campo">
            <!-- Grupo de la foto propia -->
            <label for="imagen">...o subir una foto propia (JPG, PNG o WEBP, maximo 2 MB)</label>
            <!-- Etiqueta -->
            <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
            <!-- Selector de archivo; si se sube una foto, tiene prioridad sobre la imagen de ejemplo -->
        </div>
        <div class="campo">
            <!-- Grupo del estado -->
            <label for="estado">Estado</label>
            <!-- Etiqueta -->
            <select id="estado" name="estado">
                <!-- Lista desplegable de estados -->
                <option value="activa" <?php echo $vEstado === "activa" ? "selected" : ""; ?>>Activa</option>
                <!-- Se puede reservar -->
                <option value="inactiva" <?php echo $vEstado === "inactiva" ? "selected" : ""; ?>>Inactiva</option>
                <!-- No se puede reservar -->
            </select>
        </div>
        <div class="fila">
            <!-- Fila con los botones -->
            <button class="boton-panel" type="submit"><?php echo $cancha ? "Guardar cambios" : "Crear cancha"; ?></button>
            <!-- Boton de guardar -->
            <a class="boton-chico" href="<?php echo h(url('admin_canchas')); ?>">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../layout/pie.php"; ?>
