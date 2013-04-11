<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'usuario') {
	header('Location: autentifica.php');
	exit();
}
require_once('cmodulos.php');
imodulopedc();
cmodulopedc();
require_once('top.php');
?>

<?php
require_once('func.php');
function tamimagen($codart) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_ART WHERE CODART='$codart'";
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if ($datos['IMGART'] != '' and file_exists('imagenes/' . $datos['IMGART'])) {
		list($ancho, $altura, $tipo, $atr) = getimagesize('imagenes/' . $datos['IMGART']); 
		return ($altura + 150);
	}else{
		return 300;
	}
}
function famsec($seccion) {
	//construye la consulta para ver productos de una sección
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if ($seccion != '-1') {
		$ssql = "SELECT * FROM F_FAM WHERE SECFAM='$seccion'";
	}else{
		$ssql = 'SELECT * FROM F_FAM';
	}
	$rs = mysql_query($ssql, $conn);
	$cadena = '(';
	while ($datos = mysql_fetch_array($rs)) {
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
	echo('<select name="seccion" class="buscarselect" onChange="redireccionar(document.section.seccion.value,document.section.familia.value,\'1\')">');
	echo('<option value="-1">TODOS</option>');
	for ($i=0; $i<=(mysql_num_rows($rs) - 1); $i++) {
		$datos = mysql_fetch_array($rs);
		if ($i == 0) { $envio = $datos['CODSEC']; }
		if ($valor == $datos['CODSEC']) { $selected = 'selected="selected"'; }
		echo('<option value="' . $datos['CODSEC'] . '" ' . $selected . '>' . $datos['DESSEC'] . '</option>');
		$selected = NULL;
	}
	echo('</select>');
	return $valor;
}
//Escribe un select con las familias de una sección
function familias($seccion, $familia) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if ($seccion != '-1') {
		$ssql = "SELECT * FROM F_FAM WHERE SECFAM='$seccion'";
	}else{
		$ssql = 'SELECT * FROM F_FAM';
	}
	$rs = mysql_query($ssql, $conn);
	echo('<select name="familia" class="buscarselect" onChange="redireccionar(document.section.seccion.value,document.section.familia.value,\'0\')">');
	if ($familia != '-1') {
		echo('<option value="-1">TODOS</option>');
	}else{
		echo('<option value="-1" selected>TODOS</option>');
	}
	for ($i=0; $i<=(mysql_num_rows($rs) - 1); $i++) {
		$datos = mysql_fetch_array($rs);
		if ($i == 0) { $envio = $datos['CODFAM']; }
		if ($datos['CODFAM'] == $familia) { $selected = ' selected="selected"'; }
		echo('<option value="' . $datos['CODFAM'] . '"' . $selected . '>' . $datos['DESFAM'] . '</option>');
		$selected=NULL;
	}
	echo '</select>';
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
	if ($datos['MCACFG'] == 1) { echo('<td width="91" class="cabecera" nowrap="nowrap">Cod. Art&iacute;culo</td>'); }
	if ($datos['MDECFG'] == 1) { echo('<td class="cabecera">Denominaci&oacute;n</td>'); }
	if ($datos['MPRCFG'] == 1) { echo('<td width="70" class="cabecera">Precio</td>'); }
	echo('<td width="62" class="cabecera">Cantidad</td>');
	if ($datos['MSTCFG'] == 1) { echo('<td width="75" class="cabecera">Stock</td>'); }
	echo('<td width="34" class="cabecera">Pedir</td>');
	echo('<td width="8" class="cabecera"></td>');
	echo('</tr>');
	echo('</table>');
}
function muestraart($codart, $cliente, $colorcelda) {
	//El articulo tiene diferentes tarifas segun clientes
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//datos del articulo
	$ssql = "SELECT * FROM F_ART WHERE CODART='$codart'";
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//datos de la tarifa
	$ssql = 'SELECT TARCLI, DT1CLI FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs2 = mysql_query($ssql, $conn);
	$tarifa = mysql_fetch_array($rs2);
	//Consulto la tabla de Configuración
	$ssql = 'SELECT * FROM F_CFG WHERE DS1CFG <> ""';
	$rs4 = mysql_query($ssql, $conn); 
	$conf = mysql_fetch_array($rs4);
	$tarifaCliente = ($tarifa['TARCLI'] != 0) ? ($tarifa['TARCLI']) : ($conf['TCACFG']);
	//Ya tengo el articulo y la tarifa solo me falta saber el precio
	$ssql = 'SELECT PRELTA FROM F_LTA WHERE TARLTA=' . $tarifaCliente . ' AND ARTLTA=\'' . $codart . '\'';
	$rs3 = mysql_query($ssql, $conn);
	$precio = mysql_fetch_array($rs3);
	//Ya tenemos todos los datos ahora vamos a escribir los que procedan
	//voy escribiendo la fila
	echo('<form name="aaa' . $codart . '" method="post" action="carritomenu.php" target="carrito">' . "\r\n");
	echo('<tr>' . "\r\n");
	if($conf['MIMCFG'] == 1) {	//IMAGEN
		echo('<td align="center" valign="middle" width="');
		if(($conf['MANCFG'] - 1) <  50) {
			echo ('50');
		}else{
			echo($conf['MANCFG'] - 1);
		}
		echo('" bgcolor="' . $colorcelda . '">');
		if( (file_exists('BBDD/' . $conf['CIACFG'] . $datos['IMGART'])) && ($datos['IMGART'] != '') ) {
			$imagen = $datos['IMGART'];
			echo('<img src="BBDD/' . $conf['CIACFG'] . $imagen . '" width="' . $conf['MANCFG'] . '" height="' . $conf['MALCFG'] . '" onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&precio=' . preciodesc($codart, $cliente) . '\',\'800\',\'600\')" border="0" alt="Ver Artículo" style="cursor:pointer">');
		}else{
			echo('<img src="plantillas/' . PLANTILLA . '/imagenes/IND.gif" width="' . $conf['MANCFG'] . '" height="' . $conf['MALCFG'] . '" onclick="Abrir_Ventana(\'dproducto.php?cod=' . $codart . '&precio=' . preciodesc($codart, $cliente) . '\',\'800\',\'600\')" border="0" alt="Ver Artículo" style="cursor:pointer">' . "\r\n");
		}
		echo('</td>' . "\r\n");
	} 
	if ($conf['MCACFG'] == 1) { echo('<td width="91" bgcolor="' . $colorcelda . '">' . $codart . '</td>' . "\r\n"); }	//codigo articulo
	if ($conf['MDECFG'] == 1) { echo('<td bgcolor="' . $colorcelda . '">' . $datos['DESART'] . '</td>' . "\r\n"); }	//denominacion
	if ($conf['MPRCFG'] == 1) {	//Precio
		echo('<td align="right" width="70" bgcolor="' . $colorcelda . '">' . "\r\n");
		$predesc = preciodesc($codart, $cliente);
		printf("%.2f", $predesc - ($predesc * $tarifa['DT1CLI'] / 100));
		echo('</td>' . "\r\n");
	}
//Cantidad
  //$canart = ($datos['CANART'] < 0.1) ? (1) : ($datos['CANART']);
	$canart = 1;
	echo('<td width="62" nowrap bgcolor="' . $colorcelda . '">' . "\r\n");
	echo('<table width="0" border="0" cellspacing="0" cellpadding="0">' . "\r\n");
	echo('<tr>' . "\r\n");
	echo('<td rowspan="2">' . "\r\n");
	echo('<input value="' . number_format($canart, decimales(), '.', '') . '" name="codigo' . str_replace('.', '-', $codart) . '" id="codigo' . $codart . '" size="6" maxlength="10" type="text" style="text-align:right;" onkeypress="EvaluateText(\'%f\', this);" onBlur="this.value = NumberFormat(this.value, \'' . decimales() . '\', \'.\', \'\')">' . "\r\n");
	echo('</td>' . "\r\n");
	echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" style="cursor:pointer" alt="Seleccionar uno m&aacute;s" onclick="sumaresta(\'codigo' . $codart . '\',\'+\',' . decimales() . ', ' . $canart . ')"></td>' . "\r\n");
	echo('</tr>' . "\r\n");
	echo('<tr>' . "\r\n");
	echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" alt="Seleccionar uno menos" onclick="sumaresta(\'codigo' . $codart . '\',\'-\',' . decimales() . ', ' . $canart .')" style="cursor:pointer"></td>' . "\r\n");
	echo('</tr>' . "\r\n");
	echo('</table>' . "\r\n");
	echo('</td>' . "\r\n");
	if ($conf['MSTCFG'] == 1) {	//Stock
		echo('<td align="right" width="75" bgcolor="' . $colorcelda . '">' . "\r\n");
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
		echo('</td>' . "\r\n");
	}
	echo('<td align="center" width="33" bgcolor="' . $colorcelda . '">' . "\r\n"); //Añadir al pedido
	echo('<input type="image" src="plantillas/' . PLANTILLA . '/imagenes/boton_pedir.gif" alt="Incluir en el pedido" style="border:none" onclick="return ComprobarCero(\'codigo' . $codart . '\')" >' . "\r\n");
	echo('<input type="hidden" name="codigo" id="codigo" value="' . $codart . '"/>' . "\r\n");
	echo('<input type="hidden" name="tarifa" id="tarifa" value="' . $tarifaCliente . '"/>' . "\r\n");
	echo('</td>' . "\r\n");
	echo('</tr>' . "\r\n");
	echo('</form>' . "\r\n");
}
function ivainc($cliente) {
	//funcion que averigua si los precios son con iva o sin iva.
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT TCACFG FROM F_CFG WHERE DS1CFG <> ""';
	$rs4 = mysql_query($ssql, $conn); 
	$conf = mysql_fetch_array($rs4);
	//buscamos la tarifa del clinte
	$ssql = "SELECT TARCLI FROM F_CLI WHERE CODCLI=$cliente";
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	$tarifaCliente = ($datos['TARCLI'] != 0) ? ($datos['TARCLI']) : ($conf['TCACFG']);
	//dentro de la tarifa buscamos si esta o no el iva incluido
	$ssql = 'SELECT IINTAR FROM F_TAR WHERE CODTAR=' . $tarifaCliente;
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	if ($datos1['IINTAR'] == 1) {
		echo('<div align="right">IVA INCLUIDO</div>');
	}else{
		echo('<div align="right">IVA NO INCLUIDO</div>');
	}
}
//conecto con la base de datos
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
//Busco el tamaño de pagina
$ssql = 'SELECT * from F_CFG';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs); 
$TAMANO_PAGINA = $datos['NAPCFG'];
if (isset($_GET['familia'])) { $familia = $_GET['familia']; }
?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" class="menucolorfondo">
<?php require_once('menum.php'); ?>
<br />
<?php
//inicializo el criterio y recibo cualquier cadena que se desee buscar
$busqueda = '';
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
}else{
	if (isset($_GET['seccion']) and $_GET['seccion'] != '-1') {
		$txt_criterio = famsec($_GET['seccion']);
		$criterio = 'WHERE ' . famsec($_GET['seccion']);
		$busqueda = 'seccion=' . $_GET['seccion']; 
		if (isset($_GET['codigo']) && ($_GET['codigo'] != '')) {
			$criterio .= " AND CODART like '%" . $_GET['codigo'] . "%'";
			$busqueda .= '&codigo=' . $_GET['codigo'];
		}
		if (isset($_GET['descripcion']) && ($_GET['descripcion'] != '')) {
			$criterio .= " AND (DESART like '%" . $_GET['descripcion'] . "%'";
			$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%')";
			$busqueda .= '&descripcion=' . $_GET['descripcion'];
		}
	}else{
		if (isset($_GET['descripcion']) or isset($_GET['codigo'])) {
			if ($_GET['codigo'] != '') {
				$criterio .= "WHERE CODART like '%" . $_GET['codigo'] . "%'";
				$busqueda .= '&codigo=' . $_GET['codigo'];
			}
			if ($_GET['descripcion'] != '') {
				$criterio .= "WHERE DESART like '%" . $_GET['descripcion'] . "%'";
				$criterio .= " OR DEWART like '%" . $_GET['descripcion'] . "%'";
				$busqueda .= '&descripcion=' . $_GET['descripcion'];
      }
		}
	}
}
//examino la página a mostrar y el inicio del registro a mostrar
$pagina = @$_GET['pagina'];
if (!$pagina) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina - 1) * $TAMANO_PAGINA;
}
//miro a ver el número total de campos que hay en la tabla con esa búsqueda
$ssql = "SELECT COUNT(*) AS regtot FROM F_ART $criterio";
$rs = mysql_query($ssql, $conn);
$num_total_registros = mysql_result($rs, "regtot");
//calculo el total de páginas
$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
//pongo el número de registros total, el tamaño de página y la página que se muestra
//construyo la sentencia SQL
$ssql = "select CODART from F_ART $criterio LIMIT $inicio, $TAMANO_PAGINA";
$rs1 = mysql_query($ssql, $conn);
$num_total_registros2 = mysql_num_rows($rs1);
//escribo los resultados
cabecera();
if ($num_total_registros != 0) {
	echo('<div class="lproductos">');
	echo('<table width="100%" cellspacing="1" cellpadding="3">');
	//recorremos todos los productos y mostramos solo los seleecionados
	$celdacontador = COLORCLAROCELDA;
	for ($i=1; $i<=$num_total_registros2; $i++) {
		$datos1 = mysql_fetch_array($rs1);
		// solo muestro los productos entre el inicio y el inicio+tamaño de la pagina
//		if ( ($i > $inicio) and ($i <= ($inicio + $TAMANO_PAGINA)) ) {
			muestraart($datos1['CODART'], $_SESSION['cod_factusol'], $celdacontador);
			if($celdacontador == COLORCLAROCELDA) {
				$celdacontador = COLOROSCUROCELDA;
			}else{
				$celdacontador = COLORCLAROCELDA;
			}
//		}
	}
	echo('</table>');
	echo('</div>');
	ivainc($_SESSION['cod_factusol']);
}else{
	echo('<br /><div align="center"><u>No se han encontrado Articulos</u></div><br />');
}
//cerramos el conjunto de resultados y la conexión con la base de datos
?>
<table width="100%" cellpadding="5" cellspacing="0">
	<tr>
		<td align="left">N&uacute;mero de Productos encontrados: <?=$num_total_registros?></td>
		<td align="center">Mostrando la p&aacute;gina <?=$pagina?> de <?=$total_paginas?></td>
		<td align="right">
