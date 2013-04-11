<?php
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT * FROM F_CFG';
$rs = mysql_query($ssql, $conn);
$datoscfg = mysql_fetch_array($rs);
?>

<table width="100%" height="25" border="0" cellspacing="0" cellpadding="0" class="menucolorfondonombre">
	<tr>
		<td align="left">&nbsp;<?=$_SESSION['cod_factusol']?> - <?=$_SESSION['usuario']?></td>
		<td align="right" valign="middle" style="padding-right:1px">
<?php
//Comprobamos los modulos instalados para los agentes
if($datoscfg['PPACFG'] == 1) {
?>
<a href="pedagente.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonpedidos.png" border=0></a>&nbsp;
<?php
}
//Cerrar Session
?>
<a href="salir.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botoncerrarlasesion.png" border=0></a>
		</td>
	</tr>
</table>