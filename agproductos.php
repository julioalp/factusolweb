<?php
session_start();
if( (@$_SESSION['autentificado'] != 'SI') || ($_SESSION['tipo_usuario'] != 'agente') ) {
	header('Location: autentificage.php');
	exit();
}
require_once('conf.inc.php');
require_once('func.php');
require_once('cmodulos.php');
imodulopeda();
cmodulopeda();
require_once('top.php');
?>

<?php
function cliente($client) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT NOFCLI FROM F_CLI WHERE CODCLI=' . $client;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	echo($datos['NOFCLI']);
}
function tamimagen($codart) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_ART WHERE CODART='" . $codart . "'";
	$rs = mysql_query($ssql,$conn);
	$datos = mysql_fetch_array($rs);
	if ($datos['IMGART'] != '' and file_exists('imagenes/' . $datos['IMGART'])) {
		list($ancho, $altura, $tipo, $atr) = getimagesize('imagenes/' . $datos['IMGART']);
		return ($altura + 150) ;
	}else{
		return 300;
	}
}
function famsec($seccion) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if ($seccion != '-1') { 
		$ssql = "SELECT * FROM F_FAM WHERE SECFAM='" . $seccion . "'";
	}else{
		$ssql = "SELECT * FROM F_FAM";
	}
	$rs = mysql_query($ssql,$conn);
	$cadena = '(';
	while($datos = mysql_fetch_array($rs)) {
		$cadena .= " FAMART='" . $datos['CODFAM'] . "' or";
	}
	$cadena = substr($cadena, 0, strlen($cadena)-2) . ')';
	return $cadena;
}
function secciones($valor) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_SEC";
	$rs = mysql_query($ssql, $conn);
	// para mantener la seleccion anterior
	echo('<select name="seccion" onChange="redireccionarp(document.section.seccion.value, document.section.familia.value, \'1\')" style="width:174px">');
	echo('<option value="-1">TODOS</option>');
	for ($i=0; $i<=(mysql_num_rows($rs)-1); $i++){
		$datos = mysql_fetch_array($rs);
		if ($i == 0) { $envio = $datos['CODSEC']; }
		if ($valor == $datos['CODSEC']) { $selected = 'selected="selected"'; }
		echo '<option value="' . $datos['CODSEC'] . '" ' . $selected . '>' . $datos ['DESSEC'] . '</option>';
		$selected = NULL;
	}
	echo('</select>');
	return $valor;
}
//Escribe un select con las familias de una sección
function familias($seccion,$familia) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if ($seccion != '-1') {
		$ssql= 'SELECT * FROM F_FAM WHERE SECFAM=\'' . $seccion . '\'';
	}else{
		$ssql= 'SELECT * FROM F_FAM';
	}
	$rs = mysql_query($ssql,$conn);
	echo '<select name="familia" onChange="redireccionarp(document.section.seccion.value,document.section.familia.value,\'0\')" style="width:174px">';
	if ($familia != '-1') {
		echo '<option value="-1" >TODOS</option>';
	}else{
		echo '<option value="-1" selected>TODOS</option>';
	}
	for ($i=0; $i<=(mysql_num_rows($rs)-1); $i++){
		$datos = mysql_fetch_array($rs);
		if ($i == 0) { $envio=$datos['CODFAM']; }
		if ($datos['CODFAM'] == $familia){ $selected = 'selected="selected"'; }
		echo '<option value="' . $datos['CODFAM'] . '"' . $selected . '">' . $datos ['DESFAM'] . '</option>';
		$selected=NULL;
	}
	echo '</select>';
	return $envio;
}
function ttarifas($codarticulo, $cliente, $tardef) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//averiguo las tarifas de un articulo
	$ssql = 'SELECT * FROM F_LTA WHERE ARTLTA=\'' . $codarticulo . '\'';
	$rs = mysql_query($ssql, $conn);
	$counter = mysql_num_rows($rs);
	if($counter != 0) {
		echo('<select name="tarifa" class="tarifaselect">');
		for($i=0; $i<$counter; $i++) { 
			$datos = mysql_fetch_array($rs);
			echo($datos['TARLTA']);
			$ssql = 'SELECT * FROM F_TAR WHERE CODTAR=' . $datos['TARLTA'];
			$rs2 = mysql_query($ssql, $conn);
			$tar = mysql_fetch_array($rs2);
			if($datos['TARLTA'] != $tardef) {
				if($tar['IINTAR'] != 0) {
					echo('<option title="' . $tar['DESTAR'] . ' (I/I)' . '" value="' . $datos['TARLTA'] . '">');
					printf("%.2f", $datos['PRELTA']);
					echo('</option>');
				}else{
					echo('<option title="' . $tar['DESTAR'] . ' (+IVA)' . '" value="' . $datos['TARLTA'] . '">');
					printf("%.2f", $datos['PRELTA']);
					echo('</option>');
				}
			}else{
				if($tar['IINTAR'] != 0) {
					echo('<option title="' . $tar['DESTAR'] . '* (I/I)' . '" selected="selected" value="' . $datos['TARLTA'] . '">');
					printf("%.2f", preciodesc($codarticulo, $cliente));
					echo('</option>');
				}else{
					echo('<option title="' . $tar['DESTAR'] . '* (+IVA)' . '" selected="selected" value="' . $datos['TARLTA'] . '">');
					printf("%.2f", preciodesc($codarticulo, $cliente));
					echo('</option>');
				}
			}
		}
		echo('</select>');
	}
}
function cabecera() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	echo('<table class="taglist" width="100%" cellspacing="1" cellpadding="3">');
	echo('<tr align="center" heigth="15">');
	if($datos['MIMCFG'] == 1) {
		echo('<td width="');
		if(($datos['MANCFG'] - 1) <  50) {
			echo ('49');
		}else{
			echo($datos['MANCFG'] - 1);
		}
		echo('" class="cabecera">Detalles</td>');
	}	//IMAGEN
	if ($datos['MCACFG'] == 1) { echo('<td width="91" class="cabecera">Cod. Art&iacute;culo</td>'); }	//codigo articulo
	if ($datos['MDECFG'] == 1) { echo('<td class="cabecera">Denominaci&oacute;n</td>'); }	//denominacion
	echo('<td class="cabecera" width="70">Precio</td>');	//Precio
	if($datos['CDTCFG'] == 1) { echo('<td width="62" class="cabecera">Descuento</td>'); }	//Descuento
	echo('<td class="cabecera" width="62">Cantidad</td>');	//Cantidad
	if ($datos['MSTCFG'] == 1) { echo('<td class="cabecera" width="75">Stock</td>'); }	//Stock
	echo('<td width="32" class="cabecera">Pedir</td>');
	echo('</tr>');
	echo('</table>');
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
	if ($cliente != '-1') {
		$ssql = 'SELECT TARCLI, DT1CLI FROM F_CLI WHERE CODCLI=' . $cliente;
	}else{
		$ssql = 'SELECT TCACFG FROM F_CFG';
	}
	$rs2 = mysql_query($ssql, $conn);
	$tarifa = mysql_fetch_array($rs2);
	//Ya tengo el articulo y la tarifa solo me falta saber el precio
	$ssql = 'SELECT PRELTA FROM F_LTA WHERE TARLTA=' . $tarifa['TARCLI'] . ' AND ARTLTA=\'' . $codart . '\'';
	$rs3 = mysql_query($ssql, $conn);
	$precio = mysql_fetch_array($rs3);
	//Ya tenemos todos los datos ahora vamos a escribir los que procedan
	//Consulto la tabla de Configuración
	$ssql = 'SELECT * FROM F_CFG';
	$rs4 = mysql_query($ssql, $conn); 
	$conf = mysql_fetch_array($rs4);   
	echo('<form name="aaa' . $codart . '" method="post" action="carritomenuage.php" target="carrito">');
	echo('<table width="100%" cellspacing="1" cellpadding="3">' . "\r\n");
