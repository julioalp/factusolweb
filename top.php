<?php
require_once('conf.inc.php');
$conn = mysql_connect(BD_HOST,BD_USERNAME,BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = "SELECT LOGCFG FROM F_CFG";
$rs = mysql_query($ssql,$conn);
$datosCfg = mysql_fetch_array($rs);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="func/funciones.js"></script>
<title><? echo TITULO; ?></title>
<link href="plantillas/<?php echo(PLANTILLA); ?>/estilos/estilo.css" rel="stylesheet" type="text/css" />
<base target="_self">
<style type="text/css">
<!--
#Layer2 { position:relative; right:0px; z-index:1; }
#Layer1 { position:absolute; right:0px; z-index:2; top:54px; }
-->
</style>
</head>
<body>

<table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" height="82">
      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
          <td height="54" align="right" valign="middle" style="background-color:#<?=COLORCABECERA?>">
<?php
if($datosCfg['LOGCFG'] != '') {
	echo('<img border="0" src="' . $datosCfg['LOGCFG'] . '">');
}else{
?>
						<p style="font-size:30px; color:#CCCCCC; font-family:Arial; margin-right:20px"><?=TITULO?></p>
<?php } ?>
					</td>
        </tr>
				<tr>
					<td height="28" valign="top" class="degradado" style="background-repeat:repeat-x; background-position:bottom; background-color:#<?=COLORCABECERABARRA?>">
						<p align="right" style="margin-bottom:0px; margin-right:20px; margin-top:5px; font-weight:bold; font-size:11px; font-family:Verdana, Arial, Helvetica, sans-serif">
							<a href="index.php" style="color:#<?=COLORCABECERATEXTO?>">Inicio</a>
							&nbsp; &nbsp; &nbsp; <a href="productos.php" style="color:#<?=COLORCABECERATEXTO?>">Productos</a>
							&nbsp;&nbsp; &nbsp; <a href="autentifica.php" style="color:#<?=COLORCABECERATEXTO?>">Zona de Clientes</a>
							&nbsp; &nbsp; &nbsp; <a href="autentificage.php" style="color:#<?=COLORCABECERATEXTO?>">Intranet</a></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" height="100%">