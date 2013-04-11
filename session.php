<?php
require('conf.inc.php');
$usuario = addslashes(htmlspecialchars(strip_tags($_POST['usuario'])));
$password = addslashes(htmlspecialchars(strip_tags($_POST['password'])));
//conectamos con la base de datos
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
//hacemos la consulta para comprobar el usuario y password
$ssql = "SELECT * FROM F_CLI WHERE CUWCLI='" . $usuario . "' and CAWCLI='" . $password . "'"; 
$rs = mysql_query($ssql, $conn); 

//si el registro tiene por lo menos 1 existe el usuario y password.
if(mysql_num_rows($rs) != 0) { 
    //usuario y contraseña válidos 
    //defino una sesion y guardo datos 
    session_start(); 
    $_SESSION['autentificado'] = 'SI'; 
    $_SESSION['tipo_usuario'] = 'usuario';
    //Muestro el mensaje.
    $datos = mysql_fetch_array($rs);
    $_SESSION['cod_factusol'] = $datos['CODCLI'];
    $_SESSION['usuario'] = $datos['NOCCLI'];
    header('Location: cliente.php');
		exit();       
}else { 
    //si no existe Mando el error
    //echo 'Usuario o Password Incorrectos';
    header('Location: autentifica.php?ERROR=Usuario o Password Incorrectos');
		exit();
}
?>
