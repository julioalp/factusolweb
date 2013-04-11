<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="./func/funciones.js"></script>
<title>Untitled Document</title>
<link href="estilo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
a { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color: #000000; font-weight: bold; }
a:visited { color: #000000; }
a:hover { color: #000000; }
a:active { color: #000000; }
-->
</style></head>

<body>

<table width="700" height="263" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="22%" valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><?
require('conf.inc.php');
$conn=odbc_connect(DSN1,BD_USERNAME,BD_PASSWORD);
$ssql="SELECT * FROM F_CFG";
$rs=odbc_exec($conn,$ssql);
$datos=odbc_fetch_array($rs);
echo'<img border="0" src="'.$datos["LOGCFG"].'" height="70"></p>';
?></td>
      </tr>
      <tr>
        <td><a href="index.php">Inicio</a></td>
      </tr>
      <tr>
        <td><a href="productos.php">Catalogo</a></td>
      </tr>
      <tr>
        <td><a href="autentifica.php">Zona Clientes </a></td>
      </tr>
      <tr>
        <td><a href="autentificage.php">Intranet</a></td>
      </tr>
    </table></td>
    <td width="78%">
