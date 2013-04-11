<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
$accion = '';
if( (file_exists('../conf.inc.php') == false) && (file_exists('../instalar.php') == true) ) { $accion = 'Instalaci&oacute;n'; }
if( (file_exists('../conf.inc.php') == true) && (file_exists('../instalar.php') == false) ) {
	require_once('../conf.inc.php');
	if (isset($_POST['loginadmin']) && isset($_POST['contraadmin'])) {
		$logadm = $_POST['loginadmin'];
		$conadm = $_POST['contraadmin'];
	}else{
		require_once('../funciones/acceso.php');
		exit();
	}
	if( ( ($logadm != USUARIOADMIN) || ($conadm != CONTRASADMIN) ) && (!isset($_POST['titulo'])) ) {
		echo('No tiene acceso al panel de administraci&oacute;n.');
		exit();
  }
	$accion = 'Administraci&oacute;n';
}
if($accion != '') {
	function escribeconfinc($mostrarerr) {
		$fp = fopen('../conf.inc.php', 'w+');
		$cadena = "<?php
ini_set('display_errors', " . $mostrarerr . ");
ini_set('max_execution_time', '" . $_POST['tiempoerror'] . "');
ini_set('memory_limit', '" . $_POST['memoria'] . "');
define ('MOSTRARERR', '" . $mostrarerr . "');
define ('TIEMPOMAX', '" . $_POST['tiempoerror'] . "');
define ('LIMITEMEM', '" . $_POST['memoria'] . "');
define ('TITULO', '" . $_POST['titulo'] . "');
define ('BD_HOST', '" . $_POST['host'] . "');
define ('BD_USERNAME', '" . $_POST['usuario'] . "');
define ('BD_PASSWORD', '" . $_POST['password'] . "');
define ('BD_DATABASE1', '" . $_POST['BD1'] . "');
define ('COLORCABECERA', '" . $_POST['colorcabecera'] . "');
define ('COLORCABECERABARRA', '" . $_POST['colorcabecerabarra'] . "');
define ('COLORCABECERATEXTO', '" . $_POST['colorcabeceratexto'] . "');
define ('COLORCLAROCELDA', '" . $_POST['colorclaroceldas'] . "');
define ('COLOROSCUROCELDA', '" . $_POST['coloroscuroceldas'] . "');
define ('PLANTILLA', '" . $_POST['plantilla'] . "');
define ('MOSTRARPRECIO', '" . @$_POST['avprecios'] . "');
define ('USUARIOADMIN', '" . $_POST['loginadmin'] . "');
define ('CONTRASADMIN', '" . $_POST['contraadmin'] . "');
define ('CLIENTENUMAUT', '" . @$_POST['clinumaut'] . "');
?>";
		if(fwrite($fp, $cadena) != true) {
			echo('No se ha podido escribir en el archivo. Compruebe los permisos de escritura en su servidor.');
			exit();
		}     
		fclose($fp);
	}
	function escribesql() {
		$conexioSQL = mysql_connect($_POST['host'], $_POST['usuario'], $_POST['password']);
		$seleccioBD = mysql_select_db($_POST['BD1'], $conexioSQL);
		if(mysql_errno() == 1049) {
			echo('<b>La base de datos: </b>' . $_POST['BD1'] . '<b> no existe.<br>Se proceder&aacute; a crearla.</b><br>');
			mysql_query('CREATE DATABASE ' . $_POST['BD1'], $conexioSQL);
			$seleccioBD = mysql_select_db($_POST['BD1'], $conexioSQL);
		}
		if(mysql_errno() != 0) {
			echo('Ha ocurrido un error al conectar con la base de datos indicada.<br>Compruebe el mensaje de error y solventelo antes de continuar.<br><br>');
			require('mostrarerr.php');
			exit();
		}
		$gestor = fopen('../BBDD/factusolweb.sql', 'r');
		$contenido = fread($gestor, filesize('../BBDD/factusolweb.sql'));
		fclose($gestor);
		$inicio = 0;
		$fin = 0;
		if(strpos($contenido, ";\r\n", $inicio) == false) {
			$separador = ";\n";
		}else{
			$separador = ";\r\n";
		}
		while(strpos($contenido, $separador, $inicio)) {
			$fin = strpos($contenido, $separador, $inicio) + 1;
			$sql = substr($contenido, $inicio, $fin - $inicio);
			mysql_query($sql, $conexioSQL);
			$inicio = $fin;
		}
		if(mysql_errno() != 0) {
			require('mostrarerr.php');
			exit();
		}
	}
	function escribearchivos($accion) {
		if(isset($_POST['mostrarerror'])) { $mostrarerr = 'true'; }else{ $mostrarerr = 'false'; }
		$plantilla = 'estandar';
		escribeconfinc($mostrarerr);
		if($accion == 'Administraci&oacute;n') {
			$conexioSQL = mysql_connect($_POST['host'], $_POST['usuario'], $_POST['password']);
			$seleccioBD = mysql_select_db($_POST['BD1'], $conexioSQL);
			$sql = "UPDATE PAGINAS SET CONTPAG = '" . $_POST['postEditor'] . "' WHERE NOMBPAG = 'inicio'";
			mysql_query($sql, $conexioSQL);
		}else{
			escribesql();
		}
		echo('<p>&nbsp;</p>');
		echo('<p>&nbsp;</p>');
		echo('<table width="70%" border="0" align="center" cellpadding="0" cellspacing="0">');
		echo('<tr>');
		echo('<td align="center" valign="middle"><p>La instalaci&oacute;n se ha completado, si han aparecido errores solventelos y vuelva a ejecutar esta instalaci&oacute;n.</p>');
		if($accion == 'Instalaci&oacute;n') {
			echo('<p color="#FF0000">Si todo fu&eacute; correcto, por favor, elimine el fichero: "instalar.php"</p></td>');
		}
		echo('</tr>');
		echo('</table>');
		exit();
	}
	if(isset($_POST['titulo'])) {
		escribearchivos($accion);
	}else{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Instalaci&oacute;n Factusol Web</title>
<link href="../plantillas/estandar/estilos/estilo.css" rel="stylesheet" type="text/css">
<script src="js.js" type="text/javascript"></script>
<style type="text/css">
<!--
td { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; }
.Estilo3 { font-size: 16px; font-weight: bold; }
.Estilo4 { font-size: 24px; font-weight: bold; }
.Estilo5 { color: #0000FF; }
-->
</style>
</head>
<body>

<table width="955" border="0" cellspacing="5" cellpadding="0" align="center">
	<tr>
		<td><h1 align="left"><img src="../plantillas/estandar/imagenes/logoEmpresa.gif" alt=""></h1></td>
		<td id="paleta" style="visibility:hidden">
			<div id="pickerPanel" class="dragPanel">
				<h4 id="pickerHandle"><p align="center" style="font-size:9px">Pulsar y arrastrar para mover</p></h4>
				<div id="pickerDiv">
					<img id="pickerbg" src="../img/pickerbg.png" alt="">
					<div id="selector"><img src="../img/select.gif"></div>
				</div>
				<div id="hueBg">
					<div id="hueThumb"><img src="../img/hline.png"></div>
				</div>
				<div id="pickervaldiv">
					<form name="pickerform" onSubmit="return pickerUpdate()">
					<br>
					R <input name="pickerrval" id="pickerrval" type="text" value="0" size="3" maxlength="3">
					H <input name="pickerhval" id="pickerhval" type="text" value="0" size="3" maxlength="3">
					<br>
					G <input name="pickergval" id="pickergval" type="text" value="0" size="3" maxlength="3">
					S <input name="pickergsal" id="pickersval" type="text" value="0" size="3" maxlength="3">
					<br>
					B <input name="pickerbval" id="pickerbval" type="text" value="0" size="3" maxlength="3">
					V <input name="pickervval" id="pickervval" type="text" value="0" size="3" maxlength="3">
					<br>
					<br>
					# <input name="pickerhexval" id="pickerhexval" type="text" value="0" size="6" maxlength="6">
					<br>
					</form>
				</div>
				<div id="pickerSwatch">&nbsp;</div>
			</div>
		</td>
		<td valign="bottom">
			<div align="right">
				<p class="Estilo3"><?=$accion?> de </p>
				<p class="Estilo4">FactuSOL <span class="Estilo5">Web</span></p>	
			</div>
		</td>
	</tr>
	<tr><td colspan="3"><hr><br /></td></tr>
</table>
<form id="form1" name="instala" method="post" onSubmit="return mandar()">
<table width="955" height="282" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="130" valign="top">
<!-- Menú -->
<table width="130" height="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td id="menuServidor" style="background-color:#EEEEFF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Servidor');">Servidor</span></b></p>
		</td>
	</tr>
	<tr>
		<td id="menuAdministracion" style="background-color:#F5F5FF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Administracion');">Administraci&oacute;n</span></b></p>
		</td>
	</tr>
	<tr>
		<td id="menuAspecto" style="background-color:#F5F5FF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Aspecto');">Aspecto</span></b></p>
		</td>
	</tr>
	<tr>
		<td id="menuAvanzadas" style="background-color:#F5F5FF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Avanzadas');">Avanzadas</span></b></p>
		</td>
	</tr>
	<tr>
		<td id="menuEditar" style="background-color:#F5F5FF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Editar');">Editar</span></b></p>
		</td>
	</tr>
	<tr>
		<td id="menuInstalar" style="background-color:#F5F5FF">
			<p><b><span style="cursor:pointer" onClick="Alternar('Instalar');">Instalar</span></b></p>
		</td>
	</tr>
</table>
<!-- Fin Menú -->
		</td>
		<td width="600" valign="top">
<!-- Menú Servidor -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Servidor" bgcolor="#EEEEFF">
	<tr><th valign="top" colspan="4"><br><p>Direcci&oacute;n de la base de datos MySQL</p><br></th></tr>
	<tr>
		<td valign="top" title="IP del servidor MySQL"><p>Host:</p></td>
		<td valign="top" title="IP del servidor MySQL"><input name="host" type="text" id="host" value="" onChange="estado('Servidor');"></td>
		<td valign="top"><p><b style="cursor:pointer" onClick="compUsuario();"><u>Probar</u></b></p></td>
		<td valign="top"><p id="Prueba"></p></td>
	</tr>
	<tr><td valign="top" colspan="4"><hr></td></tr>
	<tr><th valign="top" colspan="4"><p>Acceso a la base de datos</p><br></th></tr>
	<tr>
		<td valign="top"><p>Usuario:</p></td>
		<td valign="top"><input name="usuario" type="text" id="usuario" onChange="estado('Servidor');"></td>
		<td valign="top"><p>Password:</p></td>
		<td valign="top"><input name="password" type="password" id="password" onChange="estado('Servidor');"></td>
	</tr>
	<tr><td valign="top" colspan="4"><hr></td></tr>
	<tr><th valign="top" colspan="4"><p>Nombre de la base de datos</p><br></th></tr>
	<tr>
		<td valign="top"><p>FactuSol Web:</p></td>
		<td valign="top"><input name="BD1" type="text" id="BD1" value="" onChange="estado('Servidor');"></td>
	</tr>
</table>
<!-- Fin Menú Servidor -->
<!-- Menú Administración -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Administracion" bgcolor="#EEEEFF" style="display:none">
	<tr><th valign="top" colspan="4"><br><p>Acceso al panel de administraci&oacute;n</p><br></th></tr>
	<tr>
		<td valign="top"><p>Usuario:</p></td>
		<td valign="top"><input name="loginadmin" type="text" id="user" onChange="estado('Administracion');" title="Usuario para la zona de abrir/cerrar m&oacute;dulos y cambiar configuraciones."></td>
		<td valign="top"><p>Contrase&ntilde;a:</p></td>
		<td height="100%" valign="top"><input type="text" name="contraadmin" id="pass" onChange="estado('Administracion');" title="Contrase&ntilde;a de la administraci&oacute;n."></td>
	</tr>
</table>
<!-- Fin Menú Administración -->
<!-- Menú Aspecto -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Aspecto" bgcolor="#EEEEFF" style="display:none">
	<tr><th valign="top" colspan="4"><br><p>Cambios globales de la Web</p></th></tr>
	<tr>
		<td valign="top"><p>T&iacute;tulo:</p></td>
		<td valign="top"><input name="titulo" type="text" id="titulo" value="FactuSOL Web" onChange="estado('Aspecto');" title="T&iacute;tulo de la pagina."></td>
		<td valign="top"><p>Plantilla:</p></td>
		<td valign="top">
			<select name="plantilla" id="plantilla" title="Cambia el aspecto gr&aacute;fico de la Web">
				<option value="estandar">Est&aacute;ndar</option>
			</select>
		</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr></td></tr>
	<tr><th valign="top" colspan="4"><p>Colores de la cabecera</p></th></tr>
	<tr>
		<td valign="top"><p>Cabecera:</p></td>
		<td valign="top">#<input type="text" name="colorcabecera" value="0000FF" size="7" id="colorcabecera" onFocus="ddcolorposter.echocolor(this, 'colorbox1')" onChange="estado('Aspecto');" title="Color del fondo de la cabecera de las p&aacute;ginas (usar la paleta de colores)"> <span id="colorbox1" class="colorbox">____</span></td>
	</tr>
	<tr>
		<td valign="top"><p>Barra:</p></td>
		<td valign="top">#<input type="text" name="colorcabecerabarra" value="00FF00" size="7" id="colorcabecerabarra" onFocus="ddcolorposter.echocolor(this, 'colorbox2')" onChange="estado('Aspecto');" title="Color de la barra de la cabecera (usar la paleta de colores)"> <span id="colorbox2" class="colorbox">____</span></td>
		<td valign="top"><p>Texto:</p></td>
		<td valign="top">#<input type="text" name="colorcabeceratexto" value="0000FF" size="7" id="colorcabeceratexto" onFocus="ddcolorposter.echocolor(this, 'colorbox3')" onChange="estado('Aspecto');" title="Color del texto del menu de la barra de la cabecera (usar la paleta de colores inferior)"> <span id="colorbox3" class="colorbox">____</span></td>
	</tr>
	<tr><td valign="top" colspan="4"><hr></td></tr>
	<tr><th valign="top" colspan="4"><p>Colores de las celdas</p></th></tr>
	<tr>
		<td valign="top"><p>Oscuras:</p></td>
		<td valign="top">#<input type="text" name="coloroscuroceldas" value="EEEEEE" size="7" id="coloroscuroceldas" onFocus="ddcolorposter.echocolor(this, 'colorbox4')" onChange="estado('Aspecto');" title="Color del fondo alternado oscuro de las celdas de la tabla de productos (usar la paleta de colores)"> <span id="colorbox4" class="colorbox">____</span></td>
		<td valign="top"><p>Claras:</p></td>
		<td valign="top" height="100%">#<input type="text" name="colorclaroceldas" value="FFFFFF" size="7" id="colorclaroceldas" onFocus="ddcolorposter.echocolor(this, 'colorbox5')" onChange="estado('Aspecto');" title="Color del fondo alternado claro de las celdas de la tabla de productos (usar la paleta de colores inferior)"> <span id="colorbox5" class="colorbox">____</span></td>
	</tr>
</table>
<!-- Fin Menú Aspecto -->
<!-- Menú Avanzadas -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Avanzadas" bgcolor="#EEEEFF" style="display:none">
	<tr><th valign="top" colspan="4"><br><p>Opciones avanzadas de configuraci&oacute;n del servidor</p><br></th></tr>
	<tr>
		<td valign="top"><p>Tiempo para error:</p></td>
		<td valign="top"><input type="text" name="tiempoerror" value="<?php echo(ini_get('max_execution_time')); ?>" id="tiempoerror" size="4" onChange="estado('Avanzadas');" title="Segundos m&aacute;ximos que esperar&aacute; el servidor antes de devolver un error por demora en ejecuci&oacute;n."></td>
		<td valign="top"><p>Mostrar errores:</p></td>
		<td valign="top"><input type="checkbox" name="mostrarerror" <?php if (ini_get('display_errors') == true) { echo ('checked'); } ?> id="mostrarerror" style="background-color:#EEEEFF" title="Muestra los errores en las p&aacute;ginas, recomendado s&oacute;lo para pruebas."></td>
	</tr>
	<tr>
		<td valign="top"><p>Memoria m&aacute;xima:</p></td>
		<td valign="top"><input type="text" name="memoria" value="<?php echo(ini_get('memory_limit')); ?>" id="memoria" size="4" onChange="estado('Avanzadas');" title="Memoria m&aacute;xima empleada por el servidor. Aumentar solamente si el tama&ntilde;o del fichero .sql es mayor que el expuesto aqu&iacute;."></td>
	</tr>
	<tr><th valign="top" colspan="4"><br><p>Alta de clientes.</p><br></th></tr>
	<tr>
		<td valign="top"><p>Permitir iniciar sesi&oacute;n antes de validar en FactuSol:</p></td>
		<td valign="top"><input type="checkbox" name="clinumaut" value="si"<?php if(@CLIENTENUMAUT == 'si') { echo(' checked');} ?> id="clinumaut" size="4" onChange="estado('Avanzadas');" title="Permite (a los clientes) realizar pedidos sin necesidad de ser registrados previamente en FactuSol."></td>
	</tr>
	<tr><th valign="top" colspan="4"><br><p>Mostrar precios</p><br></th></tr>
	<tr>
		<td valign="top"><p>Mostrar precios a los clientes no registrados:</p></td>
		<td valign="top" height="100%"><input type="checkbox" name="avprecios" value="si"<?php if(@MOSTRARPRECIO == 'si') { echo(' checked');} ?> id="avprecios" size="4" onChange="estado('Avanzadas');" title="Oculta el precio a los clientes no registrados."></td>
	</tr>
</table>
<!-- Fin Menú Avanzadas -->
<!-- Menú Editar -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Editar" bgcolor="#EEEEFF" style="display:none">
	<tr>
		<th valign="top">
			<p>Editor para la p&aacute;gina principal.</p>
		</th>
	</tr>
	<tr>
		<td valign="top" id="Editor">
<?php
if($accion == 'Administraci&oacute;n') {
	include_once "../ckeditor/ckeditor.php";
	$edValor = '';
	$sql = "SELECT * FROM PAGINAS WHERE NOMBPAG = 'inicio'";
	$conexioSQL = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	$seleccioBD = mysql_select_db(BD_DATABASE1, $conexioSQL);
	$rs = mysql_query($sql, $conexioSQL);
	@$lista = mysql_fetch_assoc($rs);
	$edValor = $lista['CONTPAG'];
	$CKEditor = new CKEditor();
	$CKEditor->basePath = '../ckeditor/';
	$CKEditor->editor("postEditor", $edValor);
}else{
	echo('Esta función estará disponible después de la instalación.');
}
?>
		</td>
	</tr>
</table>
<!-- Fin Menú Editar -->
<!-- Menú Instalar -->
<table width="600" height="100%" border="0" cellspacing="0" cellpadding="5" id="Instalar" bgcolor="#EEEEFF" style="display:none">
	<tr><th valign="top"><br><p id="textoInstalar1"></p></th></tr>
	<tr><td colspan="4" valign="top"><hr></td></tr>
	<tr><th valign="top"><p id="textoInstalar2"></p></th></tr>
	<tr>
		<th valign="top">
			<br><br><br>
			<input type="submit" name="Submit" id="boton" value="Instalar" disabled="disabled">
		</th>
	</tr>
	<tr>
		<td valign="top" height="100%">
			<div align="left" id="texto" style="visibility:hidden">
				<p style="font-size:24px; text-decoration:blink">Instalando...</p>
			</div>
		</td>
	</tr>
</table>
<!-- Fin Menú Instalar -->
		</td>
		<td width="25">&nbsp;</td>
		<td width="200" valign="top">
<!-- Cuadro Estado -->
<table width="200" border="0" cellspacing="0" cellpadding="10" bgcolor="#FFF5F5">
	<tr>
		<th colspan="2"><br />
		<p>Datos suministrados </p>
		<br /></th>
	</tr>
	<tr>
		<td><p>Servidor:</p></td>
		<td><img src="../plantillas/estandar/imagenes/nor_ant.gif" id="imgServidor" /></td>
	</tr>
	<tr>
		<td><p>Administraci&oacute;n:</p></td>
		<td><img src="../plantillas/estandar/imagenes/nor_ant.gif" id="imgAdministracion" /></td>
	</tr>
	<tr>
		<td><p>Aspecto:</p></td>
		<td><img src="../plantillas/estandar/imagenes/aceptar.gif" id="imgAspecto" /></td>
	</tr>
	<tr>
		<td><p>Avanzadas:</p></td>
		<td><img src="../plantillas/estandar/imagenes/aceptar.gif" id="imgAvanzadas" /></td>
	</tr>
	<tr>
		<th colspan="2"><br /><p id="estadoDatos">Faltan datos</p><br /></th>
	</tr>
</table>
<!-- Fin Cuadro Estado -->
		</td>
	</tr>
</table>
</form>
<link rel="stylesheet" type="text/css" href="../css/screen.css">
<script type="text/javascript" src="../js/ddcolorposter.js"></script>
<script type="text/javascript" src="../js/YAHOO.js" ></script>
<script type="text/javascript2" src="../js/log.js" ></script>
<script type="text/javascript" src="../js/color.js" ></script>
<script type="text/javascript" src="../js/event.js" ></script>
<script type="text/javascript" src="../js/dom.js" ></script>
<script type="text/javascript" src="../js/animation.js" ></script>
<script type="text/javascript" src="../js/dragdrop.js" ></script>
<script type="text/javascript" src="../js/slider.js" ></script>
<script type="text/javascript">
	var hue;
	var picker;
	//var gLogger;
	var dd1, dd2;
	var r, g, b;

	function init() {
		if (typeof(ygLogger) != "undefined")
			ygLogger.init(document.getElementById("logDiv"));
		pickerInit();
		ddcolorposter.fillcolorbox("colorcabecera", "colorbox1")
		ddcolorposter.fillcolorbox("colorcabecerabarra", "colorbox2")
		ddcolorposter.fillcolorbox("colorcabeceratexto", "colorbox3")
		ddcolorposter.fillcolorbox("coloroscuroceldas", "colorbox4")
		ddcolorposter.fillcolorbox("colorclaroceldas", "colorbox5")
    }

    // Picker ---------------------------------------------------------

    function pickerInit() {
		hue = YAHOO.widget.Slider.getVertSlider("hueBg", "hueThumb", 0, 180);
		hue.onChange = function(newVal) { hueUpdate(newVal); };
		picker = YAHOO.widget.Slider.getSliderRegion("pickerDiv", "selector",
				0, 180, 0, 180);
		picker.onChange = function(newX, newY) { pickerUpdate(newX, newY); };
		hueUpdate();
		dd1 = new YAHOO.util.DD("pickerPanel");
		dd1.setHandleElId("pickerHandle");
		dd1.endDrag = function(e) {
			// picker.thumb.resetConstraints();
			// hue.thumb.resetConstraints();
        };
	}

	executeonload(init);

	function pickerUpdate(newX, newY) {
		pickerSwatchUpdate();
	}

	function hueUpdate(newVal) {
		var h = (180 - hue.getValue()) / 180;
		if (h == 1) { h = 0; }
		var a = YAHOO.util.Color.hsv2rgb( h, 1, 1);
		document.getElementById("pickerDiv").style.backgroundColor =
			"rgb(" + a[0] + ", " + a[1] + ", " + a[2] + ")";
		pickerSwatchUpdate();
	}

	function pickerSwatchUpdate() {
		var h = (180 - hue.getValue());
		if (h == 180) { h = 0; }
		document.getElementById("pickerhval").value = (h*2);
		h = h / 180;
		var s = picker.getXValue() / 180;
		document.getElementById("pickersval").value = Math.round(s * 100);
		var v = (180 - picker.getYValue()) / 180;
		document.getElementById("pickervval").value = Math.round(v * 100);
		var a = YAHOO.util.Color.hsv2rgb( h, s, v );
		document.getElementById("pickerSwatch").style.backgroundColor =
			"rgb(" + a[0] + ", " + a[1] + ", " + a[2] + ")";
		document.getElementById("pickerrval").value = a[0];
		document.getElementById("pickergval").value = a[1];
		document.getElementById("pickerbval").value = a[2];
		var hexvalue = document.getElementById("pickerhexval").value =
			YAHOO.util.Color.rgb2hex(a[0], a[1], a[2]);
			ddcolorposter.initialize(a[0], a[1], a[2], hexvalue)
	}
</script>
<!--[if gte IE 5.5000]>
<script type="text/javascript">
function correctPNG() // correctly handle PNG transparency in Win IE 5.5 or higher.
   {
   for(var i=0; i<document.images.length; i++)
      {
	  var img = document.images[i]
	  var imgName = img.src.toUpperCase()
	  if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
	     {
		 var imgID = (img.id) ? "id='" + img.id + "' " : ""
		 var imgClass = (img.className) ? "class='" + img.className + "' " : ""
		 var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
		 var imgStyle = "display:inline-block;" + img.style.cssText
		 if (img.align == "left") imgStyle = "float:left;" + imgStyle
		 if (img.align == "right") imgStyle = "float:right;" + imgStyle
		 if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
		 var strNewHTML = "<span " + imgID + imgClass + imgTitle
		 + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
	     + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
		 + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
		 img.outerHTML = strNewHTML
		 i = i-1
	     }
      }
   }
YAHOO.util.Event.addListener(window, "load", correctPNG);
</script>
<![endif]-->
<script language="javascript" type="text/javascript">
	document.getElementById('textoInstalar1').innerHTML = 'Una vez configurados todos los par&aacute;metros, proceda a la instalaci&oacute;n de los m&oacute;dulos pulsando sobre el bot&oacute;n &ldquo;Instalar&rdquo;';
</script>
<?php if($accion == 'Administraci&oacute;n') { ?>
<script language="javascript" type="text/javascript">
	document.getElementById('textoInstalar2').innerHTML = 'En caso de surgir alg&uacute;n error solvetalo y vuelva a realizar la instalaci&oacute;n.';
	document.instala.host.value = '<?=BD_HOST?>';
	document.instala.usuario.value = '<?=BD_USERNAME?>';
	document.instala.password.value = '<?=BD_PASSWORD?>';
	document.instala.BD1.value = '<?=BD_DATABASE1?>';
	estado('Servidor');
	document.instala.loginadmin.value = '<?=USUARIOADMIN?>';
	document.instala.contraadmin.value = '<?=CONTRASADMIN?>';
	estado('Administracion');
	document.instala.titulo.value = '<?=TITULO?>';
	document.instala.plantilla.options[0].selected = true;
	document.instala.colorcabecera.value = '<?=COLORCABECERA?>';
	document.instala.colorcabecerabarra.value = '<?=COLORCABECERABARRA?>';
	document.instala.colorcabeceratexto.value = '<?=COLORCABECERATEXTO?>';
	document.instala.coloroscuroceldas.value = '<?=COLOROSCUROCELDA?>';
	document.instala.colorclaroceldas.value = '<?=COLORCLAROCELDA?>';
	estado('Aspecto');
</script>
<?php }elseif($accion != '') { ?>
	<script language="javascript" type="text/javascript">
	document.getElementById('textoInstalar2').innerHTML = 'S&iacute; despu&eacute;s de la configuraci&oacute;n no se ha mostrado ning&uacute;n error, proceda a la eliminaci&oacute;n del fichero instalar.php<br>En caso contrario solveta los problemas y vuelva a realizar la instalaci&oacute;n.';
	</script>
<?php } ?>
</body>
</html>
<?php
	}
}else{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Instalaci&oacute;n Factusol Web</title>
<link href="../plantillas/estandar/estilos/estilo.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo3 {font-size: 16px; font-weight: bold; }
.Estilo4 {font-size: 24px; font-weight: bold; }
.Estilo5 {color: #0000FF}
-->
</style>
</head>
<body>
<table width="955" border="0" cellspacing="5" cellpadding="0" align="center">
	<tr>
		<td><h1 align="left"><img src="../plantillas/estandar/imagenes/logosmgacompleto.gif" alt=""></h1></td>
		<td valign="bottom">
			<div align="right">
				<p class="Estilo3">Administraci&oacute;n de </p>
				<p class="Estilo4">FactuSol <span class="Estilo5">Web</span></p>	
			</div>
		</td>
	</tr>
</table>
<table width="955" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr><td><hr><br></td></tr>
	<tr>
		<td align="center">
			<h3>Existe una instalaci&oacute;n anterior.<br>
				<br>
				Para administrar los m&oacute;dulos, elimine el fichero instalar.php<br>
				<br>
				Para volver a instalar los m&oacute;dulos, elimine el fichero conf.inc.php</h3>
		</td>
	</tr>
</table>
</body>
</html>
<?php
}
?>