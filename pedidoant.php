<?php
session_start();
require_once('conf.inc.php');
require_once('func.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script src="./func/funciones.js"></script>
	<link href="plantillas/<?php echo(PLANTILLA); ?>/estilos/estilo.css" rel="stylesheet" type="text/css" />
	<style>.innerb {height:100%; overflow:auto;}</style>
</head>
<body class="carritofondo">

<?php
function descuento($cliente, $codart, $familia) {
	//busco el descuento ya sea el asignado al cliente o el asignado al producto por tipo de cliente
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT DT1CLI FROM F_CLI WHERE CODCLI=' . $cliente;
	//compruebo si tiene asignado un descuento
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if ($datos['DT1CLI'] != NULL or $datos['DT1CLI'] != '0') { return $datos["DT1CLI"]; } //retorno el descuento
}
function nuevo_pedido($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT * FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT CODPCL FROM F_PCL WHERE TIPPCL=\'' . $datos1['SPCCFG'] . '\' ORDER BY CODPCL DESC';
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		$numpedido = $datos['CODPCL'] + 1;
	}else{
		$numpedido = 1;
	}
	//Datos del cliente 
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $_SESSION['cod_factusol'];
	$rs2 = mysql_query($ssql, $conn);
	$datos2 = mysql_fetch_array($rs2);
	//ahora escribo el pedido
	mysql_select_db(BD_DATABASE1);
	$ssql = 'INSERT INTO F_PCL (FOPPCL,TIPPCL,CODPCL,FECPCL,AGEPCL,CLIPCL,TIVPCL,REQPCL,ESTPCL,PIVA1PCL,PIVA2PCL,PIVA3PCL,PREC1PCL,PREC2PCL,PREC3PCL,PDTO1PCL,PDTO2PCL,PDTO3PCL,PPPA1PCL,PPPA2PCL,PPPA3PCL,PFIN1PCL,PFIN2PCL,PFIN3PCL) VALUES (\'' . $datos2['FPACLI'] . '\',\'' . $datos1['SPCCFG'] . '\',' . $numpedido . ',\'' . date("Y/m/d") . '\',' . $datos2['AGECLI'] . ',' . $_SESSION['cod_factusol'] . ',' . $datos2['IVACLI'] . ',' . $datos2['REQCLI'] . ',0, ' . $datos1['PIV1CFG'] . ',' . $datos1['PIV2CFG'] . ',' . $datos1['PIV3CFG'] . ',' . $datos1['PRE1CFG'] . ',' . $datos1['PRE2CFG'] . ',' . $datos1['PRE3CFG'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ')'; 
	mysql_query($ssql, $conn);
}
function existe_pedido($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql ,$conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	//si hay pedido retorno 1 sino 0
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
		$rs2 = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs2) != 0) {
			return 1;
		}else{
			return 2;
		}
	}else{
		return 0;
	}
}
function insertar_producto($producto, $tarifa, $cliente, $cant) {
	//Al insertar producto tenemos que actualizar el pedido y el detalle.
	//tengo que tener en cuenta que no est� ya el articulo en el pedido sino loque hago es actualizar ese producto con uno m�s
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes y compruebo si existe correo y si no lo creo
	if(!existe_pedido($cliente)) { nuevo_pedido($cliente); }
	//compruebo si se permiten decimales
	$cant = decimal($cant);
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		//averiguo si hay articulos iguales en el pedido pendiente
		$ssql = 'SELECT * FROM F_LPC WHERE ARTLPC=\'' . $producto . '\' AND TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
		$rs2 = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs2) != 0) {
			//actualizo el pedido
			$datos2 = mysql_fetch_array($rs2);
			$cantidad = decimal($datos2['CANLPC'] + $cant);
			$total = ($datos2['PRELPC'] * $cantidad) - ($datos2['PRELPC'] * $cantidad * $datos2['DT1LPC'] / 100);
			$ssql = 'UPDATE F_LPC SET CANLPC=' . $cantidad . ', TOTLPC=' . $total . ' WHERE TIPLPC=\'' . $datos2['TIPLPC'] . '\' AND CODLPC=' . $datos2['CODLPC'] . ' AND ARTLPC=\'' . $producto . '\'';
			mysql_select_db(BD_DATABASE1);
			mysql_query($ssql, $conn);
		}else{
			//escribo el nuevo art�culo y actualizo las tablas.
			//posicion de la linea
			$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' ORDER BY POSLPC DESC';
			$rs3 = mysql_query($ssql, $conn);
			$linea = (mysql_num_rows($rs3) + 1);
			//Descripcion y tipo de IVA
			mysql_select_db(BD_DATABASE1);
			$ssql = 'SELECT * FROM F_ART WHERE CODART=\'' . $producto . '\'';
			$rs3 = mysql_query($ssql, $conn);
			$datos3 = mysql_fetch_array($rs3);
			$descripcion = $datos3['DESART'];
			$tipoiva = $datos3['TIVART'];
			$familia = $datos3['FAMART'];
			//Descuento del cliente
			//descuento porcentaje
			$descuento = descuento($_SESSION['cod_factusol'], $producto, $familia);
			//Precio del Articulo
			//Descuento unitario
			$ssql = 'SELECT * FROM F_LTA WHERE TARLTA=' . $tarifa . ' AND ARTLTA=\'' . $producto . '\'';
			$rs3 = mysql_query($ssql, $conn);
			$datos3 = mysql_fetch_array($rs3);
			$precio = preciodesc($producto, $_SESSION['cod_factusol']);
			//IVA Inc.
			$ssql = 'SELECT * FROM F_TAR WHERE CODTAR=' . $tarifa;
			$rs3 = mysql_query($ssql, $conn);
			$datos3 = mysql_fetch_array($rs3);
			$ivainc = $datos3['IINTAR'];         
			$total = ($precio - ($precio * $descuento / 100)) * $cant;
			mysql_select_db(BD_DATABASE1);
			$ssql = 'INSERT INTO F_LPC (TIPLPC,CODLPC,POSLPC,ARTLPC,DESLPC,CANLPC,DT1LPC,PRELPC,TOTLPC,IVALPC,IINLPC) VALUES(\'' . $datos1['SPCCFG'] . '\',' . $datos['CODPCL'] . ',' . $linea . ',\'' . $producto . '\',\'' . $descripcion . '\',' . $cant . ',' . $descuento . ',' . $precio . ',' . $total . ',' . $tipoiva . ',' . $ivainc . ')';
			$rs3 = mysql_query($ssql, $conn);
			if($rs3) {
				echo('');
			}else{
				echo('Ha habido un error');
			}
		}
	}
}
function tamimagenant($codart) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_ART WHERE CODART='$codart'";
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['IMGART'] != '' and file_exists('imagenes/' . $datos['IMGART'])) {
		list($ancho, $altura, $tipo, $atr) = getimagesize('imagenes/' . $datos['IMGART']); 
		return ($altura + 150) ;
	}else{
		return 300;
	}
}
function muestraartant($codart, $cliente, $cantidad, $colorcelda) {
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
	//Consulto la tabla de Configuraci�n
	$ssql = 'SELECT * FROM F_CFG';
	$rs4 = mysql_query($ssql, $conn); 
	$conf = mysql_fetch_array($rs4);
	//voy escribiendo la fila
	//denominacion
	echo('<tr bordercolor="#cccccc">');
	echo('<td align="left" bgcolor="' . $colorcelda . '">' . $datos['DESART'] . '</td>');
	//Precio            
	echo('<td align="right" width="40" bgcolor="' . $colorcelda . '">');
	printf("%.2f", preciodesc($codart, $cliente));
	echo('</td>');
	//Cantidad
	echo('<td align="right" nowrap width="55" bgcolor="' . $colorcelda . '">');
	echo('<table width="0" border="0" cellspacing="0" cellpadding="0">');
	echo('<tr><td rowspan="2">');
	echo('<input size="5" value="' . number_format($cantidad, decimales(), '.', '') . '"  name="cantidad' . $codart . '" id="cantidad' . $codart . '" size="10" maxlength="10" type="text" style="text-align:right;" onkeypress="EvaluateText(\'%f\', this);" onBlur="this.value = NumberFormat(this.value, \'' . decimales() . '\', \'.\', \'\')">');
	echo('</td>');
	echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" style="cursor:pointer" onclick="sumaresta(\'cantidad' . $codart . '\',\'+\',\'' . decimales() . '\')"></td></tr>');
	echo('<tr><td width="30"><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" onclick="sumaresta(\'cantidad' . $codart.'\',\'-\',\'' . decimales() . '\')" style="cursor:pointer">');
	echo('</td></tr>');
	echo('</table>');
	echo('</td>');
	echo('<td align="center" width="33" bgcolor="' . $colorcelda . '"><input name="check-' . $codart . '" type="checkbox" value="' . $codart . '" checked>');
	echo('<input type="hidden" name="codigo-' . $codart . '" id="codigo' . $codart . '" value="' . $codart . '"/>');
	echo('<input type="hidden" name="tarifa-' . $codart . '" id="tarifa' . $codart . '" value="' . $tarifa['TARCLI'] . '"/>');
	echo('</td>');
	echo('</tr>');
}
function ivaincant($cliente) {
	//funcion que averigua si los precios son con iva o sin iva.
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	if($cliente != '-1') {
		//buscamos la tarifa del cliente
		$ssql = 'SELECT TARCLI FROM F_CLI WHERE CODCLI=' . $cliente;
		$rs = mysql_query($ssql, $conn);
		$datos = mysql_fetch_array($rs);
		$tarifa = $datos['TARCLI'];
	}else{
		$ssql = 'SELECT * FROM F_CFG';
		$rs = mysql_query($ssql, $conn);
		$datos = mysql_fetch_array($rs);
		$tarifa = $datos['TCACFG'];
	}
	//dentro de la tarifa buscamos si esta o no el iva incluido
	$ssql = 'SELECT IINTAR FROM F_TAR WHERE CODTAR=' . $tarifa;
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	if($datos1['IINTAR'] == 1) {
		echo('<div align="right">IVA INCLUIDO</div>');
	}else{
		echo('<div align="right">IVA NO INCLUIDO</div>');
	}
}
// recojo las variables del formulario si se ha enviado
if(isset($_POST['enviar'])) {
	foreach($_POST as $nombre_campo => $valor) {
		if(strpos($nombre_campo, "heck-") != false) {
			if($_POST[$nombre_campo] != '') {
				$art = $_POST[$nombre_campo];
				insertar_producto($_POST[$nombre_campo], $_POST['tarifa-' . $art], $_SESSION['cod_factusol'], $_POST['cantidad' . $art]);
			}
		}
	}
	echo("<script>self.location.href='carritomenu.php'</script>");
}
// Formulario para mostar el pedido anterior
?>
<table border="1" cellspacing="0" cellpadding="1" class="carritoancho">
	<tr>
		<td colspan="4" align="center" height="20" style="border:none">PRODUCTOS PEDIDO ANTERIOR</td>
	</tr>
	<tr bordercolor="#999999" bgcolor="#cccccc">
		<th>Producto</th>
		<th width="40">Precio</th>
		<th width="55">Cantidad</th>
		<th width="50">Agregar</th>
	</tr>
