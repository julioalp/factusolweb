<?
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'usuario') {
	header('Location: autentifica.php');
	exit();
}
require_once('top.php');
?>

<?php
require_once('cmodulos.php');
require_once('menum.php');
//Compruebo si está cerrado el módulo
imodulofact ();
cmodulofact();
include_once('calendario/calendario.php');
?>
<script language="JavaScript" src="calendario/javascripts.js"></script>
<h3 align="center">Rango de Facturas</h3>
<form name="rango" id="rango" method="post" action="lfacturas.php" onSubmit="return verifForm()" class="menucolorfondo">
  <table width="100%" border="0">
    <tr>
      <td><input name="opcion" type="radio" value="10" checked="checked" class="menucolorfondo" /> 10 &Uacute;ltimas</td>
    </tr>
    <tr>
      <td><input name="opcion" type="radio" value="todas" class="menucolorfondo" /> Todas</td>
    </tr>
    <tr>
      <td><input name="opcion" type="radio" value="semana" class="menucolorfondo" /> Esta semana</td>
    </tr>
    <tr>
      <td><input name="opcion" type="radio" value="mes" class="menucolorfondo" /> Este mes</td>
    </tr>
    <tr>
      <td><input name="opcion" type="radio" value="mesant" class="menucolorfondo" /> Mes anterior</td>
    </tr>
    <tr>
      <td><input name="opcion" type="radio" value="fechas" class="menucolorfondo" /> Entre las fechas
<?php escribe_formulario_fecha_vacio('fecha1', 'rango'); ?>
				&nbsp;y&nbsp;
<?php escribe_formulario_fecha_vacio('fecha2', 'rango'); ?>
			</td>
    </tr>
    <tr>
      <td><br /><input type="image" src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonverfacturas.png" style="border:none"></td>
    </tr>
  </table>
</form>
<?php require_once('button.php'); ?>