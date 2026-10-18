<?php                                                                                   // Abre el bloque de codigo PHP.
require_once __DIR__ . "/../includes/auth.php";                                         // Carga las funciones de sesion y permisos.
require_once __DIR__ . "/../../config/conexion.php";                                    // Carga la conexion a la base de datos.
require_once __DIR__ . "/../controllers/ReservaController.php";                         // Carga el controlador de reservas.

requerirCliente();                                                                      // Solo clientes con sesion pueden pagar.
$reservaCtrl = new ReservaController($conexion);                                        // Crea el controlador de reservas.
$errorPago = "";                                                                        // Aca se guarda el error del formulario de tarjeta.

if ($_SERVER["REQUEST_METHOD"] === "POST") {                                            // Si llegaron datos por POST...
    $paso = $_POST["paso"] ?? "";                                                       // Lee en que paso del proceso estamos.

    if ($paso === "resumen") {                                                          // PASO 1: viene del dashboard con cancha, fecha y hora.
        $canchaId = (int)($_POST["cancha_id"] ?? 0);                                    // Id de la cancha elegida.
        $fecha = trim($_POST["dia_reserva"] ?? "");                                     // Fecha elegida (el campo se llama dia_reserva).
        $hora = trim($_POST["turno_reserva"] ?? "");                                    // Hora elegida (el campo se llama turno_reserva).
        $validacion = $reservaCtrl->validarSolicitud($canchaId, $fecha, $hora);         // Verifica que se pueda reservar eso.
        if ($validacion["error"] !== "") {                                              // Si no se puede...
            setMensaje("error", $validacion["error"]);                                  // ...guarda el error.
            header("Location: dashboard.php");                                          // ...y vuelve al dashboard.
            exit;                                                                       // Corta la ejecucion.
        }                                                                               // Cierra el if.
        $_SESSION["reserva_pendiente"] = ["cancha_id" => $canchaId, "fecha" => $fecha, "hora" => $hora]; // Guarda la reserva "en espera" de pago (todavia NO esta en la base).
    } elseif ($paso === "pagar") {                                                      // PASO 2: el usuario envio los datos de la tarjeta.
        $p = $_SESSION["reserva_pendiente"] ?? null;                                    // Recupera la reserva en espera.
        if (!$p) {                                                                      // Si no hay ninguna...
            header("Location: dashboard.php");                                          // ...vuelve al dashboard.
            exit;                                                                       // Corta la ejecucion.
        }                                                                               // Cierra el if.
        $titular = trim($_POST["pg_titular"] ?? "");                                    // Nombre del titular escrito.
        $numero = trim($_POST["pg_numero"] ?? "");                                      // Numero de tarjeta escrito.
        $vencimiento = trim($_POST["pg_venc"] ?? "");                                   // Vencimiento escrito.
        $cvv = trim($_POST["pg_codigo"] ?? "");                                         // CVV escrito.
        $errorPago = $reservaCtrl->validarTarjeta($titular, $numero, $vencimiento, $cvv); // Valida el formato de la tarjeta ficticia.
        if ($errorPago === "") {                                                        // Si la tarjeta esta bien...
            $resultado = $reservaCtrl->confirmarReservaConPago($_SESSION["usuario_id"], $p["cancha_id"], $p["fecha"], $p["hora"], $numero); // ...registra reserva + pago.
            unset($_SESSION["reserva_pendiente"]);                                      // Borra la reserva en espera (ya se proceso).
            setMensaje($resultado["exito"] ? "exito" : "error", $resultado["mensaje"]); // Guarda el mensaje de resultado.
            header("Location: dashboard.php");                                          // Vuelve al dashboard a ver la reserva.
            exit;                                                                       // Corta la ejecucion.
        }                                                                               // Cierra el if (si hubo error, sigue y muestra el formulario).
    }                                                                                   // Cierra el if/elseif de pasos.
}                                                                                       // Cierra el if del POST.

$pendiente = $_SESSION["reserva_pendiente"] ?? null;                                    // Lee la reserva en espera.
if (!$pendiente) {                                                                      // Si no hay nada para pagar (ej: entro directo a pago.php)...
    header("Location: dashboard.php");                                                  // ...vuelve al dashboard.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.

$validacion = $reservaCtrl->validarSolicitud($pendiente["cancha_id"], $pendiente["fecha"], $pendiente["hora"]); // Revalida: alguien pudo reservar ese turno mientras tanto.
if ($validacion["error"] !== "") {                                                      // Si ya no se puede...
    unset($_SESSION["reserva_pendiente"]);                                              // ...descarta la reserva en espera.
    setMensaje("error", $validacion["error"]);                                          // ...guarda el motivo.
    header("Location: dashboard.php");                                                  // ...y vuelve al dashboard.
    exit;                                                                               // Corta la ejecucion.
}                                                                                       // Cierra el if.
$cancha = $validacion["cancha"];                                                        // Datos de la cancha para mostrar el resumen.

$tituloPagina = "Pago";                                                                 // Titulo que usa el header.
require __DIR__ . "/../includes/header.php";                                            // Dibuja la parte de arriba.
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
            <dt>Cancha</dt><dd><?php echo h($cancha["nombre"] . " (" . $cancha["tipo"] . ")"); ?></dd>
            <!-- Nombre y tipo de cancha -->
            <dt>Fecha</dt><dd><?php echo h(date("d/m/Y", strtotime($pendiente["fecha"]))); ?></dd>
            <!-- Fecha en formato dia/mes/año -->
            <dt>Horario</dt><dd><?php echo h($pendiente["hora"] . " a " . date("H:i", strtotime($pendiente["hora"] . " +1 hour"))); ?></dd>
            <!-- Hora de inicio y de fin (1 hora despues) -->
            <dt>Total</dt><dd class="total">$<?php echo number_format($cancha["precio_hora"], 0, ",", "."); ?></dd>
            <!-- Precio a pagar -->
        </dl>
        <a class="volver-inicio" href="dashboard.php">← Elegir otro horario</a>
        <!-- Enlace para volver sin pagar -->
    </section>

    <section class="tarjeta">
        <!-- Caja del formulario de tarjeta -->
        <h2 class="panel-subtitulo">Pago con tarjeta</h2>
        <!-- Titulo de la caja -->
        <p class="aviso">Pago <strong>simulado</strong>: no se cobra nada ni se guarda el numero de tarjeta. Para probar usa 4242 4242 4242 4242, un vencimiento futuro y cualquier CVV de 3 digitos.</p>
        <!-- Aviso de que el pago es ficticio -->
        <form class="formulario-panel" action="pago.php" method="post" autocomplete="off">
            <!-- Formulario de la tarjeta; autocomplete off evita que el navegador sugiera datos -->
            <input type="hidden" name="paso" value="pagar">
            <!-- Indica que es el segundo paso (pagar) -->
            <div class="campo">
                <!-- Grupo del titular -->
                <label for="pg_titular">Titular de la tarjeta</label>
                <!-- Etiqueta -->
                <input type="text" id="pg_titular" name="pg_titular" required autocomplete="off" placeholder="Como figura en la tarjeta">
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
            <button class="boton-panel" type="submit">Pagar $<?php echo number_format($cancha["precio_hora"], 0, ",", "."); ?> y reservar</button>
            <!-- Boton que envia el pago -->
        </form>
    </section>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