//voy escribiendo la fila
	echo('<tr>');
//IMAGEN
	if ($conf['MIMCFG'] == 1) {
		echo('<td align="center" valign="middle" width="');
		if(($conf['MANCFG'] - 1) <  50) {
			echo ('49');
		}else{
			echo($conf['MANCFG'] - 1);
		}
		echo('" bgcolor="' . $colorcelda . '">');
		if( (file_exists('BBDD/' . $conf['CIACFG'] . $datos['IMGART'])) && ($datos['IMGART'] != '') ) {
			$imagen = 'BBDD/' . $conf['CIACFG'] . $datos['IMGART'];
		}else{
			$imagen = 'plantillas/' . PLANTILLA . '/imagenes/IND.gif';
		}
		$htmimagen = '<img src="' . $imagen . '" width="' . $conf['MANCFG'] . '" height="' . $conf['MALCFG'] . '"';
		if ($conf['PCTCFG'] != 0) {
			$htmimagen .= ' onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&tar=\' + document.aaa' . $codart . '.tarifa.options[document.aaa' . $codart . '.tarifa.selectedIndex].value + \'&cliente=' . $cliente . '\' ,\'600\',\'' . tamimagen($codart) . '\')" border="0" ALT="Ver Art&iacute;culo" style="cursor:pointer"';
		}else{
			$htmimagen .= ' onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&tar=' . $tarifa['TARCLI'] .'&cliente=' . $cliente . '\',\'600\',\'' . tamimagen($codart) . '\')" border="0" ALT="Ver Art&iacute;culo" style="cursor:pointer"';
		}
		echo( $htmimagen . '>');
		echo('</td>');
	}
