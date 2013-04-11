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
require_once('menum.php');
require_once('conf.inc.php');
require_once('func.php');
?>
<h3 align="center"><strong>Contenido del pedido</strong></h3>
<form name="actualizar" method="post" action="carrito.php" class="menucolorfondo">
	<table align="center" border="1" bordercolor="#000000" cellpadding="2" cellspacing="0">
		<tr bordercolor="#999999" bgcolor="#cccccc">
			<th height="20">Cod</th>
			<th>Concepto</th>
			<th>Precio</th>
			<th>Unds</th>
			<th>Descuento</th>
			<th>Total</th>
			<th>IVA inc.</th>
			<th>Borrar</th>
		</tr>
<?
function actualiza_pedido($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1  =mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//si existe al menos un pedido
	if (mysql_num_rows($rs) != 0) {
		//Selecciono las lineas de pedido por orden
		$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' ORDER BY POSLPC ASC';
		//actualizo el pedido
		$rs2 = mysql_query($ssql, $conn);
		$i = 1;
		while($datos2 = mysql_fetch_array($rs2)) {
			$total = $datos2['PRELPC'] * ($_POST['num' . ($i)]) - ($datos2['PRELPC'] * (round($_POST['num' . ($i)]) * $datos2['DT1LPC'] / 100));
			$ssql = 'UPDATE F_LPC SET CANLPC=' . ($_POST['num' . ($i)]) . ' ,TOTLPC=' . $total . ' WHERE TIPLPC=\'' . $datos2['TIPLPC'] . '\' AND CODLPC=' . $datos2['CODLPC'] .' AND POSLPC=' . $datos2['POSLPC']; 
			mysql_query($ssql, $conn);
			$i += 1;
		}
	}
}
function borrar_producto($linea, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//borro el producto
	$ssql = 'DELETE FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'] .' AND POSLPC=' . $linea;
	$rs = mysql_query($ssql, $conn);
	$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] .'\' AND CODLPC=' . $datos['CODPCL'] . ' AND POSLPC>' . $linea;
	$rs = mysql_query($ssql, $conn);
	while ($row = mysql_fetch_array($rs)) {
		//a los registros posteriores les pongo una linea menos
		$ssql = 'UPDATE F_LPC SET POSLPC=' . ($row['POSLPC']-1) . ' WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'] . ' AND POSLPC=' . $row['POSLPC'];
		mysql_query($ssql, $conn);
	}
	// si no hay productos borro el pedido
	$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
	$ra = mysql_query($ssql, $conn);
	if (mysql_num_rows($ra) == 0) { borrar_pedido($cliente); }
}
function insertar_producto($producto, $tarifa, $cliente, $cant) {
	//Al insertar producto tenemos que actualizar el pedido y el detalle.
	//tengo que tener en cuenta que no esté ya el articulo en el pedido sino loque hago es actualizar ese producto con uno más
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	//compruebo si existe correo y si no lo creo
	if (!existe_pedido($cliente)) { nuevo_pedido($cliente); }
	//compruebo si se permiten decimales
	$cant = decimal($cant);
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	if (mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		//averiguo si hay articulos iguales en el pedido pendiente
		$ssql = 'SELECT * FROM F_LPC WHERE ARTLPC=\'' . $producto . '\' AND TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
		$rs2 = mysql_query($ssql, $conn);
		if (mysql_num_rows($rs2) != 0) {
			//actualizo el pedido
			$datos2 = mysql_fetch_array($rs2);
			$cantidad = decimal($datos2['CANLPC'] + $cant);
			$total = ($datos2['PRELPC'] * $cantidad) - ($datos2['PRELPC'] * $cantidad * $datos2['DT1LPC'] / 100);
			$ssql = 'UPDATE F_LPC SET CANLPC=' . $cantidad . ', TOTLPC=' . $total . ' WHERE TIPLPC=\'' . $datos2['TIPLPC'] . '\' AND CODLPC=' . $datos2['CODLPC'] . ' AND ARTLPC=\'' . $producto . '\'' ;
			mysql_query($ssql, $conn);
		}else{
			//escribo el nuevo artículo y actualizo las tablas.
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
			$total = ($precio-($precio * $descuento / 100)) * $cant; 
			mysql_select_db(BD_DATABASE1);
			$ssql = 'INSERT INTO F_LPC (TIPLPC,CODLPC,POSLPC,ARTLPC,DESLPC,CANLPC,DT1LPC,PRELPC,TOTLPC,IVALPC,IINLPC) VALUES(\'' . $datos1['SPCCFG'] . '\',' . $datos['CODPCL'] . ',' . $linea . ',\'' . $producto . '\',\'' . $descripcion . '\',' . $cant . ',' . $descuento . ',' . $precio . ',' . $total . ',' . $tipoiva . ',' . $ivainc . ')'; 
			$rs3 = mysql_query($ssql, $conn);
			if ($rs3) {
				echo('');
			}else{
				echo('Ha habido un error');
			}
		}
	}
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
	if (mysql_num_rows($rs) != 0) {
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
	mysql_select_db (BD_DATABASE1);
	$ssql = 'INSERT INTO F_PCL (FOPPCL,TIPPCL,CODPCL,FECPCL,AGEPCL,CLIPCL,TIVPCL,REQPCL,ESTPCL,PIVA1PCL,PIVA2PCL,PIVA3PCL,PREC1PCL,PREC2PCL,PREC3PCL,PDTO1PCL,PDTO2PCL,PDTO3PCL,PPPA1PCL,PPPA2PCL,PPPA3PCL,PFIN1PCL,PFIN2PCL,PFIN3PCL) VALUES (\'' . $datos2['FPACLI'] . '\',\'' . $datos1['SPCCFG'] . '\',' . $numpedido . ',\'' . date("Y/m/d") . '\',' . $datos2['AGECLI'] . ',' . $_SESSION['cod_factusol'] . ',' . $datos2['IVACLI'] . ',' . $datos2['REQCLI'] . ',0, ' . $datos1['PIV1CFG'] . ',' . $datos1['PIV2CFG'] . ',' . $datos1['PIV3CFG'] . ',' . $datos1['PRE1CFG'] . ',' . $datos1['PRE2CFG'] . ',' . $datos1['PRE3CFG'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['DT2CLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['PPACLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ',' . $datos2['FINCLI'] . ')'; 
	mysql_query($ssql, $conn);
}
function existe_pedido($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	mysql_select_db(BD_DATABASE1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	$rs = mysql_query($ssql, $conn);
	//si hay pedido retorno 1 sino 0
	if (mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		$ssql = 'SELECT * FROM F_LPC WHERE TIPLPC=\'' . $datos1['SPCCFG'] . '\' AND CODLPC=' . $datos['CODPCL'];
		$rs2 = mysql_query($ssql, $conn);
		if (mysql_num_rows($rs2) != 0) {
			return 1;
		}else{
			return 2;
		}
	}else{
		return 0;
	}
}
function borrar_pedido($cliente) {
	//para borrar un pedido tengo que tener en cuenta que sea un pedido de cliente que esté pendiente
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_PCL WHERE TIPPCL=\'' . $datos['SPCCFG'] . '\' AND CLIPCL=' . $cliente . ' AND ESTPCL=\'0\'';
	$rs1 = mysql_query($ssql, $conn);
	//compruebo que haya pedido y lo borro si existe
	if (mysql_num_rows($rs1) != 0){
		$datos1 = mysql_fetch_array($rs1);
		$numpedido = $datos1['CODPCL'];
		//BORRO LAS LINEAS DE PEDIDO
		$ssql = 'DELETE FROM F_LPC WHERE TIPLPC=\'' . $datos['SPCCFG'] . '\' AND CODLPC=' . $numpedido ;
		mysql_query($ssql, $conn);
		//BORRO EL PEDIDO
		$ssql = 'DELETE FROM F_PCL WHERE TIPPCL=\'' . $datos['SPCCFG'] . '\' AND CODPCL=' . $numpedido;
		mysql_query($ssql, $conn);         
	}
}
function descuentoart($cliente, $codart, $familia) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//averiguamos el tipo de cliente
	$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	if ($datos1['TCLCLI'] != '') {  
		$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $datos1['TCLCLI'] . '\' AND ARTDES=\'' . $codart . '\'';
		$rs = mysql_query($ssql, $conn);
	}else{
		return 0;
	}
	if (mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		if ($datos['TIPDES'] == 1) {
			return $datos['IMPDES'];
		}else{
			return 0;
		}
	}else{
		$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $datos['TCLCLI'] . '\' AND ARTDES=\'' . $familia . '\'';
		$rs = mysql_query($ssql, $conn);
		if (mysql_num_rows($rs) != 0) {
			$datos = mysql_fetch_array($rs);
			if ($datos['TIPDES'] == 1) {
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
	if ($datos['DT1CLI'] != NULL or $datos['DT1CLI'] != '0') { return $datos['DT1CLI']; }	//retorno el descuento
}
function escribir_pedido($cliente) {
	//parto de la base que solo hay un pedido en curso
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//busco la serie para pedidos de clientes
	$ssql = 'SELECT SPCCFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	//busco los pedidos por serie, cliente y estado      
	$ssql = 'SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1['SPCCFG'] . '\' AND ESTPCL=\'0\'';
	mysql_select_db(BD_DATABASE1);
	$rs = mysql_query($ssql, $conn);
	echo (mysql_error());
	if (mysql_num_rows($rs) != 0) {
		$datos = mysql_fetch_array($rs);
		//cojo el detalle del pedido por orden de lineas
		$ssql = 'SELECT * FROM F_LPC WHERE CODLPC=' . $datos['CODPCL'] . ' AND TIPLPC=\'' . $datos['TIPPCL'] . '\' ORDER BY POSLPC ASC'; 
		$rs1 = mysql_query($ssql, $conn);
		if (mysql_num_rows($rs1) != 0) {
			$total = 0;
			$colorcelda = COLOROSCUROCELDA;
			while ($linea = mysql_fetch_array($rs1)) {
				if($colorcelda == COLORCLAROCELDA) {
					$colorcelda = COLOROSCUROCELDA;
				}else{
					$colorcelda = COLORCLAROCELDA;
				}
				//compruebo que el producto no tenga 0 en cantidad y si es así lo borro
				echo('<tr bordercolor="#cccccc" bgcolor="#ffffff">');
				echo('<td bgcolor="' . $colorcelda . '">' . $linea['ARTLPC'] . '</td>');
				echo('<td bgcolor="' . $colorcelda . '">' . $linea['DESLPC'] . '</td>');
				echo('<td align="right" bgcolor="' . $colorcelda . '">');
				printf("%.2f", $linea['PRELPC']);
				echo('</td>');
				echo('<td align="right" nowrap bgcolor="' . $colorcelda . '"><img src="plantillas/' . PLANTILLA . '/imagenes/menos.gif" border="0" style="cursor:pointer" onclick="sumaresta2(\'num' . $linea['POSLPC'] . '\',\'-\');actualizar.submit();" >&nbsp;<input value="');
				echo(decimal($linea['CANLPC']));
				echo('" name="num' . $linea['POSLPC'] . '" id="num' . $linea['POSLPC'] . '" size="10" maxlength="10" type="text" onfocus="this.blur()" style="text-align:right;">&nbsp;<img src="plantillas/' . PLANTILLA . '/imagenes/mas.gif" border="0" onclick="sumaresta2(\'num' . $linea['POSLPC'] . '\',\'+\');actualizar.submit();" style="cursor:pointer"></td>');
				echo('<td align="right" bgcolor="' . $colorcelda . '">');
				printf("%.2f", $linea['DT1LPC']);
				echo('%</td>');
				echo('<td align="right" bgcolor="' . $colorcelda . '">');
				$total = number_format($total + ($linea['TOTLPC']), 2, '.', '');
				printf("%.2f", ($linea['TOTLPC']));
				echo('</td>');
				if ($linea['IINLPC'] != 0) {
					echo('<td align="center" bgcolor="' . $colorcelda . '">SI</td>');
				}else{
					echo('<td align="center" bgcolor="' . $colorcelda . '">NO</td>');
				}
				echo('<td align="center" bgcolor="' . $colorcelda . '"><img src="plantillas/' . PLANTILLA . '/imagenes/nor.gif" border="0" align="middle" style="cursor:pointer" onclick="borrarart(' . $linea['POSLPC'] . ')" alt="Borrar art&iacute;culo">&nbsp;</td>');
				echo('</tr>');
			}
			echo('<tr bordercolor="#999999" align="right" bgcolor="#cccccc">');
			echo('<td colspan="6"><font color="#000000" face="Verdana" size="2"><b>Total:</b></font></td>');
			echo('<td colspan="2"><font color="#000000" face="Verdana" size="2"><b>');
			printf("%.2f", $total);
			echo('</b></font></td>');
			echo('</tr>');
		}else{
			echo('<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">');
			echo('<td colspan="7" align="center">NO SE HAN ENCONTRADO ARTICULOS</td>');
			echo('</tr>');
			echo('<tr bordercolor="#999999" align="center" bgcolor="#cccccc">');
			echo('<td colspan="2"><font color="#000000" face="Verdana" size="2"><b>Total:</b></font></td>');
			echo('<td></td><td>&nbsp;</td><td>&nbsp;</td>');
			echo('<td><font color="#cccccc">.</font></td>');
			echo('<td><font color="#cccccc">.</font></td>');
			echo('<td><font color="#cccccc">.</font></td>');
			echo('</tr>');
		} 
	}else{
		echo('<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">');
		echo('<td height="30" colspan="8">NO SE HAN ENCONTRADO ARTICULOS EN EL PEDIDO</td></tr>');
		echo('<tr bordercolor="#999999" align="center" bgcolor="#cccccc">');
		echo('<td colspan="8"><font color="#000000" face="Verdana" size="2"><b>&nbsp;</b></font></td>');
		echo('</tr>');
	}
}
// Tipos de Acciones 1=Nuevo 2=Borrar 4=borrar producto
if(isset($_GET['accion'])) {
	switch ($_GET['accion']) {
		case 1:
			nuevo_pedido($_SESSION['cod_factusol']);
			break;
		case 2:
			borrar_pedido($_SESSION['cod_factusol']);
			echo('<script language="Javascript">location.href="cliente.php";</script>');
			break;
		case 4:
			borrar_pedido($_SESSION['cod_factusol']);
			nuevo_pedido($_SESSION['cod_factusol']);
			break;
		case 5:
			borrar_producto($_GET['linea'], $_SESSION['cod_factusol']);
			break;
	}
}
//si le mando el cod hay que insertar un producto
if (isset($_POST['codigo'])) {
	$codigo = 'codigo' . $_POST['codigo'];
	insertar_producto($_POST['codigo'], $_POST['tarifa'], $_SESSION['cod_factusol'], $_POST[$codigo]);
}
//si le paso el num1 hay que actualizar el pedido
if (isset($_POST['num1'])) { actualiza_pedido($_SESSION['cod_factusol']); }
escribir_pedido($_SESSION['cod_factusol']);
?>
</table>
<br />
<div align="center" style="width:100%">
<?php if (existe_pedido($_SESSION['cod_factusol']) == 1) { ?>
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonfinalizarpedido.png" onclick="javascript:document.location.href='fpedido.php';" border="0" style="cursor:pointer" />&nbsp;
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botoncancelarpedido.png" onclick="borrarcli();" border="0" style="cursor:pointer">&nbsp;
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonanadirarticulos.png" onclick="javascript:document.location.href='pproductos.php';" border="0" style="cursor:pointer">&nbsp;
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonnuevopedido.png" onclick="comenzarcli();" border="0" style="cursor:pointer">
<?php
}else{
?>
	<img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonanadirarticulos.png" onclick="javascript:document.location.href='pproductos.php';" border="0" style="cursor:pointer">
<?php
}
?>
</div>
<p><a href="pproductos.php"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolver.png" border="0"></a></p>
</form>
<?php require_once('button.php'); ?>