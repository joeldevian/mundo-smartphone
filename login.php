<?php 
include 'inc/comun.php'; 
session_start(); // Asegúrate de que la sesión esté iniciada
?>
<!DOCTYPE html>
<html class="lockscreen">
<head>
    <meta charset="UTF-8">
    <title>Sistema Inventario</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/png" href="../images/client_13.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <script src="js/calendario/jquery-ui.min.js" type="text/javascript"></script>
    <script src="js/tipo_alimen.js"></script>
    <script src="js/validarfrom.js"></script>

    <script>
        $(function() {
            $("[data-role='navbar']").navbar();
            $("[data-role='header'], [data-role='footer']").toolbar();
        });

        $(document).on("pagecontainerchange", function() {
            var current = $(".ui-page-active").jqmData("title");
            $("[data-role='header'] h1").text(current);
            $("[data-role='navbar'] a.ui-btn-active").removeClass("ui-btn-active");
            $("[data-role='navbar'] a").each(function() {
                if ($(this).text() === current) {
                    $(this).addClass("ui-btn-active");
                }
            });
        });
    </script>
</head>
<body class="lockscreen">
<?php
$bd = new GestarBD;

if (isset($_POST["iniciar"])) {
    $usuario = $_POST["usuario"];
    $password = $_POST["pass"];

    // Se agregó la función de hash para las contraseñas
    // Se recomienda almacenar las contraseñas en la base de datos como hash
    $hashed_password = hash('sha256', $password); // Cambia el método de hash según tu implementación

    // Sin aplicar hash, usa directamente la contraseña ingresada
    $usuario = $bd->SelectText('*', 'administrador', "correo='$usuario' AND pass='$password'", false, null, null);

    $bd->consulta($usuario);
    
    if ($mostrar = $bd->mostrar_registros()) {
        $_SESSION['dondequedavalida'] = true;
        $_SESSION['dondequeda_tipo'] = $mostrar['nive_usua'];
        $_SESSION['dondequeda_nombre'] = $mostrar['nombre'];
        $_SESSION['dondequeda_apellido'] = $mostrar['apellido'];
        $_SESSION['dondequeda_nive_usua'] = $mostrar['nive_usua'];
        $_SESSION['dondequeda_usuario'] = $mostrar['usuario'];
        $_SESSION['dondequeda_correo'] = $mostrar['correo'];
        $_SESSION['dondequeda_id'] = $mostrar['id'];

        header("Location: index.php?mod=index");
        exit;
    } else {
        // Mensaje de error mejorado
        echo '<div class="form-box">
                <div class="alert alert-warning alert-dismissable">
                    <i class="fa fa-warning"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <b>Inicio de Sesión !</b>  Usuario o Contraseña Incorrectos. Intente de nuevo.
                </div>
              </div>';
    }
}

// Manejo de código de registro temporal
if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $query = "SELECT * FROM registros_temp WHERE codigo = '$codigo'";
    $bd->consulta($query);

    if ($temp_resg = $bd->mostrar_registros()) {
        // Inserción segura con parámetros preparados (cambia a un método que utilice esto si es posible)
        $insertUser = "INSERT INTO `administrador` (`usuario`, `pass`, `nombre`, `correo`, `nive_usua`, codigo_user, codigo_referr)
                       VALUES (
                           '{$temp_resg['usuario']}', 
                           '{$temp_resg['pass']}', 
                           '{$temp_resg['nombre']}',  
                           '{$temp_resg['email']}', 
                           '{$temp_resg['nive_usua']}', 
                           '$codigo', 
                           '{$temp_resg['codigo_referr']}'
                       )";
        $bd->consulta($insertUser);

        $borrarTemp = "DELETE FROM registros_temp WHERE codigo = '$codigo'";
        $bd->consulta($borrarTemp);

        echo '<div class="form-box">
                <div class="alert alert-success alert-dismissable">
                    <i class="fa fa-check"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <b>Registro !</b>  Has confirmado tu cuenta correctamente Ingresa al sistema.
                </div>
              </div>';
    }
}
?>

<div class="form-box" id="login-box">
    <div class="header">Inicie Sesión</div>
    <form name="frmLogin" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post"> <!-- Se agregó htmlspecialchars -->
        <div class="body bg-gray">
            <div class="form-group">
                <input type="email" name="usuario" class="form-control" placeholder="Correo Electrónico" required />
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control" placeholder="Contraseña" required />
            </div>
            <div class="form-group">
                <input type="checkbox" name="remember_me" /> Recordar
            </div>
        </div>
        <div class="footer">
            <button type="submit" name="iniciar" class="btn bg-olive btn-block">Entrar</button>
        </form>
        <center>
            <button type="button" data-toggle="modal" data-target="#myModal" class="btn bg-olive btn-block">¿Olvidó su contraseña?</button>

            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <center>
                                <form role="form" name="fe" action="" method="post">
                                    <h4 class="modal-title" id="myModalLabel">Recuperar Contraseña</h4>
                        </div>
                        <div class="modal-body">
                            <center>
                                <div class="span12 alert alert-success" style="margin-left: 0">Datos para recuperar tu contraseña.</div>
                            </center>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label>Correo</label>
                                        <p><input type="email" name="email" placeholder="Correo Electrónico" required class="form-control"></p>
                                    </div>
                                </div>
                            </div>
                            </br>
                            <div class="span12 alert alert-success" style="margin-left: 0">Te enviaremos tu contraseña a tu correo</div>
                        </div>

                        <div class="modal-footer">
                            <button name="btn1" type="submit" value="Agregar" class="btn btn-primary">Enviar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </center>
    </div>
</div>
</body>
</html>