//codigo articulo
	if ($conf['MCACFG'] == 1) { echo('<td width="91" bgcolor="' . $colorcelda . '">' . $codart . '</td>'); }	//codigo de barras
	if ($conf['MDECFG'] == 1) { echo('<td bgcolor="' . $colorcelda . '">' . $datos['DESART'] . '</td>'); }	//denominacion
//Precio
	echo('<td align="right" width="70" bgcolor="' . $colorcelda . '">');
	echo('<input type="hidden" name="codigo" value="' . $codart . '" />');
	if ($conf['PCTCFG'] != 0) {
		ttarifas($codart, $cliente, $tarifa['TARCLI']);
	}else{
		echo('<input type="hidden" name="tarifa" value="' . $tarifa['TARCLI'] . '" />');
		printf("%.2f", preciodesc($codart, $cliente));
	} 
	echo('</td>');
//Descuento
	if($conf['CDTCFG'] == 1) {
		echo('<td align="right" width="62" nowrap bgcolor="' . $colorcelda . '">');
		echo('<table width="0" border="0" cellspacing="0" cellpadding="0"><tr><td rowspan="2">');
		echo('<input value="' . number_format($tarifa['DT1CLI'], decimales(), '.', '') . '" name="descuento' . str_replace('.', '-', $codart) . '" id="descuento' . $codart . '" size="6" maxlength="10" type="text" style="text-align:right;" onkeypress="EvaluateText(\'%f\', this);" onBlur="this.value = NumberFormat(this.value, \'' . decimales() . '\', \'.\', \'\')"></td>');
		echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" style="cursor:pointer" onclick="sumres(\'descuento' . $codart . '\',\'+\',\'' . decimales() . '\')"></td></tr>');
		echo('<tr> <td><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" onclick="sumres(\'descuento' . $codart . '\',\'-\',\'' . decimales() . '\')" style="cursor:pointer"></td></tr></table></td>');
	}
//Cantidad
  //$canart = ($datos['CANART'] < 0.1) ? (1) : ($datos['CANART']);
	$canart = 1;
	echo('<td align="right" width="62" nowrap bgcolor="' . $colorcelda . '">');
	echo('<table width="0" border="0" cellspacing="0" cellpadding="0"><tr><td rowspan="2">');
	echo('<input value="' . number_format($canart, decimales(), '.', '') . '" name="cantidad' . str_replace('.', '-', $codart) . '" id="cantidad' . $codart . '" size="6" maxlength="10" type="text" style="text-align:right;" onkeypress="EvaluateText(\'%f\', this);" onBlur="this.value = NumberFormat(this.value, \'' . decimales() . '\', \'.\', \'\')"></td>');
	echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" style="cursor:pointer" onclick="sumaresta(\'cantidad' . $codart . '\',\'+\',' . decimales() . ', ' . $canart . ')"></td></tr>');
	echo('<tr> <td><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" onclick="sumaresta(\'cantidad' . $codart . '\',\'-\',' . decimales() . ', ' . $canart . ')" style="cursor:pointer"></td></tr></table></td>');
