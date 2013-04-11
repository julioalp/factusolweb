<?php
session_start();
require_once('top.php');
require_once('conf.inc.php');
?>

<?php
if (@$_SESSION['autentificado'] == 'SI' ) {
	if (@$_SESSION['tipo_usuario'] == 'usuario') {
		require_once('menum.php');
	}else{
		require_once('menumage.php');
	}
	define('LOGEADO', 'si');
}else{
	define('LOGEADO', 'no');
}
require_once('cmodulos.php');
imoduloart(); 
cmoduloart();
require_once('func.php');
//escribe un Select con las secciones
function tamimagen($codart) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_ART WHERE CODART='$codart'";
	$rs = mysql_query($ssql, $conn);
	$datos=mysql_fetch_array($rs);
	if($datos['IMGART'] != '' and file_exists('imagenes/' . $datos['IMGART'])) {
		list($ancho, $altura, $tipo, $atr) = getimagesize('imagenes/' . $datos['IMGART']); 
		return ($altura + 150);
	}else{
		return 300;
	}
}
function famsec ($seccion) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if($seccion != '-1') {
		$ssql = "SELECT * FROM F_FAM WHERE SECFAM='$seccion'";
	}else{
		$ssql = 'SELECT * FROM F_FAM';
	}
	$rs = mysql_query($ssql, $conn);
	$cadena = '(';
	while($datos = mysql_fetch_array($rs)) {
		$cadena .= " FAMART='" . $datos['CODFAM'] . "' or";
	}
	$cadena = substr($cadena, 0, strlen($cadena) - 2) . ')';
	return $cadena;
}       
function secciones($valor) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_SEC';
	$rs = mysql_query($ssql, $conn);
	// para mantener la seleccion anterior
	echo('<select name="seccion" onChange="redireccionar2(document.section.seccion.value,document.section.familia.value,\'1\')">');
	echo('<option value="-1">TODOS</option>');
	for($i=0; $i<=(mysql_num_rows($rs) - 1); $i++) {
		$datos = mysql_fetch_array($rs);
		if($i == 0) { $envio = $datos['CODSEC']; }
		if($valor == $datos['CODSEC']) { $selected = 'selected="selected"'; }
		echo('<option value="'. $datos['CODSEC'] . '"' . $selected . '>' . $datos ['DESSEC'] . '</option>');
		$selected = NULL;
	}
	echo('</select>');
	return $valor; 
}
//Escribe un select con las familias de una sección
function familias($seccion, $familia) {
	$envio = '';
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if($seccion != '-1') {
		$ssql = 'SELECT * FROM F_FAM WHERE SECFAM=\'' . $seccion . '\'';
	}else{
		$ssql = 'SELECT * FROM F_FAM';
	}
	$rs = mysql_query($ssql ,$conn);
	echo('<select name="familia" onChange="redireccionar2(document.section.seccion.value,document.section.familia.value,\'0\')">');
	if($familia != '-1') {
		echo('<option value="-1">TODOS</option>');
	}else{
		echo('<option value="-1" selected>TODOS</option>');
	}
	for($i=0; $i<=(mysql_num_rows($rs) - 1); $i++){
		$datos = mysql_fetch_array($rs);
		if($i == 0) { $envio = $datos['CODFAM']; }
		if($datos['CODFAM'] == $familia) { $selected = 'selected="selected"'; }
		echo('<option value="'. $datos['CODFAM'] . '" ' . $selected . '>' . $datos ['DESFAM'] . '</option>');
		$selected = NULL;
	}
	echo('</select>');
	return $envio;
}
function cabecera() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);       
	echo('<table class="taglist" width="100%" cellspacing="1" cellpadding="3">');
	echo('<tr align="center">');
	if($datos['MIMCFG'] == 1) {
		echo('<td width="');
		if(($datos['MANCFG'] - 1) <  49) {
			echo ('49');
		}else{
			echo($datos['MANCFG'] - 1);
		}
		echo('" class="cabecera">Detalles</td>');
	}	//IMAGEN
	if($datos['MCACFG'] == 1) { echo('<td width="91" class="cabecera">Cod. Art&iacute;culo</td>'); }	//codigo articulo
	if($datos['MDECFG'] == 1) { echo('<td class="cabecera">Denominaci&oacute;n</td>'); }	//denominacion
	if( ($datos['MPRCFG'] == 1) && ( (LOGEADO == 'si') || (MOSTRARPRECIO == 'si') ) ) {
		echo('<td width="70" class="cabecera">Precio</td>');
	}		//Precio
	if($datos['MSTCFG'] == 1) { echo('<td width="75" class="cabecera">Stock</td>'); }	//Stock
	echo('</tr></table>');
}
function muestraart($codart, $cliente, $colorcelda) {
	//El articulo tiene diferentes tarifas segun clientes
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//datos del articulo
	$ssql = 'SELECT * FROM F_ART WHERE CODART=\'' . $codart . '\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//datos de la tarifa tengo que saber si es autentificado o no
	if($cliente != '-1') {
		$ssql = 'SELECT TARCLI FROM F_CLI WHERE CODCLI=' . $cliente;
		$rs2 = mysql_query($ssql, $conn);
		$tarifa = mysql_fetch_array($rs2);
		$tar = $tarifa['TARCLI'];
	}else{
		$ssql = 'SELECT TCACFG FROM F_CFG';
		$rs2 = mysql_query($ssql, $conn);
		$tarifa = mysql_fetch_array($rs2);
		$tar = $tarifa['TCACFG'];
	}
	$rs2 = mysql_query($ssql, $conn);
	$tarifa = mysql_fetch_array($rs2);
	//Ya tengo el articulo y la tarifa solo me falta saber el precio
	$ssql = 'SELECT PRELTA FROM F_LTA WHERE TARLTA=' . $tar . ' AND ARTLTA=\'' . $codart . '\'';
	$rs3 = mysql_query($ssql, $conn);
	$precio = mysql_fetch_array($rs3);
	//Ya tenemos todos los datos ahora vamos a escribir los que procedan
	//Consulto la tabla de Configuración
	$ssql = 'SELECT * FROM F_CFG';
	$rs4 = mysql_query($ssql, $conn); 
	$conf = mysql_fetch_array($rs4);
	//voy escribiendo la fila
	echo('<table width="100%" cellspacing="1" cellpadding="3">' . "\r\n");
	echo('<tr>');
	//IMAGEN
	if($conf['MIMCFG'] == 1) {
		echo('<td align="center" valign="middle" width="');
		if(($conf['MANCFG'] - 1) <  50) {
			echo('50');
		}else{
			echo($conf['MANCFG'] - 1);
		}
		echo('" bgcolor="' . $colorcelda . '">');
		if(MOSTRARPRECIO == 'si') { $dpropre = preciodesc($codart, $cliente); }else{ $dpropre = ''; }
		if( (file_exists('BBDD/' . $conf['CIACFG'] . $datos['IMGART'])) && ($datos['IMGART'] != '') ) {
			$imagen = $datos['IMGART'];
			echo('<img src="BBDD/' . $conf['CIACFG'] . $imagen . '" width="' . $conf['MANCFG'] . '" height="' . $conf['MALCFG'] . '" onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&precio=' . $dpropre . '\',\'800\',\'600\')" alt="Ver Artículo" style="cursor:pointer">');
		}else{
			echo('<img src="plantillas/' . PLANTILLA . '/imagenes/IND.gif" width="' . $conf['MANCFG'] . '" height="' . $conf['MALCFG'] . '" onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&precio=' . $dpropre . '\',\'800\',\'600\')" ALT="Ver Artículo" style="cursor:pointer">');
		}
		echo('</td>');
	}
	//Codigo articulo
	if($conf['MCACFG'] == 1) { echo('<td width="91" bgcolor="' . $colorcelda . '">' . $codart . '</td>'); }
	//Denominacion
	if($conf['MDECFG'] == 1) { echo('<td bgcolor="' . $colorcelda . '">' . $datos['DESART'] . '</td>'); }
	//Precio            
	if( ($conf['MPRCFG'] == 1) && ( (LOGEADO == 'si') || (MOSTRARPRECIO == 'si') ) ) {
		echo('<td align="right" width="70" bgcolor="' . $colorcelda . '">');
		printf("%.2f", preciodesc($codart, $cliente));
		echo('</td>');
	}
	//Stock
	if($conf['MSTCFG'] == 1) {
		echo('<td align="right" width="60" bgcolor="' . $colorcelda . '">');
		switch($datos['CSTART']) {
			case 0:
				echo('Art&iacute;culo no disponible');
				break;
			case 1:
				echo('Consultar Disponibilidad');
				break;
			case 2:
				echo('Art&iacute;culo disponible');
				break;
			case 3:
				printf("%.2f", $datos['USTART']);
				break;
		}
		echo('</td>');
	}
	echo('</tr></table>');
}
function ivainc($cliente) {
//funcion que averigua si los precios son con iva o sin iva.
	$conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
	mysql_select_db (BD_DATABASE1);
	if($cliente != "-1") { 
		//buscamos la tarifa del cliente
		$ssql = 'SELECT TARCLI FROM F_CLI WHERE CODCLI=' . $cliente . ' LIMIT 1';
		$rs = mysql_query($ssql,$conn);
		$datos = mysql_fetch_array($rs);
		$tarifa = $datos["TARCLI"];
	}else{
		$ssql = 'SELECT * FROM F_CFG';
		$rs = mysql_query($ssql,$conn);
		$datos = mysql_fetch_array($rs);
		$tarifa = $datos["TCACFG"];
	}
	//dentro de la tarifa buscamos si esta o no el iva incluido
	$ssql = 'SELECT IINTAR FROM F_TAR WHERE CODTAR=' . $tarifa . ' LIMIT 1';
	$rs1 = mysql_query($ssql,$conn);
	$datos1 = mysql_fetch_array($rs1);
	if($datos1["IINTAR"] == 1) {
		echo '<div align="right">IVA INCLUIDO</div>';
	}else{
		echo '<div align="right">IVA NO INCLUIDO</div>';
	}
}
$busqueda = '';
//compruebo si está activo
cmoduloart ();
//conecto con la base de datos
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
//Busco el tamaño de pagina
$ssql = 'SELECT * from F_CFG';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs); 
$TAMANO_PAGINA = $datos['NAPCFG'];
//ahora lo escribo la informacion 
if(isset($_GET['familia'])) {
  $familia = $_GET['familia'];
}else{
  $familia = '';
}
?>
<form name="section" type="GET" action="productos.php" style="border:1px solid; width:100%" class="carritofondo">
	<table border="0" cellspacing="0" cellpadding="1">
		<tr>
