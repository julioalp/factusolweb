<?php require_once('../conf.inc.php'); ?>
<html>
<head>
<title>Activacion base de datos</title>
<style type="text/css">
<!--
.Estilo1 {font-size: 16px}
.Estilo3 {font-size: 16px; font-weight: bold; }
.Estilo4 {font-size: 24px; font-weight: bold; }
.Estilo5 {color: #0000FF}
-->
</style>
</head>
<body>

<script language="javascript" type="text/javascript">
function mandar() {
	document.getElementById("boton").style.visibility="hidden";
	document.getElementById("texto").style.visibility="visible";
	return true;
}
</script>
<table width="100%" border="0" align="center">
  <tr>
    <td width="748"><h1 align="left" class="Estilo1"><img src="../plantillas/<?php echo(PLANTILLA); ?>/imagenes/logoEmpresa.gif" alt="Software DELSOL" width="300" height="169"></h1></td>
    <td width="748" valign="bottom"><div align="right">
      <p class="Estilo3">Actualizaci&oacute;n de la Base de Datos  </p>
      <p class="Estilo4">FactuSOL <span class="Estilo5">Web</span> </p>
    </div></td>
  </tr>
</table>
<form name="formulario" action="index2.php" method="POST" onSubmit="return mandar()">
<hr>
<br>
<br>
<br>
<table width="336" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#6666AA">
	<tr>
		<td><p>Se va a actualizar la base de datos, pulse en el bot&oacute;n Proceder para comenzar el proceso.</p>
			<p style="font-size:11px">Nota: Este proceso puede durar varios segundos/minutos, dependiendo de la cantidad de datos a introducir.
</p>
			<p align="right"><span id="texto" style="visibility:hidden; text-align:center">Actualizando, por favor espere.</span><input name="Proceder" id="boton" type="submit" value="Proceder"></p>
		</td>
	</tr>
</table>
<p><p>
<p></form>
<p align="center">&nbsp;</p>
</body>
</html>