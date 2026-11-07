<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/AdminController.php";                           // Carga el controlador de administracion.
require_once __DIR__ . "/../models/Usuario.php";                                        // Carga el modelo de usuarios.

requerirAdmin();                                                                        // Solo pueden entrar administradores.

$adminCtrl = new AdminController($conexion);                                            // Crea el controlador de administracion.
$usuarioModelo = new Usuario($conexion);                                                // Crea el modelo de usuarios.

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);                                          // Si viene un id, es MODIFICACION; si no, es ALTA.
$usuario = $id > 0 ? $usuarioModelo->buscarPorId($id) : null;                           // Busca el usuario si estamos editando.
if ($id > 0 && !$usuario) {                                                             // Si pidio editar un usuario que no existe...
    setMensaje("error", "Usuario no encontrado");                                       // ...guarda el error.
    header("Location: admin_usuarios.php");                                             // ...y vuelve a la lista.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$error = "";                                                                            // Aca se guarda el error del formulario.

if ($_SERVER["REQUEST_METHOD"] === "POST") {                                            // Si el admin envio el formulario...
    $nombre = trim($_POST["nombre"] ?? "");                                             // Lee el nombre.
    $email = trim($_POST["email"] ?? "");                                               // Lee el email (solo se usa en el alta).
    $telefono = trim($_POST["telefono"] ?? "");                                         // Lee el telefono.
    $rol = $_POST["rol"] ?? "cliente";                                                  // Lee el rol.
    $password = $_POST["password"] ?? "";                                               // Lee la contraseña.
    if ($usuario) {                                                                     // Si estamos modificando...
        $resultado = $adminCtrl->actualizarUsuario($id, $nombre, $telefono, $rol, $password, $_SESSION["usuario_id"]); // ...pide actualizar.
    } else {                                                                            // Si estamos dando de alta...
        $resultado = $adminCtrl->crearUsuario($nombre, $email, $telefono, $password, $rol); // ...pide crear.
    }                                                                                   // Cierra el if/else.
    if ($resultado["exito"]) {                                                          // Si salio bien...
        setMensaje("exito", $resultado["mensaje"]);                                     // ...guarda el mensaje de exito.
        header("Location: admin_usuarios.php");                                         // ...y vuelve a la lista.
        exit;                                                                           // Corta la ejecucion.
    }                                                                                   // Cierra el if.
    $error = $resultado["mensaje"];                                                     // Si salio mal, guarda el error para mostrarlo.
}                                                                                       // Cierra el if del POST.

$vNombre = $_POST["nombre"] ?? ($usuario["nombre"] ?? "");                              // Valor a mostrar en nombre.
$vEmail = $_POST["email"] ?? ($usuario["email"] ?? "");                                 // Valor a mostrar en email.
$vTelefono = $_POST["telefono"] ?? ($usuario["telefono"] ?? "");                        // Valor a mostrar en telefono.
$vRol = $_POST["rol"] ?? ($usuario["rol"] ?? "cliente");                                // Valor a mostrar en rol.

$tituloPagina = $usuario ? "Editar usuario" : "Nuevo usuario";                          // Titulo segun el modo.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
?>
<h1 class="panel-titulo"><?php echo $usuario ? "Editar usuario" : "Nuevo usuario"; ?></h1>
<!-- Titulo segun alta o modificacion -->

<?php if ($error !== "") { ?>
    <!-- Si hubo error, lo muestra -->
    <p class="error"><?php echo h($error); ?></p>
    <!-- Cuadro rojo con el error -->
<?php } ?>

<section class="tarjeta tarjeta-angosta">
    <!-- Caja blanca con el formulario -->
    <form class="formulario-panel" action="admin_usuario_form.php" method="post">
        <!-- Formulario que se envia a esta misma pagina -->
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <!-- Campo oculto con el id (0 si es alta) -->
        <div class="campo">
            <!-- Grupo del nombre -->
            <label for="nombre">Nombre</label>
            <!-- Etiqueta -->
            <input type="text" id="nombre" name="nombre" value="<?php echo h($vNombre); ?>" required>
            <!-- Campo de texto del nombre -->
        </div>
        <div class="campo">
            <!-- Grupo del email -->
            <label for="email">Correo electronico</label>
            <!-- Etiqueta -->
            <input type="email" id="email" name="email" value="<?php echo h($vEmail); ?>" <?php echo $usuario ? "disabled" : "required"; ?>>
            <!-- Al editar el email no se puede cambiar (disabled); al crear es obligatorio -->
        </div>
        <div class="campo">
            <!-- Grupo del telefono -->
            <label for="telefono">Telefono</label>
            <!-- Etiqueta -->
            <input type="text" id="telefono" name="telefono" value="<?php echo h($vTelefono); ?>" required>
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
            <label for="password"><?php echo $usuario ? "Contraseña nueva (dejar vacio para no cambiarla)" : "Contraseña"; ?></label>
            <!-- Etiqueta que cambia segun el modo -->
            <input type="password" id="password" name="password" <?php echo $usuario ? "" : "required"; ?> placeholder="Minimo 6 caracteres">
            <!-- Obligatoria solo al crear -->
        </div>
        <div class="fila">
            <!-- Fila con los botones -->
            <button class="boton-panel" type="submit"><?php echo $usuario ? "Guardar cambios" : "Crear usuario"; ?></button>
            <!-- Boton de guardar -->
            <a class="boton-chico" href="admin_usuarios.php">Volver</a>
            <!-- Enlace para volver sin guardar -->
        </div>
    </form>
</section>
<?php require __DIR__ . "/../includes/footer.php"; ?>
