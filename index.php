<?php
require_once('top.php');
?>

<style type="text/css">
<!--
.Estilo1 {font-size: 18px}
-->
</style>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="middle">
<?php
$edValor = '';
$sql = "SELECT CONTPAG FROM PAGINAS WHERE NOMBPAG = 'inicio'";
$conexioSQL = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
$seleccioBD = mysql_select_db(BD_DATABASE1, $conexioSQL);
$rs = mysql_query($sql, $conexioSQL);
@$datosPaginas = mysql_fetch_assoc($rs);
$edValor = $datosPaginas['CONTPAG'];
echo($edValor);
?>
		</td>
	</tr>
</table>
<?php require_once('button.php'); ?>