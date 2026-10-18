<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AdminController.php";                           // Carga el controlador de administracion.
require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo de usuarios.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$adminCtrl = new AdminController($conexion);                                            // Crea el controlador de administracion.
$usuarioModelo = new Usuario($conexion);                                                // Crea el modelo de usuarios.

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "eliminar") { // Si el admin apreto "Eliminar"...
    $resultado = $adminCtrl->eliminarUsuario((int)($_POST["usuario_id"] ?? 0), $_SESSION["usuario_id"]); // ...pide eliminar (no deja borrarse a si mismo).
    setMensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]);         // Guarda el mensaje para mostrarlo.
    header("Location: admin_usuarios.php");                                             // Recarga la pagina.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$usuarios = $usuarioModelo->listarTodos();                                              // Trae todos los usuarios.

$tituloPagina = "Administrar usuarios";                                                 // Titulo que usa el header.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
?>
<div class="encabezado-seccion">
    <!-- Fila con el titulo y el boton de alta -->
    <h1 class="panel-titulo">Usuarios</h1>
    <!-- Titulo de la pagina -->
    <a class="boton-panel" href="admin_usuario_form.php">+ Nuevo usuario</a>
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
                        <td><?php echo (int)$u["id"]; ?></td>
                        <!-- Numero de usuario -->
                        <td><?php echo h($u["nombre"]); ?></td>
                        <!-- Nombre -->
                        <td><?php echo h($u["email"]); ?></td>
                        <!-- Email -->
                        <td><?php echo h($u["telefono"]); ?></td>
                        <!-- Telefono -->
                        <td><span class="etiqueta etiqueta-<?php echo $u["rol"] === "admin" ? "admin" : "confirmada"; ?>"><?php echo h(ucfirst($u["rol"])); ?></span></td>
                        <!-- Rol con color -->
                        <td class="acciones">
                            <!-- Botones de accion -->
                            <a class="boton-chico" href="admin_usuario_form.php?id=<?php echo (int)$u["id"]; ?>">Editar</a>
                            <!-- Enlace a la modificacion -->
                            <?php if ((int)$u["id"] !== (int)$_SESSION["usuario_id"]) { ?>
                                <!-- El admin no ve el boton Eliminar en su propia fila -->
                                <form action="admin_usuarios.php" method="post" onsubmit="return confirm('¿Eliminar este usuario?');">
                                    <!-- Formulario de baja con confirmacion -->
                                    <input type="hidden" name="accion" value="eliminar">
                                    <!-- Accion: eliminar -->
                                    <input type="hidden" name="usuario_id" value="<?php echo (int)$u["id"]; ?>">
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
<?php require __DIR__ . "/../includes/footer.php"; ?>
