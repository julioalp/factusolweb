<?php
session_start();
if ($_SESSION['autentificado'] == 'SI' and $_SESSION['tipo_usuario'] == 'usuario') {
	header ('Location: cliente.php');
	exit();
}
require_once('top.php');
?>

<?php require_once('conf.inc.php'); ?>
<div align="center">
	<p>&nbsp;</p>
<?php echo @$_GET['ERROR']; ?>
<br /><br /><br />
<style type="text/css">
div.rounded-box {
	position:relative;
	width: 160px;
	background-color: #E6E6E6;
	margin:10px;
	padding:5px;
}
/*********************
GLOBAL ATTRIBUTES
*********************/
div.top-left-corner, div.bottom-left-corner, div.top-right-corner, div.bottom-right-corner {
	position:absolute; width:20px; height:20px; background-color:#FFFFFF; overflow:hidden;
}
div.top-left-inside, div.bottom-left-inside, div.top-right-inside, div.bottom-right-inside {
	position:relative; font-size:150px; font-family:arial; color:#E6E6E6; line-height: 40px;
}
/*********************
SPECIFIC ATTRIBUTES
*********************/
div.top-left-corner { top:0px; left:0px; }
div.bottom-left-corner {bottom:-1px; left:0px;}
div.top-right-corner {top:0px; right:0px;}
div.bottom-right-corner {bottom: -1px; right:0px;}
div.top-left-inside {left:-8px;}
div.bottom-left-inside {left:-8px; top:-18px;}
div.top-right-inside {left:-25px;}
div.bottom-right-inside {left:-25px; top:-18px;}
div.box-contents { position: relative; padding: 8px; color:#000000; }
</style>
	<div class="rounded-box">
    <div class="top-left-corner"><div class="top-left-inside">&bull;</div></div>
    <div class="bottom-left-corner"><div class="bottom-left-inside">&bull;</div></div>
    <div class="top-right-corner"><div class="top-right-inside">&bull;</div></div>
    <div class="bottom-right-corner"><div class="bottom-right-inside">&bull;</div></div>
    <div class="box-contents">
			<form id="form1" name="form1" method="post" action="session.php" />
				<p>Usuario:<br />
					<input name="usuario" type="text" id="usuario" size="20" /></p><br />
				<p>Password:<br />
					<input name="password" type="password" id="password" size="20" /></p><br /><br />
				<input type="hidden" name="enviado" value="true" />
				<input type="image" style="border:none" src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonacceder.png" align="middle" /><br /><br />
<?php
// Vamos a comprobar si el modulo de alta de clientes está activo.
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT PAOCFG FROM F_CFG '; 
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs);
if ($datos['PAOCFG']== 1) {
	echo('<p align="center"><a href="altacliente.php">Registro nuevo usuario</a></p>');
	mysql_close($conn);    
}
?>
      </form>
		</div> 
<!-- end div.box-contents --> 
	</div> 
<!-- end div.rounded-box -->
</div>
<script language="javascript">
	document.form1.usuario.focus();
</script>
<?php require_once('button.php'); ?>