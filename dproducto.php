<?php
session_start();
if (@$_SESSION['autentificado'] == 'SI' ) {
	define('LOGEADO', 'si');
}else{
	define('LOGEADO', 'no');
}
require_once('cmodulos.php');
imoduloart ();
cmoduloart ();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Detalle de Producto</title>
<link href="plantillas/<?php echo(PLANTILLA); ?>/estilos/estilo.css" rel="stylesheet" type="text/css" />
<script languaje="javascript"> 
function cerrarse(){ 
	window.close() 
} 
</script> 
</head>
<body onLoad="this.focus();">

<?php
require_once('conf.inc.php');
require_once('func.php');
$conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
mysql_select_db (BD_DATABASE1);
//recojo el codigo del articulo y la tarifa
$codart=$_GET["cod"];
if (isset ($_GET["precio"])){ 
      $precio=$_GET["precio"];
}else{
      if (espredefinida ($_GET["tar"],$_GET["cliente"])!=1){
             $precio=preciodesc ($codart, $_GET["cliente"]);
      }else{
            $ssql="SELECT * FROM F_LTA WHERE TARLTA=".$_GET["tar"]." AND ARTLTA='".$codart."'";
            $rs1=mysql_query ($ssql,$conn);
            $datos1=mysql_fetch_array ($rs1);
            $precio=$datos1["PRELTA"];
      }
}
//datos del articulo
$ssql = 'SELECT * FROM F_ART WHERE CODART=\'' . $codart . '\'';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs);
//Carpeta de imagenes
$ssql = 'SELECT * FROM F_CFG';
$rs3 = mysql_query($ssql, $conn);
$carpeta = mysql_fetch_array($rs3);
if($datos['IMGART'] == '') {
	$imagen = '';
}else{
	$imagen = $datos['IMGART'];
}
?>
<div align="center">
<table width="99%" border="1" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td width="60%" height="30" class="cabecera">&nbsp;<?php echo($codart . ' - ' . $datos['DESART']); ?> </td>
    <td class="cabecera">
<?php if( ($carpeta['MPRCFG'] == 1) && ( (LOGEADO == 'si') || (MOSTRARPRECIO == 'si') ) ) { ?>
			<div align="right">Precio: <?php printf("%.2f", $precio); ?>&nbsp;</div>
<?php } ?>
		</td>
  </tr>
  <tr>
    <td height="144" valign="top" style="padding:2px">Descripción:<br><br><?php echo(saltolinea($datos['DEWART'])); ?> </td>
    <td width="27%" valign="top" aling="center"><div align="center">
<?php
if( (file_exists('BBDD/' . $carpeta['CIACFG'] . $imagen)) && ($imagen != '') ) {
		echo('<img src="BBDD/' . $carpeta['CIACFG'] . $imagen . '" id="imagen1">');
	}else{
		echo('<img src="plantillas/' . PLANTILLA . '/imagenes/IND.gif" id="imagen1">');
	}
?>
		</div>
<?php
		if ($carpeta['MCBCFG'] == 1) { echo('<br>Cod. Barras:<br>' . $datos['EANART']); }
?>
		</td>
  </tr>
  <tr>
    <td height="38" colspan="2">&nbsp;<?php echo $datos["MEWART"];?> </td>
  </tr>
<?php
if($carpeta['MCP1CFG'] == 1) { echo('<tr><td colspan="2" class="cabecera" style="padding:2px">' . $carpeta['NCP1CFG'] . ': ' . $datos['CP1ART'] . '</td></tr>'); }
if($carpeta['MCP2CFG'] == 1) { echo('<tr><td colspan="2" class="cabecera" style="padding:2px">' . $carpeta['NCP2CFG'] . ': ' . $datos['CP2ART'] . '</td></tr>'); }
if($carpeta['MCP3CFG'] == 1) { echo('<tr><td colspan="2" class="cabecera" style="padding:2px">' . $carpeta['NCP3CFG'] . ': ' . $datos['CP3ART'] . '</td></tr>'); }
?>
	</tr>
</table>
</div>
<p align="center"><br><input type=button value="Cerrar Ventana" onClick="cerrarse()"></p>
</body>
</html>