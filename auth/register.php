<?php
/** 
* @file
* \brief Registro en la aplicación
* \details Pantalla de registro en la aplicación. Añade cabeceras, muestra 
* los mensajes de error de action_register.php y define la estructura del layout.
* \author auth.agoraUS
*/

require_once 'captcha/ReCaptcha.php';
require_once 'captcha/RequestMethod.php';
require_once 'captcha/RequestParameters.php';
require_once 'captcha/Response.php';
require_once 'captcha/RequestMethod/Post.php';
require_once 'captcha/RequestMethod/Socket.php';
require_once 'captcha/RequestMethod/SocketPost.php';

$clave_del_sitio = "6Ldx5RYTAAAAALAbT0AF2v0Aj9tNI6nfjinPOo9E";
$clave_secreta = "6Ldx5RYTAAAAAJbkX1dztvGGHNi9Yq5aCAxSkbni";

if($_POST['accion'] == 'enviar'){
    $recaptcha = new ReCaptcha($clave_secreta);
    $respuesta = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
    if($respuesta->isSuccess()){
        echo 'El formulario ha sido validado';
    }else{
        echo 'Se ha devuelto el siguiente error:';
        foreach ($respuesta->getErrorCodes() as $error_code) {
            echo '<tt>' . $error_code . '</tt> ';
        }
    }
}

include_once("database.php");
session_start();
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta charset="utf-8">
   <link rel="stylesheet" href="layout.css" />
   <script src="lib/jquery-2.1.1.min.js"></script>
   <script src="https://www.google.com/recaptcha/api.js" async defer></script>
   
   <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="bootstrap/js/bootstrap.mi.js"></script>
	<script type="text/javascript" src="bootstrap/js/npm.js"></script>
	
	<link rel="stylesheet" href="style/style.css" type="text/css">
	
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css">
	<link rel="stylesheet" href="styles/bootstrap/css/bootstrap-theme.css" type="text/css">
	<link rel="stylesheet" href="styles/bootstrap/css/bootstrap-theme.css.map" type="text/css">
	<link rel="stylesheet" href="styles/bootstrap/css/bootstrap.css.map" type="text/css">
   
   <title><?php echo TITLE?></title>
   <script type="text/javascript">
    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"
                ))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-z
                A-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function form_process(){
        var errores = false;
        $('#error').html("");
        if ($('#username').val() == undefined || $('#username').val() == "") {
            errores = true;
            $('#error').html($('#error').html() + "-Debe elegir un nombre de usuario<br>");
        } else if ($('#username').val().length < 5) {
            errores = true;
            $('#error').html($('#error').html() + 
            "-El nombre de usuario es demasiado corto (mínimo 5 caracteres)<br>");
        }
        if ($('#password').val() == undefined || $('#password').val() == "") {
            errores = true;
            $('#error').html($('#error').html() + "-Debe elegir una contraseña<br>");
        } else if ($('#password').val().length < 5) {
            errores = true;
            $('#error').html($('#error').html() + "-La contraseña es demasiado corta (mínimo 5 caracteres)<br>");
        } else if ( $('#r_password').val() == undefined ||
                    $('#r_password').val() == "" ||
                    $('#password').val() != $('#r_password').val()) {
            errores = true;
            $('#error').html($('#error').html() + "-Las contraseñas no coinciden<br>");
        }
        if ($('#email').val() == undefined || $('#email').val() == "") {
            errores = true;
            $('#error').html($('#error').html() + "-Debe indicar una dirección de correo electrónico.<br>");
        } else if (!validateEmail($('#email').val())) {
            errores = true;
            $('#error').html($('#error').html() + "-La dirección de correo electrónico no es válida<br>");
        }
        if ($('#genre').val() == undefined || $('#genre').val() == "" || $('#genre').val() == "default" ) {
            errores = true;
            $('#error').html($('#error').html() + "-Debe elegir un género<br>");
        }
        if ($('#age').val() == undefined || $('#age').val() == "") {
            errores = true;
            $('#error').html($('#error').html() + "-Debe elegir una edad<br>");
        } else if ($('#age').val() < 1) {
            error = true;
            $('#error').html($('#error').html() + "-La edad no es válida<br>");
        }
        if ($('#autonomous_community').val() == undefined ||
            $('#autonomous_community').val() == "" || 
            $('#autonomous_community').val() == "default" ){
            errores = true;
            $('#error').html($('#error').html() + "-Debe elegir una comunidad autónoma<br>");
        }
        return !errores;
    }
</script>
<?php
    if (!isset($_SESSION['registerForm'])) {
        $registerForm['username'] = "";
        $registerForm['password'] = "";
        $registerForm['email'] = "";
        $registerForm['age'] = "";
    } else {
        $registerForm = $_SESSION['registerForm'];
    }

    $_SESSION['registerForm'] = $registerForm;
    ?>
