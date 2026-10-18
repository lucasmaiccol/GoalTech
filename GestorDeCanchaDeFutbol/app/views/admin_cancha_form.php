<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AdminController.php";                           // Carga el controlador de administracion.
require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo de canchas.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$adminCtrl = new AdminController($conexion);                                            // Crea el controlador de administracion.
$canchaModelo = new Cancha($conexion);                                                  // Crea el modelo de canchas.

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);                                          // Si viene un id, es MODIFICACION; si no, es ALTA.
$cancha = $id > 0 ? $canchaModelo->buscarPorId($id) : null;                             // Busca la cancha si estamos editando.
if ($id > 0 && !$cancha) {                                                              // Si pidio editar una cancha que no existe...
    setMensaje("error", "Cancha no encontrada");                                        // ...guarda el error.
    header("Location: admin_canchas.php");                                              // ...y vuelve a la lista.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$error = "";                                                                            // Aca se guarda el error del formulario.

if ($_SERVER["REQUEST_METHOD"] === "POST") {                                            // Si el admin envio el formulario...
    $nombre = trim($_POST["nombre"] ?? "");                                             // Lee el nombre.
    $tipo = trim($_POST["tipo"] ?? "");                                                 // Lee el tipo.
    $precio = trim($_POST["precio_hora"] ?? "");                                        // Lee el precio.
    $estado = $_POST["estado"] ?? "activa";                                             // Lee el estado.
    $descripcion = trim($_POST["descripcion"] ?? "");                                   // Lee la descripcion.
    $archivo = $_FILES["imagen"] ?? null;                                               // Lee la foto subida (si eligio una).
    $predefinida = $_POST["imagen_predefinida"] ?? "";                                  // Lee la imagen de ejemplo elegida (si eligio una).
    if ($cancha) {                                                                      // Si estamos modificando...
        $resultado = $adminCtrl->actualizarCancha($id, $nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida); // ...pide actualizar.
    } else {                                                                            // Si estamos dando de alta...
        $resultado = $adminCtrl->crearCancha($nombre, $tipo, $precio, $estado, $descripcion, $archivo, $predefinida); // ...pide crear.
    }                                                                                   // Cierra el if/else.
    if ($resultado["exito"]) {                                                          // Si salio bien...
        setMensaje("exito", $resultado["mensaje"]);                                     // ...guarda el mensaje de exito.
        header("Location: admin_canchas.php");                                          // ...y vuelve a la lista.
        exit;                                                                           // Corta la ejecucion.
    }                                                                                   // Cierra el if.
    $error = $resultado["mensaje"];                                                     // Si salio mal, guarda el error para mostrarlo.
}                                                                                       // Cierra el if del POST.

$vNombre = $_POST["nombre"] ?? ($cancha["nombre"] ?? "");                               // Valor a mostrar en nombre.
$vTipo = $_POST["tipo"] ?? ($cancha["tipo"] ?? "");                                     // Valor a mostrar en tipo.
$vPrecio = $_POST["precio_hora"] ?? ($cancha["precio_hora"] ?? "");                     // Valor a mostrar en precio.
$vEstado = $_POST["estado"] ?? ($cancha["estado"] ?? "activa");                         // Valor a mostrar en estado.
$vDescripcion = $_POST["descripcion"] ?? ($cancha["descripcion"] ?? "");                // Valor a mostrar en descripcion.

$tituloPagina = $cancha ? "Editar cancha" : "Nueva cancha";                             // Titulo segun el modo.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
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
    <form class="formulario-panel" action="admin_cancha_form.php" method="post" enctype="multipart/form-data">
        <!-- Formulario que se envia a esta misma pagina; multipart/form-data es necesario para poder subir archivos -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->
        <div class="campo">
            <!-- Grupo del nombre -->
            <label for="nombre">Nombre</label>
            <!-- Etiqueta -->
            <input type="text" id="nombre" name="nombre" value="<?php echo h($vNombre); ?>" required placeholder="Cancha 1">
            <!-- Campo de texto del nombre -->
        </div>
        <div class="campo">
            <!-- Grupo del tipo -->
            <label for="tipo">Tipo</label>
            <!-- Etiqueta -->
            <input type="text" id="tipo" name="tipo" list="tipos" value="<?php echo h($vTipo); ?>" required placeholder="Futbol 5">
            <!-- Campo de texto con sugerencias (datalist) -->
            <datalist id="tipos">
                <!-- Lista de sugerencias para el tipo -->
                <option value="Futbol 5"><option value="Futbol 7"><option value="Futbol 11">
                <!-- Las tres opciones mas comunes -->
            </datalist>
        </div>
        <div class="campo">
            <!-- Grupo del precio -->
            <label for="precio_hora">Precio por hora ($)</label>
            <!-- Etiqueta -->
            <input type="number" id="precio_hora" name="precio_hora" value="<?php echo h($vPrecio); ?>" required min="1" step="0.01" placeholder="800">
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
                <?php foreach (AdminController::IMAGENES as $ruta => $nombreImg) { ?>
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
            <a class="boton-chico" href="admin_canchas.php">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../includes/footer.php"; ?>
