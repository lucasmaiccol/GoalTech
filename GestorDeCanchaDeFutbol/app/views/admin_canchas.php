<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AdminController.php";                           // Carga el controlador de administracion.
require_once __DIR__ . "/../models/Cancha.php";                                         // Carga el modelo de canchas.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$adminCtrl = new AdminController($conexion);                                            // Crea el controlador de administracion.
$canchaModelo = new Cancha($conexion);                                                  // Crea el modelo de canchas.

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "eliminar") { // Si el admin apreto "Eliminar"...
    $resultado = $adminCtrl->eliminarCancha((int)($_POST["cancha_id"] ?? 0));           // ...pide eliminar la cancha.
    setMensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]);         // Guarda el mensaje para mostrarlo.
    header("Location: admin_canchas.php");                                              // Recarga la pagina.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$canchas = $canchaModelo->listarTodas();                                                // Trae todas las canchas.

$tituloPagina = "Administrar canchas";                                                  // Titulo que usa el header.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Canchas</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="admin_cancha_form.php">+ Nueva cancha</a>
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
                            <td><?php echo (int)$c["id"]; ?></td>
                            <!-- Numero de cancha -->
                            <td><?php echo htmlImagenCancha($c, "miniatura", "lazy"); ?></td>
                            <!-- Miniatura de la foto de la cancha -->
                            <td><?php echo h($c["nombre"]); ?></td>
                            <!-- Nombre -->
                            <td><?php echo h($c["tipo"]); ?></td>
                            <!-- Tipo -->
                            <td>$<?php echo number_format($c["precio_hora"], 0, ",", "."); ?></td>
                            <!-- Precio -->
                            <td><span class="etiqueta etiqueta-<?php echo $c["estado"] === "activa" ? "confirmada" : "cancelada"; ?>"><?php echo h(ucfirst($c["estado"])); ?></span></td>
                            <!-- Estado con color: activa (verde) o inactiva (rojo) -->
                            <td class="acciones">
                                <!-- Botones de accion -->
                                <a class="boton-chico" href="admin_cancha_form.php?id=<?php echo (int)$c["id"]; ?>">Editar</a>
                                <!-- Enlace a la modificacion -->
                                <form action="admin_canchas.php" method="post" onsubmit="return confirm('¿Eliminar esta cancha?');">
                                    <!-- Formulario de baja con confirmacion -->
                                    <input type="hidden" name="accion" value="eliminar">
                                    <!-- Accion: eliminar -->
                                    <input type="hidden" name="cancha_id" value="<?php echo (int)$c["id"]; ?>">
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
<?php require __DIR__ . "/../includes/footer.php"; ?>
