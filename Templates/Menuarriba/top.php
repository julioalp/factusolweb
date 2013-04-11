<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<script src="./func/funciones.js"></script>
<title></title>
<link href="estilo.css" rel="stylesheet" type="text/css" />
<base target="_self">
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" link="#0000FF" vlink="#0000FF" alink="#0000FF">

<p align="center">
<?
require('conf.inc.php');
$conn=odbc_connect(DSN1,BD_USERNAME,BD_PASSWORD);
$ssql="SELECT * FROM F_CFG";
$rs=odbc_exec($conn,$ssql);
$datos=odbc_fetch_array($rs);
echo'<img border="0" src="'.$datos["LOGCFG"].'" height="70"></p>';
?>

<p align="center"><b><font face="Verdana" size="1">
<a target="_top" href="index.php"><span style="text-decoration: none">Inicio</span></a> - 
<a target="_top" href="productos.php"><span style="text-decoration: none">Ver catálogo</span></a> - 
<a target="_top" href="autentifica.php"><span style="text-decoration: none">Zona de clientes</span></a> - 
<a target="_top" href="autentificage.php"><span style="text-decoration: none">Intranet</span></a></font></b></p>
<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" width="100%" id="table2" cellspacing="0" cellpadding="0" bgcolor="#C0C0C0">
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table></td>
  </tr>
  <tr>
  	<td>
