
<table width="100%" border="0" cellspacing="0" cellpadding="3">
<?php
$alternss = false;
if ( isset($_GET['seccion']) && ($_GET['seccion'] != '-1') ) { $alternss = true; }
$seccion = @$_GET['seccion'];
if ($datos['UFSCFG'] != 0) { $valufs = ''; }else{ $valufs = ' style="display:none"'; }
echo('<tr' . $valufs . '>');
echo('<td>' . $datos['NFSCFG'] . ': </td>');
echo('<td>');
if($alternss == false) { $seccion = secciones(-1); }else{ secciones($seccion); }
echo('</td>');
echo('</tr>');
if ($datos['UFFCFG'] !=0 ) { $valuff = ''; }else{ $valuff = ' style="display:none"'; }
echo('<tr' . $valuff . '>');
echo('<td>' . $datos['NFFCFG'] . ': </td>');
echo('<td>');
if($alternss == false) { $familia = familias('-1', @$_GET['familia']); }else{ $familia = familias($seccion, @$_GET['familia']); }
echo('</td>');
echo('</tr>');
?>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr>
		<td>C&oacute;digo:</td>
		<td><input type="text" name="codigo" size="26" maxlength="150"></td>
	</tr>
	<tr>
		<td>Descripci&oacute;n:</td>
		<td><input type="text" name="descripcion" size="26" maxlength="150"></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="image" name="imageField" src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonbuscar.png" border="0" alt="Buscar" style="border:none"></td>
	</tr>
</table>