//Stock
	if($conf['MSTCFG'] == 1) {
		echo('<td align="right" width="75" bgcolor="' . $colorcelda . '">');
		switch ($datos['CSTART']) {
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
		echo('&nbsp;</td>');
	}
//Pedir
	echo('<td align="center" width="31" bgcolor="' . $colorcelda . '"><input type="image" src="plantillas/' . PLANTILLA . '/imagenes/boton_pedir.gif" ALT="Incluir en el pedido" style="border:none" onclick="return ComprobarCero(\'cantidad' . $codart . '\')">');
	echo('</tr>');
	echo('</table>');
	echo('</form>');
}
function ivainc($cliente) {
	//funcion que averigua si los precios son con iva o sin iva.
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//buscamos la tarifa del clinte
	$ssql = 'SELECT TARCLI FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//dentro de la tarifa buscamos si esta o no el iva incluido
	$ssql = 'SELECT IINTAR FROM F_TAR WHERE CODTAR=' . $datos['TARCLI'];
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	if ($datos1['IINTAR'] == 1) {
		echo('<div align="right">IVA INCLUIDO</div>');
	}else{
		echo('<div align="right">IVA NO INCLUIDO</div>');
	}
}
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" class="menucolorfondo">
<?php require_once('menumage.php'); ?>
<br><div align="center">Pedido para el cliente: <strong> <? cliente($_SESSION['cliente']); ?> </strong></div><br />
<?php
//conecto con la base de datos
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
//Busco el tamaño de pagina
$ssql = 'SELECT * from F_CFG';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs); 
$TAMANO_PAGINA = $datos['NAPCFG'];
if (isset($_GET['familia'])) { $familia = $_GET['familia']; }else{ $familia = ''; }
//inicializo el criterio y recibo cualquier cadena que se desee buscar
$criterio = "";
$busqueda = '';
if (isset($_GET['familia']) && $_GET['familia'] != '' && $_GET['familia'] != '-1') {
	$txt_criterio = $_GET['familia'];
	$criterio = 'WHERE FAMART=\'' . $_GET['familia'] . '\'';
	$busqueda = 'seccion=' . $_GET['seccion'] . '&familia=' . $_GET['familia'];
	if(isset($_GET['codigo']) && $_GET['codigo'] != '') {
		$criterio .= " AND CODART like '%" . $_GET['codigo'] . "%'";
		$busqueda .= '&codigo=' . $_GET['codigo'];
	}
	if(isset($_GET['descripcion']) && $_GET['descripcion'] != '' ) {
		$criterio .= " AND (DESART like '%" . $_GET['descripcion'] . "%'";
		$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%')";
		$busqueda .= '&descripcion=' . $_GET['descripcion'];
	}
  $criterio .= ' ORDER BY DESART';
}else{
	if(isset($_GET['seccion']) && $_GET['seccion'] != '' && $_GET['seccion'] != '-1') {
		$txt_criterio = famsec($_GET['seccion']);
		$criterio = 'WHERE ' . famsec($_GET['seccion']);
		$busqueda = 'seccion=' . $_GET['seccion'];
		if(isset($_GET['codigo']) && $_GET['codigo'] != '') {
			$criterio .= " AND CODART like '%" . $_GET['codigo'] . "%'";
			$busqueda .= '&codigo=' . $_GET['codigo'];
		}
		if(isset($_GET['descripcion']) && $_GET['descripcion'] != '' ) {
			$criterio .= " AND (DESART like '%" . $_GET['descripcion'] . "%'";
			$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%')";
			$busqueda .= '&descripcion='.$_GET["descripcion"];
		}
    $criterio .= ' ORDER BY DESART';
	}else{
		if( (isset($_GET['descripcion']) && $_GET['descripcion'] != '' ) || (isset($_GET['codigo']) && $_GET['codigo'] != '' ) ) {
			if(isset($_GET['codigo']) && $_GET['codigo'] != '') {
				$criterio .= "WHERE CODART like '%" . $_GET['codigo']."%'";
				$busqueda .= '&codigo=' . $_GET['codigo'];
			}
			if(isset($_GET['descripcion']) && $_GET['descripcion'] != '') {
				$criterio .= "WHERE DESART like '%" . $_GET['descripcion'] . "%'";
				$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%'";
				$busqueda .= '&descripcion=' . $_GET['descripcion'];
			}
      $criterio .= ' ORDER BY DESART';
		}
	}
}
//examino la página a mostrar y el inicio del registro a mostrar
$pagina = @$_GET['pagina'];
if(!$pagina) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina - 1) * $TAMANO_PAGINA;
}
//miro a ver el número total de campos que hay en la tabla con esa búsqueda
$ssql = 'select * from F_ART ' . $criterio;
$rs = mysql_query($ssql, $conn);
$num_total_registros = mysql_num_rows($rs);
//calculo el total de páginas
$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
//pongo el número de registros total, el tamaño de página y la página que se muestra
//construyo la sentencia SQL
$ssql = 'select * from F_ART ' . $criterio;
$rs1 = mysql_query($ssql, $conn);
//escribo los resultados
cabecera();
if($num_total_registros != 0) {
	echo('<div class="laproductos">');
	//recorremos todos los productos que hay en la pagina seleccionada.
	$celdacontador = COLORCLAROCELDA;
	for($i=1; $i<=$num_total_registros; $i++) {
		$datos1 = mysql_fetch_array($rs1);
		// solo muestro los productos entre el inicio y el inicio+tamaño de la pagina
		if($i > $inicio and $i <= ($inicio + $TAMANO_PAGINA)) {
			muestraart($datos1['CODART'], $_SESSION['cliente'], $celdacontador);
			if($celdacontador == COLORCLAROCELDA) {
				$celdacontador = COLOROSCUROCELDA;
			}else{
				$celdacontador = COLORCLAROCELDA;
			}
		}
	}
	echo('</div>');
	if($datos['PCTCFG'] == 0) { ivainc($_SESSION['cliente']); }
}else{
	echo('</table>');
	echo('<div align="center">No se han encontrado Art&iacute;culos</div>');
}
//cerramos el conjunto de resultados y la conexión con la base de datos
?>
<table width="100%" cellpadding="5" cellspacing="0">
	<tr>
		<td align="left">N&uacute;mero de Productos encontrados: <?=$num_total_registros?></td>
		<td align="center">Mostrando la p&aacute;gina <?=$pagina?>  de <?=$total_paginas?></td>
		<td align="right">