</table>
<?php
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT SPCCFG FROM F_CFG';
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs);
mysql_select_db(BD_DATABASE1);
$ssql = 'select * from F_PCL where ESTPCL=1 AND TIPPCL=\'' . $datos['SPCCFG'] . '\' AND CLIPCL=' . $_SESSION['cod_factusol'] . ' order by CODPCL desc';
$rs1 = mysql_query($ssql, $conn);
if(mysql_num_rows($rs1) != 0) {
	$datos1 = mysql_fetch_array($rs1);
	$ssql = 'select * from F_LPC where TIPLPC=\'' . $datos1['TIPPCL'] . '\' AND CODLPC=' . $datos1['CODPCL'] . ' order by POSLPC ASC';
	$rs2 = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs2) != 0) {
?>
<form name="carritoant" method="post" action="?">
<div style="width:250px; height:280px; overflow:hidden; overflow: -moz-scrollbars-none; overflow-y:scroll;">
	<table width="235" valing="top" border="1" cellpadding="1" cellspacing="0" bordercolor="#000000">
<?php
		echo('<tr>');
		echo('<td>');
		echo('<div class="innerb">');
		echo('<table cellspacing="1" cellpadding="1">');
		while($datos2 = mysql_fetch_array($rs2)) {
			if(@$celdacontador == COLORCLAROCELDA) {
				$celdacontador = COLOROSCUROCELDA;
			}else{
				$celdacontador = COLORCLAROCELDA;
			}
			muestraartant($datos2['ARTLPC'], $_SESSION['cod_factusol'], $datos2['CANLPC'], $celdacontador);
		}
		echo('</table></div></td></tr>');
?>
	</table>
</div>
<table width="250" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="cabecera" height="25" align="center"><input name="enviar" type="submit" value="Agregar al pedido"></td>
	</tr>
</table>
</form>
<?php
	}
} else{
	echo('<div align="center">Todav�a no ha realizado pedidos</div>');
}
?>
</body>
</html>