<?php
//muestro los distintos índices de las páginas, si es que hay varias páginas
if ($total_paginas > 1) {
	if ($total_paginas < 10) {
		for ($i=1; $i<=$total_paginas; $i++) {
			if ($pagina == $i) {
			//si muestro el índice de la página actual, no coloco enlace
				echo($pagina . ' ');
			}else{
			//si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
			//tengo que tener en cuenta que criterio se ha seguido
				echo('<a href="pproductos.php?pagina=' . $i . '&'. $busqueda. '">' . $i . '</a>');
			}
		}
	}else{
		echo('Ir a la pagina: <select name="paginas" onchange="goToUrl(this,\'pproductos.php?' . $busqueda . '\')">');
		for ($i=1; $i<=$total_paginas ;$i++) {
			echo('<option value="' . $i . '"');
			if ($pagina == $i) { echo('selected'); }
			echo('>' . $i . '</option>');
		}
		echo('</select>');
	}
}
?>
		</td>
	</tr>
</table>
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolver.png" onClick="javascript:window.history.back()" border="0" align="bottom" style="cursor:pointer"></a></td>
		<td valign="top" class="carritoancho">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="carritofondo">
				<tr>
					<td valign="top" align="center">
						<form name="section" type="GET" action="pproductos.php" style="border:1px solid" class="carritoancho">