</head>
<body>
   <div class="wrapper">
   <div class="container-fluid">
		<div class="logo">
		<h1>Registro de Agor@us<img alt="Agora@us" src="img/agoraUsImage.jpg" width="100px" height="100px"></h1>
	</div>
   <div id="error">
        <?php
            if (isset($_REQUEST['error'])) {
                $error = $_REQUEST['error'];
                if ($error % 2 != 0) {
                    echo "-Error al insertar en la base de datos.<br>";
                    $error--;
                }
                if ($error >= 23768) {
                    echo "-La edad no es válida.<br>";
                    $error -= 23768;
                }
                if ($error >= 16384) {
                    echo "-Debe introducir una edad.<br>";
                    $error -= 16384;
                }
                if ($error >= 8192) {
                    echo "-La comunidad autónoma no es válida.<br>";
                    $error -= 8192;
                }
                if ($error >= 4096) {
                    echo "-Debe elegir una comunidad autónoma.<br>";
                    $error-=4096;
                }
                if ($error >= 2048) {
                    echo "-El género no es válido.<br>";
                    $error -= 2048;
                }
                if ($error >= 1024) {
                    echo "-Debe elegir un género.<br>";
                    $error -= 1024;
                }
                if ($error >= 512) {
                    echo "-El email ya está registrado.<br>";
                    $error -= 512;
                }
                if ($error >= 256) {
                    echo "-La dirección de correo electrónico no es válida.<br>";
                    $error -= 256;
                }
                if ($error >= 128) {
                    echo "-Debe indicar una dirección de correo electrónico.<br>";
                    $error -= 128;
                }
                if ($error >= 64) {
                    echo "-Las contraseñas no coinciden.<br>";
                    $error -= 64;
                }
                if ($error >= 32) {
                    echo "-La contraseña es demasiado corta (mínimo 5 caracteres).<br>";
                    $error -= 32;
                }
                if ($error >= 16) {
                    echo "-Debe elegir una contraseña.<br>";
                    $error -= 16;
                }
                if ($error >= 8) {
                    echo "-Ese nombre de usuario ya existe.<br>";
                    $error -= 8;
                }
                if ($error >= 4) {
                    echo "-El nombre de usuario es demasiado corto (mínimo 5 caracteres).<br>";
                    $error -= 4;
                }
                if ($error >= 2) {
                    echo "-Debe elegir un nombre de usuario.<br>";
                    $error -= 2;
                }
            }
        ?>
    </div>
    <form id="registerForm" onsubmit="return form_process()" method="POST" action="action_register.php">
    	
             <i class="glyphicon glyphicon-user"></i><label for="username">Nombre de usuario:</label>
             <input  type="text" 
                     id="username" 
                     name="username" 
                     class="form-control"
                     value=<?php echo htmlentities($registerForm['username']) ?>>

<br />
                     <i class="glyphicon glyphicon-envelope"></i><label for="email">Correo electrónico</label>
                    <input  type="text" 
                            id="email" 
                            name="email" 
                             class="form-control"
                            value=<?php echo htmlentities($registerForm['email']) ?>>
<br />

                   <label for="password">Contraseña</label>
                    <input  type="password" 
                            id="password" 
                            class="form-control"
                            name="password">

<br />
                    <label for="r_password">Repetir contraseña</label>
                    <input  type="password" 
                            id="r_password"
                            class="form-control" 
                            name="r_password">

<br />
                    <label for="genre">Género</label>
                    <select id="genre" name="genre" class="form-control">
                        <option value="default">----------</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>

<br />
                    <label for="age">Edad</label>
                    <input  type="number" 
                            id="age" 
                            name="age" 
                            min="1" 
                            class="form-control"
                            value=<?php echo htmlentities($registerForm['age'])?>>
  
  <br />
                    <label for="autonomous_community">Comunidad autónoma</label>
                    <select name="autonomous_community" id="autonomous_community" class="form-control"> 
                        <option value="default" selected="true">----------</option>
                        <option value="Andalucia">Andalucia</option>
                        <option value="Murcia">Murcia</option>
                        <option value="Extremadura">Extremadura</option>
                        <option value="Castilla la Mancha">Castilla la Mancha</option>
                        <option value="Comunidad Valenciana">Comunidad Valenciana</option>
                        <option value="Madrid">Madrid</option>
                        <option value="Castilla y Leon">Castilla y Leon</option>
                        <option value="Aragon">Aragon</option>
                        <option value="Cataluña">Cataluña</option>
                        <option value="La Rioja">La Rioja</option>
                        <option value="Galicia">Galicia</option>
                        <option value="Asturias">Asturias</option>
                        <option value="Cantabria">Cantabria</option>
                        <option value="Pais Vasco">Pais Vasco</option>
                        <option value="Navarra">Navarra</option>
                    </select>

<br />
                <div class="g-recaptcha" data-sitekey="6Ldx5RYTAAAAALAbT0AF2v0Aj9tNI6nfjinPOo9E"></div>
                    <input  type="submit" 
                            id="submit" 
                            value ="Enviar" 
                           	class="btn btn-primary"/>
                           	
                    
                    <input  onClick="location.href = 'index.php' "
                            id="back" 
                            type="button"
                            value ="Volver" 
                           	class="btn btn-danger"/>

		
		
    </form>
    <div class="push"></div>
    </div>
    <div class="footer">
        <i class="glyphicon glyphicon-copyright-mark"></i><b>Copyright</b>
    </div>
</body>
</html>