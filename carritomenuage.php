<?php
session_start();
require_once('conf.inc.php');
require_once('func.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script src="func/funciones.js"></script>
	<link href="plantillas/<?php echo(PLANTILLA); ?>/estilos/estilo.css" rel="stylesheet" type="text/css">
	<style>.innerb {height:100%; overflow:auto;}</style> 
	<base target="_self">
</head>
<body class="carritofondo">

<?php
function borrar_pedido($agente, $cliente) {
	//para borrar un pedido tengo que tener en cuenta que sea un pedido de cliente que esté pendiente
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND TIPPCL=\'' . $datos['SPACFG'] . '\' AND CLIPCL=' . $cliente . ' AND ESTPCL=\'0\'';
	$rs1 = mysql_query($ssql, $conn);
	//compruebo que haya pedido y lo borro si existe
	if(mysql_num_rows($rs1) != 0) {
		$datos1 = mysql_fetch_array($rs1);
		$numpedido = $datos1['CODPCL'];
		//BORRO LAS LINEAS DE PEDIDO
		$ssql = 'DELETE FROM F_LPC WHERE TIPLPC=\'' . $datos['SPACFG'] . '\' AND CODLPC=' . $numpedido;
		mysql_query($ssql, $conn);
		//BORRO EL PEDIDO
		$ssql = 'DELETE FROM F_PCL WHERE TIPPCL=\'' . $datos['SPACFG'] . '\' AND CODPCL=' . $numpedido;
		mysql_query($ssql, $conn);         
	}
}
function borrar_producto($linea, $agente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//borro el producto
	$ssql = 'DELETE FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' AND POSLPC=' . $linea;
	$rs = mysql_query($ssql, $conn);
	$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' AND POSLPC>' . $linea;
	$rs = mysql_query($ssql, $conn);
	while($row = mysql_fetch_array($rs)) {
		//a los registros posteriores les pongo una linea menos
		$ssql = 'UPDATE F_LPC SET POSLPC=' . ($row['POSLPC'] - 1) . ' WHERE TIPLPC=\'' . $datos1['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' AND POSLPC=' . $row['POSLPC'];
		mysql_query($ssql, $conn);
	}
}
function actualiza_pedido($agente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//si existe al menos un pedido
	if(mysql_num_rows($rs) != 0) {
		//Selecciono las lineas de pedido por orden
		$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' ORDER BY POSLPC ASC';
		//actualizo el pedido
		$rs2 = mysql_query($ssql, $conn);
		$i = 1;
		while($datos2 = mysql_fetch_array($rs2)) {
			$total = $datos2['PRELPC'] * round($_POST['num' . ($i)]) - ($datos2['PRELPC'] * ($_POST['num' . ($i)]) * $datos2['DT1LPC'] / 100);
			$ssql = 'UPDATE F_LPC SET CANLPC=' . ($_POST['num' . ($i)]) . ' ,TOTLPC=' . $total . ' WHERE TIPLPC=\'' . $datos2['TIPLPC'] . '\' AND CODLPC=' . $datos2['CODLPC'] . ' AND POSLPC=' . $datos2['POSLPC']; 
			mysql_query($ssql, $conn);
			$i++;
		}
	}
}
function descuentoart($cliente, $codart, $familia) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//averiguamos el tipo de cliente
	$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	if($datos1['TCLCLI'] != '') {
		$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $datos1['TCLCLI'] . '\' AND ARTDES=\'' . $codart . '\'';
		$rs = mysql_query($ssql, $conn);
	}else{
		return 0;
	}
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		if($datos['TIPDES'] == 1) {
			return $datos['IMPDES'];
		}else{
			return 0;
		}
	}else{
		$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $datos['TCLCLI'] . '\' AND ARTDES=\'' . $familia . '\'';
		$rs = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs) != 0) {
			$datos = mysql_fetch_array($rs);
			if($datos['TIPDES'] == 1) {
				return $datos['IMPDES'];
			}else{
				return 0;
			}
		}else{
			return 0;
			}
	 }
}
function descuento($cliente, $codart, $familia) {
	//busco el descuento ya sea el asignado al cliente o el asignado al producto por tipo de cliente
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT DT1CLI FROM F_CLI WHERE CODCLI=' . $cliente;
	//compruebo si tiene asignado un descuento
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['DT1CLI'] != NULL or $datos['DT1CLI'] != '0') {
		//retorno el descuento
		return $datos['DT1CLI'];
	}
}
function nuevo_pedido($agente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT * FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT CODPCL FROM F_PCL WHERE TIPPCL=\'' . $datos1['SPACFG'] . '\' ORDER BY CODPCL DESC';
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		$numpedido = $datos['CODPCL'] + 1;
	}else{
		$numpedido = 1;
	}
	mysql_select_db(BD_DATABASE1);
	//Datos del cliente 
	$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs2 = mysql_query($ssql, $conn);
	$datos2 = mysql_fetch_array($rs2);
	mysql_select_db(BD_DATABASE1);
	//ahora escribo el pedido
	$ssql = 'INSERT INTO F_PCL (FOPPCL,TIPPCL,CODPCL,FECPCL,AGEPCL,CLIPCL,TIVPCL,REQPCL,ESTPCL,PIVA1PCL,PIVA2PCL,PIVA3PCL,PREC1PCL,PREC2PCL,PREC3PCL,PDTO1PCL,PDTO2PCL,PDTO3PCL,PPPA1PCL,PPPA2PCL,PPPA3PCL,PFIN1PCL,PFIN2PCL,PFIN3PCL) VALUES (\'' . $datos2['FPACLI'] . '\',\'' . $datos1['SPACFG'] . '\',' . $numpedido . ',\'' . date("Y/m/d") . '\',' . $agente . ',' . $cliente . ',' . $datos2['IVACLI'] . ',' . $datos2['REQCLI'] . ',0, ' . $datos1['PIV1CFG'] . ',' . $datos1['PIV2CFG'] . ',' . $datos1['PIV3CFG'] . ',' . $datos1['PRE1CFG'] . ',' . $datos1['PRE2CFG'] . ',' . $datos1['PRE3CFG'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ')'; 
	mysql_query($ssql, $conn);
}
function existe_pedido($agente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	//si hay pedido retorno 1 sino 0
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
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
function insertar_producto($producto, $tarifa, $agente, $cliente, $cant, $descuento) {
	if(!existe_pedido($agente, $cliente)) { nuevo_pedido($agente, $cliente); }
	//Al insertar producto tenemos que actualizar el pedido y el detalle.
	//tengo que tener en cuenta que no esté ya el articulo en el pedido sino loque hago es actualizar ese producto con uno más
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1, $conn);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPACFG, CDTCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datosCfg = mysql_fetch_array($rs1);
	//compruebo si se permiten decimales
	$cant = decimal($cant);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datosCfg['SPACFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		//averiguo si hay articulos iguales en el pedido pendiente
		$ssql = 'SELECT * FROM F_LPC WHERE ARTLPC=\'' . $producto . '\' AND TIPLPC=\'' . $datosCfg['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
		$rs2 = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs2) != 0) {
			//actualizo el pedido
			$datos2 = mysql_fetch_array($rs2);
			$cantidad = decimal($datos2['CANLPC'] + $cant);
			$total = ($datos2['PRELPC'] * $cantidad) - ($datos2['PRELPC'] * $cantidad * $datos2['DT1LPC'] / 100);
			$ssql = 'UPDATE F_LPC SET CANLPC=' . $cantidad . ', TOTLPC=' . $total . ' WHERE TIPLPC=\'' . $datos2['TIPLPC'] . '\' AND CODLPC=' . $datos2['CODLPC'] . ' AND ARTLPC=\'' . $producto . '\'' ;
			mysql_select_db(BD_DATABASE1);
			mysql_query($ssql, $conn);
		}else{
			//escribo el nuevo artículo y actualizo las tablas.
			//posicion de la linea
			$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datosCfg['SPACFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' ORDER BY POSLPC DESC';
			$rs3 = mysql_query($ssql, $conn);
			$linea = mysql_num_rows($rs3) + 1;
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
			if($datosCfg['CDTCFG'] == 0) { $descuento = descuento($_SESSION['cliente'], $producto, $familia); }
			//Precio del Articulo  
			//Descuento unitario
			$ssql = 'SELECT * FROM F_LTA WHERE TARLTA=' . $tarifa . ' AND ARTLTA=\'' . $producto . '\'';
			$rs3 = mysql_query($ssql, $conn);
			$datos3 = mysql_fetch_array($rs3);
			if(espredefinida($tarifa, $cliente) != 1) {
				$precio = preciodesc($producto, $cliente);
			}else{
				$precio = $datos3['PRELTA'];
			}
			//IVA Inc.
			$ssql = 'SELECT * FROM F_TAR WHERE CODTAR=' . $tarifa;
			$rs3 = mysql_query($ssql, $conn);
			$datos3 = mysql_fetch_array($rs3);
			$ivainc = $datos3['IINTAR'];         
			$total = ($precio - ($precio * $descuento / 100)) * $cant; 
			$ssql = 'INSERT INTO F_LPC (TIPLPC,CODLPC,POSLPC,ARTLPC,DESLPC,CANLPC,DT1LPC,PRELPC,TOTLPC,IVALPC,IINLPC) VALUES(\'' . $datosCfg['SPACFG'] . '\',' . $datos['CODPCL'] . ',' . $linea . ',\'' . $producto . '\',\'' . $descripcion . '\',' . decimal($cant) . ',' . $descuento . ',' . $precio . ',' . $total . ',' . $tipoiva . ',' . $ivainc . ')';
			mysql_select_db(BD_DATABASE1);
			$rs3 = mysql_query($ssql, $conn);
			if($rs3) {
				echo('');
			}else{
				echo('Ha habido un error');
			}
		}
	}
}
function escribir_pedido_agente($agente, $cliente) {
	//parto de la base que solo hay un pedido en curso
	$total = 0;
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		//cojo el detalle del pedido por orden de lineas
		$ssql = 'SELECT * FROM F_LPC WHERE CODLPC=' . $datos['CODPCL'] . ' AND TIPLPC=\'' . $datos['TIPPCL'] . '\' ORDER BY POSLPC ASC'; 
		$rs1 = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs1) != 0) {
			echo('<tr>');
			echo('<td>');
			echo('<div class="innerb">');
			echo('<table cellspacing="1" cellpadding="1">');
			$colorcelda = COLOROSCUROCELDA;
			while($linea = mysql_fetch_array($rs1)) {
				if($colorcelda == COLORCLAROCELDA) {
					$colorcelda = COLOROSCUROCELDA;
				}else{
					$colorcelda = COLORCLAROCELDA;
				}
				//compruebo que el producto no tenga 0 en cantidad y si es así lo borro
				echo('<tr bordercolor="#cccccc">');
//Concepto
				echo('<td align="left" width="77" bgcolor="' . $colorcelda . '" style="font-size:9px" title="' . $linea['DESLPC'] . '">' . substr($linea['DESLPC'], 0, 19) . '</td>');
//Unidades
				echo('<td align="right" width="56" bgcolor="' . $colorcelda . '">');
				echo('<table width="0" border="0" cellspacing="0" cellpadding="0">');
				echo('<tr>');
				echo('<td rowspan="2"><input value="' . decimal($linea['CANLPC']) . '" name="num' . $linea['POSLPC'] . '" id="num' . $linea['POSLPC'] . '" size="5" maxlength="5" type="text" onfocus="this.blur()" style="text-align:right; font-size:9px">');
				echo('</td>');
				echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" alt="Pedir uno m&aacute;s" onclick="sumaresta2(\'num' . $linea['POSLPC'] . '\',\'+\');actualizar.submit();" style="cursor:pointer"></td>');
				echo('</tr>');
				echo('<tr>');
				echo('<td><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" alt="Pedir uno menos" style="cursor:pointer" onclick="sumaresta2(\'num'.$linea['POSLPC'] . '\',\'-\');actualizar.submit();"></td>');
				echo('</tr>');
				echo('</table>');
				echo('</td>');
//Total
				echo('<td align="right" width="45" bgcolor="' . $colorcelda . '" style="font-size:9px">');
				$total = number_format($total + ($linea['TOTLPC']), 2, '.', '');
				printf("%.2f", ($linea['TOTLPC']));
				echo('</td>');
//Borrar
				echo('<td width="21" align="center" bgcolor="' . $colorcelda . '"><img src="plantillas/' . PLANTILLA . '/imagenes/nor.gif" border="0" align="right" style="cursor:pointer" onclick="borrarartage('.$linea["POSLPC"].')" alt="Quitar art&iacute;culo del pedido"></td>');
				echo('</tr>');
			}
			echo'</table></div></td></tr>';
		}else{
			echo '<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">';
			echo '<td colspan="4" align="center">NO SE HAN ENCONTRADO ARTICULOS</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">';
		echo '<td colspan="4" align="center">NO SE HA ENCONTRADO PEDIDO EN CURSO </td>';
		echo '</tr>';
	}
	return $total;
}
//Fin de funciones
if(isset($_GET['accion'])) {
	switch($_GET['accion']) {
		case 5:
			borrar_producto($_GET['linea'], $_SESSION['cod_factusol'], $_SESSION['cliente']);
			break;
	}
}
if(isset($_POST['codigo'])) {
	$cantidad = 'cantidad' . str_replace('.', '-', $_POST['codigo']);
	$codnum = str_replace('.', '-', $_POST['codigo']);
	@insertar_producto($_POST['codigo'], $_POST['tarifa'], $_SESSION['cod_factusol'], $_SESSION['cliente'], $_POST[$cantidad], $_POST['descuento' . $codnum]);
}
if(isset($_POST['num1'])) { actualiza_pedido($_SESSION['cod_factusol'], $_SESSION['cliente']); }
?>
<table valing="top" border="1" cellpadding="1" cellspacing="0" class="carritoancho">
	<tr>
		<td colspan="4" align="center" height="20" style="border:none">CONTENIDO DEL PEDIDO</td>
	</tr>
	<tr bordercolor="#999999" bgcolor="#cccccc">
		<th>Concepto</th>
		<th width="56">Unds</th>
		<th width="45">Total</th>
		<th width="38">Borrar</th>
	</tr>
</table>
<div class="carritocontenidoalto carritoancho" style=" overflow:hidden; overflow: -moz-scrollbars-none; overflow-y:scroll;">
<form name="actualizar" method="post" action="?">
<table valing="top" border="1" cellpadding="1" cellspacing="0" bordercolor="#000000" class="carritoanchoint">
<?php
if($_SESSION['tipo_usuario'] == 'agente') {
	if(!isset($_SESSION['cliente'])) {
		$total = escribir_pedido_agente($_SESSION['cod_factusol'], '-1');
	}else{
		$total = escribir_pedido_agente($_SESSION['cod_factusol'], $_SESSION['cliente']);
	}
}
?>
</table>
</form>
</div>
<table valing="top" border="1" cellpadding="1" cellspacing="0" class="carritoancho">
	<tr bordercolor="#999999" align="center" bgcolor="#cccccc">
		<td width="60%"><b>Total:</b></td>
		<td align="right"><b>
<?php if($total != 0) { printf("%.2f", $total); }else{ echo('0.00'); } ?>
			</b></td>
	</tr>
</table>
</body>
</html>