<?php require_once('ppbuscar.php'); ?>
						</form>
					</td>
				</tr>
				<tr>
					<td height="21" valign="top">
						<hr />
						<a href="carritomenu.php" target="carrito"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvercarrito.png" width="55" height="19" border="0" align="left" id="Image1" onmousedown="MM_swapImage('Image1','','plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvercarritopulsado.png',1)" onmouseout="MM_swapImgRestore()" onmouseup="MM_swapImgRestore()"></a>
						<a href="pedidoant.php" target="carrito"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonartant.png" width="134" height="19" border="0" align="right" id="Image2" onmousedown="MM_swapImage('Image2','','plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonartantpulsado.png',1)" onmouseout="MM_swapImgRestore()" onmouseup="MM_swapImgRestore()"></a>					</td>
				</tr>
				<tr>
					<td valign="top" class="carritoalto">
						<iframe src="carritomenu.php" name="carrito" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" id="carrito" class="carritoalto carritoancho"></iframe>
					</td>
				</tr>
				<tr>
					<td height="21" valign="bottom">
						<a href="carrito.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonrevcarrito.png" name="Image3" width="104" height="19" border="0" align="left" id="Image3" onmousedown="MM_swapImage('Image3','','plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonrevcarritopulsado.png',1)" onmouseup="MM_swapImgRestore()" onmouseout="MM_swapImgRestore()"></a>
						<a href="fpedido.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonfinalizarcompra.png" width="117" height="19" border="0" align="right" id="Image4" onmousedown="MM_swapImage('Image4','','plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonfinalizarcomprapulsado.png',1)" onmouseout="MM_swapImgRestore()"onmouseup="MM_swapImgRestore()"></a>					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script language="javascript">
    document.section.descripcion.focus();
</script>
<?php require_once('button.php'); ?>