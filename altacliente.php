<?php
session_start();
session_unset();
session_destroy();
require_once('top.php');
?>

<?php
require_once('func.php');
function fpago() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_FPA "; 
	$rs = mysql_query($ssql, $conn);
	if (mysql_num_rows($rs) != 0) {
		echo ('<select name="FPACLI">');
		while ($datos = mysql_fetch_array($rs)) {
			echo('<option name="' . $datos['CODFPA'] . '" value="' . $datos['CODFPA'] . '">' . $datos['CODFPA'] . ' - ' . $datos['DESFPA'] . '</option>');
		}
		echo('</select>');
	}else{
		echo('No disponible');
	}
}
//Compruebo que el modulo está activo
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT * FROM F_CFG '; 
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs);
if ($datos['PAOCFG'] == 1) {
	if (isset($_GET['error'])) { echo('<p>' . $_GET['error'] . '</p>'); }
?>
	<br />
	<h4 align="center">Registro nuevo usuario</h4>
	<form name="form1" action="confaltacliente.php" method="post" onsubmit="return verifForm2(this)" class="menucolorfondo">
	<p>
	<table align="center" border="1" cellspacing="0" cellpadding="1" bgcolor="#FDFDFF">
	<tr><td align="right" width="180">Usuario Web:</td><td><input name="CUWCLI" type="text" size="50" maxlength="50" />*</td></tr>
	<tr><td align="right">Password:</td><td><input name="CAWCLI" type="text" size="15" maxlength="15" />*</td></tr>
<?php
	//cojo los campos necesarios
	if ($datos['SNOMCFG'] == 1) { ?>
	<tr><td align="right">Nombre:</td><td><input name="NOFCLI" type="text" size="50" maxlength="50" />*</td></tr>
<?php }
	if ($datos['SDOMCFG'] == 1) { ?>
	<tr><td align="right">Domicilio:</td><td><input name="DOMCLI" type="text" size="50" maxlength="50" />*</td></tr>
<?php }
	if ($datos['SCPOCFG'] == 1) { ?>
	<tr>
		<td align="right">C&oacute;digo Postal:</td><td><input name="CPOCLI" type="text" size="5" maxlength="5" />*</td></tr>
<?php }
	if ($datos['SPOBCFG'] == 1) { ?>
	<tr><td align="right">Poblaci&oacute;n:</td><td><input name="POBCLI" type="text" size="30" maxlength="30" />*</td></tr>
<?php }
	if ($datos['SPROCFG'] == 1) { ?>
	<tr><td align="right">Provincia:</td><td><input name="PROCLI" type="text" size="20" maxlength="20" />*</td></tr>
<?php }
	if ($datos['SNIFCFG'] == 1) { ?>
	<tr><td align="right">NIF:</td><td><input name="NIFCLI" type="text" size="12" maxlength="12" />*</td></tr>
<?php }
	if ($datos['STELCFG'] == 1) { ?>
	<tr><td align="right">Tel&eacute;fono Fijo:</td><td><input name="STELCFG" type="text" size="40" maxlength="40" />*</td></tr>
<?php }
	if ($datos['SFAXCFG'] == 1) { ?>
	<tr><td align="right">FAX:</td><td><input name="SFAXCFG" type="text" size="40" maxlength="40" /></td></tr>
<?php }
	if ($datos['SMOVCFG'] == 1) { ?>
	<tr><td align="right">Movil:</td><td><input name="SMOVCFG" type="text" size="40" maxlength="40" /></td></tr>
<?php }
	if ($datos['SEMACFG'] == 1) { ?>
	<tr><td align="right">E-mail:</td><td><input name="SEMACFG" type="text" size="50" maxlength="50" />*</td></tr>
<?php }
	if ($datos['SPCOCFG'] == 1) { ?>
	<tr><td align="right">Persona de contacto:</td><td><input name="SPCOCFG" type="text" size="50" maxlength="50" /></td></tr>
<?php }
	if ($datos['SNENCFG'] == 1) { ?>
	<tr><td align="right">Nombre para entrega:</td><td><input name="SNENCFG" type="text" size="50" maxlength="50" /></td></tr>
<?php }
	if ($datos['SDENCFG'] == 1) { ?>
	<tr><td align="right">Domicilio para entrega:</td><td><input name="SDENCFG" type="text" size="50" maxlength="100" /></td></tr>
<?php }
	if ($datos['SCPECFG'] == 1) { ?>
	<tr><td align="right">C&oacute;digo postal entrega:</td><td><input name="SCPECFG" type="text" size="5" maxlength="5" /></td></tr>
<?php }
	if ($datos['SPOECFG'] == 1) { ?>
	<tr>
		<td align="right">Poblaci&oacute;n entrega:</td><td><input name="SPOECFG" type="text" size="30" maxlength="30" /></td></tr>
<?php }
	if ($datos['SPRECFG'] == 1) { ?>
	<tr>
		<td align="right">Provincia entrega:</td><td><input name="SPRECFG" type="text" size="20" maxlength="20" /></td></tr>
<?php }
	if ($datos['SPCECFG'] == 1) { ?>
	<tr><td align="right">Persona de contacto entrega:</td><td><input name="SPCECFG" type="text" size="30" maxlength="30" /></td></tr>
<?php }
	if ($datos['STCECFG'] == 1) { ?>
	<tr><td align="right">Tel&eacute;fono de contacto entrega:</td><td><input name="STCECFG" type="text" size="40" maxlength="40" /></td></tr>
<?php }
	if ($datos['SFPACFG'] == 1) { ?>
	<tr><td align="right">Forma de pago:</td><td>
<?php fpago(); ?>
	</td></tr>
<?php }
	if (@$datos['SDTCCFG'] == 1) { ?>
	<tr><td align="right">Datos Tarj. cr&eacute;dito:</td><td>
	<select name="tipo">
		<option value="VISA">Visa
		<option value="MASTERCARD">MasterCard
		<option value="AMEX">American Express
	</select><br>
	Numero: <input type="text" Name="numtarjeta" MAXLENGTH="16" SIZE="16"> Cod. Seguridad: <input type="text" Name="codseg" MAXLENGTH="3" SIZE="3"> <br>
	Fecha de Caducidad: Mes&nbsp;
	<select  name="mes">
		<option value="01">01
		<option value="02">02
		<option value="03">03
		<option value="04">04
		<option value="05">05
		<option value="06">06
		<option value="07">07
		<option value="08">08
		<option value="09">09
		<option value="10">10
		<option value="11">11
		<option value="12">12
	</select>
	&nbsp;A&ntilde;o&nbsp;
	<?php $year = date("Y"); ?>
	<select name="anio">
		<option value="<?=$year?>"> <?=$year?>
		<option value="<?=$year + 1?>"> <?=$year + 1?>
		<option value="<?=$year + 2?>"> <?=$year + 2?>
		<option value="<?=$year + 3?>"> <?=$year + 3?>
		<option value="<?=$year + 4?>"> <?=$year + 4?>
		<option value="<?=$year + 5?>"> <?=$year + 5?>
		<option value="<?=$year + 6?>"> <?=$year + 6?>
		<option value="<?=$year + 7?>"> <?=$year + 7?>
		<option value="<?=$year + 8?>"> <?=$year + 8?>
		<option value="<?=$year + 9?>"> <?=$year + 9?>
		<option value="<?=$year + 10?>"> <?=$year + 10?>
	</select>
	</td></tr>
<?php }
	if (@$datos['SDCBCFG'] == 1) { ?>
	<tr><td align="right">Datos Cta. bancaria:</td><td>
	<INPUT TYPE="text" NAME="banco" MAXLENGTH="4" SIZE="4">
	<INPUT TYPE="text" NAME="sucursal" MAXLENGTH="4" SIZE="4">
	<INPUT TYPE="text" NAME="dc" MAXLENGTH="2" SIZE="2">
	<INPUT TYPE="text" NAME="cuenta" MAXLENGTH="10" SIZE="10">
	</td></tr>
<?php } ?>
	</table>
	</p>
	<p><input type="submit" name="Terminado" value="Alta Cliente" /></p>
	</form>
<?php }else{ ?>
	No se permiten altas de clientes
	<script LANGUAGE="JavaScript">
		var pagina="autentifica.php";
		setTimeout ("redireccionar2()", 5000);
	</script>
<?php }
require_once('button.php'); ?>