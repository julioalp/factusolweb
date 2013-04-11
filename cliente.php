<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'usuario') { 
	header('Location: autentifica.php');
	exit();
}
require_once('conf.inc.php');
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $_SESSION['cod_factusol'];
$rs = mysql_query($ssql, $conn);
$datoscli = mysql_fetch_array($rs);
if(strlen($datoscli['MEWCLI']) < 1) {
	header('Location: pproductos.php');
	exit();
}
require_once('top.php');
?>

<?php
require_once('menum.php');
echo('<table width="60%" height="149" border="0" align="center">');
echo('<tr>');
echo('<td align="center">' . $datoscli['MEWCLI'] . '</td>');
echo('</tr>');
echo('</table>');
echo('<p></p>');
echo('<p></p>');
require_once('button.php');
?>