<?php
if(isset($_GET['seccion']) and $_GET['seccion'] != '-1') {
	$seccion = $_GET['seccion'];
	if($datos['UFSCFG'] != 0) {
		echo('<td>' . $datos['NFSCFG'] . ': </td>');
		echo('<td>');
		secciones($seccion);
		echo('</td>');
	}else{
		echo('<td><div style="visibility:hidden">' . $datos['NFSCFG'] . ': </div></td>');
		echo('<td><div style="visibility:hidden">');
		secciones($seccion);
		echo('</div></td>');
	}
	if($datos['UFFCFG'] != 0) {
		echo('<td>' . $datos['NFFCFG'] . ': </td>');
		echo('<td>');
		$familia = familias($seccion, $familia);
		echo('</td>');
	}else{
		echo('<td><div style="visibility:hidden">' . $datos['NFFCFG'] . ': </div></td>');
		echo('<td><div style="visibility:hidden">');
		$familia = familias($seccion, $familia);
		echo('</div></td>');
	}
}else {
	//debo cojer la primera sección para mostrar las familias en la primera carga
	if($datos['UFSCFG'] != 0) {
		echo('<td>' . $datos['NFSCFG'] . ': </td>');
		echo('<td>');
		$seccion = secciones(-1);
		echo('</td>');
	}else{
		echo('<td><div style="visibility:hidden">' . $datos['NFSCFG'] . ': </div></td>');
		echo('<td><div style="visibility:hidden">');
		$seccion = secciones(-1);
		echo('</div></td>');
	}
	if($datos['UFFCFG'] != 0) {
		echo('<td>' . $datos['NFFCFG'] . ': </td>');
		echo('<td>');
		$familia = familias('-1', @$_GET['familia']);
		echo('</td>');
	}else{
		echo('<td><div style="visibility:hidden">' . $datos['NFFCFG'] . ': </div></td>');
		echo('<td><div style="visibility:hidden">');
		$familia = familias('-1', $_GET['familia']);
		echo('</div></td>');
	}
}
?>
		</tr>
		<tr>
			<td>C&oacute;digo:&nbsp;</td>
			<td><input type="text" name="codigo" size="29" maxlength="150">&nbsp; &nbsp;</td>
			<td>Descripci&oacute;n:&nbsp;</td>
			<td><input type="text" name="descripcion" size="29" maxlength="150"> &nbsp; &nbsp;</td>
			<td><input type="image" name="imageField" src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonbuscar.png" style="border:none"></td>
		</tr>
	</table>
