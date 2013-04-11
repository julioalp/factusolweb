<?php
require_once('conf.inc.php');
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT MICCFG, PPCCFG, PCFCFG FROM F_CFG';
$rs = mysql_query($ssql, $conn);
$datosCfg = mysql_fetch_array($rs);
?>

<table width="100%" height="25" cellpadding="0" cellspacing="0" class="menucolorfondonombre">
	<tr>
		<td align="left">&nbsp;<? echo($_SESSION['cod_factusol']); ?> - <? echo($_SESSION['usuario']); ?></td>
		<td align="right" valign="middle" style="padding-right:1px">
<?php
//Comprobamos los modulos instalados
if($datosCfg['MICCFG'] == 1) { ?>
	<a href="productos.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botoncatalogodeproductos.png" border=0></a>&nbsp;
<?php }
if($datosCfg['PPCCFG'] == 1) { ?>
	<a href="pproductos.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonpedidos.png" border=0></a>&nbsp;
<?php }
if($datosCfg['PCFCFG'] == 1) { ?>
	<a href="rfacturas.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonconsultadefacturas.png" border=0></a>&nbsp;
<?php }
//Cerrar Session 
?>
<a href="salir.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botoncerrarlasesion.png" border=0></a>
		</td>
	</tr>
</table>