<?php
//muestro los distintos índices de las páginas, si es que hay varias páginas
if($total_paginas > 1) {
	if($total_paginas < 10) {
		for($i=1; $i<=$total_paginas; $i++) {
			if ($pagina == $i) {
				//si muestro el índice de la página actual, no coloco enlace
				echo($pagina . ' ');
			}else{
				//si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
				//tengo que tener en cuenta que criterio se ha seguido
				echo('<a href="agproductos.php?pagina=' . $i . '&' . $busqueda . '">' . $i . '</a>');
			}
		}
	}else{
		echo('Ir a la pagina: <select name="paginas" onchange="goToUrl(this,\'agproductos.php?' . $busqueda . '\')">');
		for($i=1; $i<=$total_paginas; $i++) {
			echo('<option value="' . $i . '"');
			if($pagina == $i) { echo('selected'); }
			echo('>' . $i . '</option>');
		}
	echo('</select>');
	}
}
?>
		</td>
	</tr>
</table>
		<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolver.png" onClick="javascript:window.history.back()" border="0" style="cursor:pointer"/></td>
		<td valign="top" class="carritofondo carritoancho">
			<form name="section" type="GET" action="agproductos.php" style="border:1px solid" class="carritoancho">
<?php require_once('ppbuscar.php'); ?>
			</form>
			<hr style="margin-top:2px" />
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<a href="carritomenuage.php" target="carrito"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvercarrito.png" border="0" align="left"></a>
						<a href="pedidoantage.php" target="carrito"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonartant.png" border="0" align="right"></a>
					</td>
				</tr>
			</table>
			<iframe src="carritomenuage.php" name="carrito" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" id="carrito" class="carritoalto carritoancho"></iframe>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<a href="carritoage.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonrevcarrito.png" border="0" align="left"></a>
						<a href="fpedidoage.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonfinalizarcompra.png" border="0" align="right"></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script language="javascript">
    document.section.descripcion.focus();
</script>
<?php require_once('button.php'); ?>