<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'agente') {
	header('Location: autentificage.php');
	exit();
}
require_once('conf.inc.php');
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT * FROM F_AGE WHERE CODAGE=' . $_SESSION['cod_factusol'];
$rs = mysql_query($ssql,$conn);
$datos = mysql_fetch_array($rs);
$mensajeAgente = $datos['MEWAGE'];
if($mensajeAgente == '') {
	header('Location: pedagente.php');
	exit();
}
require_once('top.php');
?>

<?php require_once('menumage.php'); ?>
<table width="100%" height="100%" border="0" align="center" class="menucolorfondo">
	<tr>
		<td align="center" valign="middle"><?=$mensajeAgente?></td>
	</tr>
</table>
<?php require_once('button.php'); ?>