</form>
<script type="ttext/javascript">
    document.section.descripcion.focus();
</script>
<?php
//examino la página a mostrar y el inicio del registro a mostrar
$pagina = @$_GET['pagina'];
if(!$pagina) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina - 1) * $TAMANO_PAGINA;
}
//miro a ver el número total de campos que hay en la tabla con esa búsqueda
$criterio = '';
if (isset($_GET['familia']) and $_GET['familia'] != '-1') {
	$txt_criterio = $_GET['familia'];
	$criterio = 'WHERE FAMART=\'' . $_GET['familia'].'\'';
	$busqueda = 'seccion=' . $_GET['seccion'] . '&familia=' . $_GET['familia'];
	if(@$_GET['codigo'] != '') {
		$criterio .= " AND CODART like '%" . $_GET['codigo'] . "%'";
		$busqueda .= '&codigo=' . $_GET['codigo'];
	}
	if(@$_GET['descripcion'] != '') {
		$criterio .= " AND (DESART like '%" . $_GET['descripcion'] . "%'";
		$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%')";
		$busqueda .= '&descripcion=' . $_GET['descripcion'];
	}
  $criterio .= ' ORDER BY DESART';
}else{
	if (isset($_GET['seccion']) and $_GET['seccion'] != '-1') {
		$txt_criterio = famsec($_GET['seccion']);
		$criterio = 'WHERE ' . famsec($_GET['seccion']);
		$busqueda = 'seccion=' . $_GET['seccion'];
		if (@$_GET['codigo'] != '') {
			$criterio .= " AND CODART like '%" . @$_GET['codigo'] . "%'";
			$busqueda .= '&codigo=' . $_GET['codigo'];
		}
		if (isset($_GET['descripcion'])) {
			$criterio .= " AND (DESART like '%" . @$_GET['descripcion'] . "%'";
			$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%')";
			$busqueda .= '&descripcion=' . $_GET['descripcion'];
		}
    $criterio .= ' ORDER BY DESART';
	}else{
		if (isset($_GET['descripcion']) or isset($_GET['codigo'])) {
			if(isset($_GET['codigo'])) {
				$criterio .= "WHERE CODART like '%" . $_GET['codigo'] . "%'";
				$busqueda .= '&codigo=' . $_GET['codigo'];
        $criterio .= ' ORDER BY DESART';
			}
			if(isset($_GET['descripcion'])) {
				$criterio .= "WHERE DESART like '%" . $_GET['descripcion'] . "%'";
				$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%'";
				$busqueda .= '&descripcion=' . $_GET['descripcion'];
        $criterio .= ' ORDER BY DESART';
			}
		}
	}
}
$ssql = 'SELECT NAPCFG from F_CFG';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs); 
$TAMANO_PAGINA = $datos['NAPCFG'];
//examino la página a mostrar y el inicio del registro a mostrar
$pagina = @$_GET['pagina'];
if(!$pagina) {
	$inicio = 0;
	$pagina = 1;
}else {
	$inicio = ($pagina - 1) * $TAMANO_PAGINA;
}
$ssql = "SELECT COUNT(*) AS regtot FROM F_ART $criterio";
$rs = mysql_query($ssql, $conn);
//Miro a ver si hay registros
$num_total_registros = mysql_result($rs, "regtot");
//calculo el total de páginas
$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
//pongo el número de registros total, el tamaño de página y la página que se muestra
//construyo la sentencia SQL
$ssql = "select CODART from F_ART $criterio LIMIT $inicio, $TAMANO_PAGINA";
$rs1 = mysql_query($ssql, $conn);
$num_total_registros2 = mysql_num_rows($rs1);
// Escribo los datos. Mostrando solo los datos que pueda. Si no hay datos mensaje 
?>
<div class="menucolorfondo">
<?php
cabecera();
//compruebo si hay articulos
if($num_total_registros != 0) {
	echo('<div class="lproductos" style="overflow:hidden; overflow: -moz-scrollbars-none; overflow-y:scroll;">');
	//recorremos todos los productos que hay en la pagina seleccionada.
	$celdacontador = COLORCLAROCELDA;
	for($i=1; $i<=$num_total_registros2; $i++) {
		$datos1 = mysql_fetch_array($rs1);
//		if($i > $inicio and $i <= ($inicio + $TAMANO_PAGINA)) {
			if(@$_SESSION['autentificado'] == 'SI' and $_SESSION['tipo_usuario'] == 'usuario') {
				muestraart($datos1['CODART'], $_SESSION['cod_factusol'], $celdacontador);
				if($celdacontador == COLORCLAROCELDA) {
					$celdacontador = COLOROSCUROCELDA;
				}else{
					$celdacontador = COLORCLAROCELDA;
				}
			}else{
				//sin autentificar
				muestraart($datos1['CODART'], '-1', $celdacontador);
				if($celdacontador == COLORCLAROCELDA) {
					$celdacontador = COLOROSCUROCELDA;
				}else{
					$celdacontador = COLORCLAROCELDA;
				}
			}
//		}
	}
	echo('</div>');
	if(@$_SESSION['autentificado'] == 'SI' and $_SESSION['tipo_usuario'] == 'usuario') {
		ivainc($_SESSION['cod_factusol']) ;
	}else{
		ivainc('-1');
	}
}else{
	echo('</table>');
	echo('<div align="center">No se han encontrado Art&iacute;culos</div>');
}
?>
<p>N&uacute;mero de Productos encontrados: <?=$num_total_registros?><br>
	Mostrando la p&aacute;gina <?=$pagina?> de <?=$total_paginas?><br><br>
<?php
//muestro los distintos índices de las páginas, si es que hay varias páginas
if($total_paginas > 1) {
	if($total_paginas < 100) {
		for($i=1; $i<=$total_paginas; $i++) {
			if($pagina == $i) {
				//si muestro el índice de la página actual, no coloco enlace
				echo($pagina . ' ');
			}else{
				//si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
				echo("<a href='productos.php?pagina=" . $i . "&" . $busqueda . "'>" . $i . "</a> ");
			}
		}
	}else{
		echo("Ir a la pagina: <select name='paginas' onchange=\"goToUrl(this,'productos.php?".$busqueda."')\">");
		for($i=1; $i<=$total_paginas; $i++) {
			echo("<option value='" . $i . "'");
      if($pagina == $i) { echo('selected');}
      echo('>' . $i . '</option>');
		}
	echo('</select>');
	}
}
?>
</div>
<?php
require_once('button.php'); ?>