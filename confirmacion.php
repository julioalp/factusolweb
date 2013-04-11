<?php
session_start();
if($_SESSION['autentificado'] != 'SI') {
	header('Location: index.php');
	exit();
}
require_once('top.php');
?>

<?php
require_once('func.php');
require_once('cmodulos.php');
if($_SESSION['tipo_usuario'] != 'usuario') {
	imodulopeda();
	cmodulopeda();
	require_once('menumage.php');
}else{
	imodulopedc();
	cmodulopedc();
	require_once('menum.php');
}
function encriptar($valor) {
	$encripta1 = ''; 
	$R = strlen(Trim($valor)); 
	for($I=0; $I<$R; $I++) {
		$encripta1 .= chr(ord(substr($valor, $I, 1)) - 2);
	}
	return $encripta1; 
}
function escribearchivo($serie, $numero, $pagos) {
	$linea = NULL;
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	$numerof = $numero;
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_PCL WHERE TIPPCL='$serie' AND CODPCL=$numero";
	$rs2 = mysql_query($ssql, $conn);
	$ssql = "SELECT * FROM F_LPC WHERE TIPLPC='$serie' AND CODLPC=$numero";
	$rs3 = mysql_query($ssql, $conn);
	$numerof = str_pad($numero, 6, '0', STR_PAD_LEFT);
	$codigo = 'pedidofactusolweb' . $serie . $numerof;
	if(file_exists('BBDD/' . $datos['CPVCFG'] . $codigo . '.txt')) {
		return 'Este pedido ya se finaliz&oacute; anteriormente.';
	}else{
		if(is_writable('BBDD/' . $datos['CPVCFG'])) {
			$fp = fopen('BBDD/' . $datos['CPVCFG'] . $codigo . '.txt', 'w');
			$datos2 = mysql_fetch_array($rs2);
			for ($i=0; $i<mysql_num_fields($rs2); $i++) {
				$linea .= mysql_field_name($rs2, $i) . ':' . $datos2[mysql_field_name($rs2, $i)] . "\n";
			}
			while($datos3 = mysql_fetch_array($rs3)) {
				for($i=0; $i<mysql_num_fields($rs3); $i++) {
					$linea .= mysql_field_name($rs3, $i) . ':' . $datos3[mysql_field_name($rs3, $i)] . "\n";
				}
        $artlpc = strval($datos3['ARTLPC']);
        $mqfart = mysql_query('SELECT USTART, CSTART FROM F_ART WHERE CODART=\'' . $artlpc . "'", $conn);
        $mfaart = mysql_fetch_array($mqfart);
				if($mfaart['CSTART'] == 3) {
	        $cantidad = $mfaart['USTART'] - $datos3['CANLPC'];
	        mysql_query('UPDATE F_ART SET USTART=$cantidad WHERE CODART=\'' . $artlpc . "'", $conn);
    		}
			}
			$linea .= 'DOCPAGO:' . encriptar($pagos) . "\n";
			$linea .= 'STATUS:OK';
			$err = fwrite($fp, $linea);
			fclose($fp);
			if ($err != -1) {
				chmod('BBDD/' . $datos['CPVCFG'] . $codigo . '.txt', 0666);
				return '';
			}else{
				return 'No dispone de permisos para generar el pedido.';
			}      
		}else{
			return 'No se puede generar pedidos.';
		}
	}
}
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
//Escribo los datos de la forma de pago
$pagos = '';
if(isset($_POST['banco']) or isset($_POST['tipo'])) {
	if(isset($_POST['banco'])) {
		$pagos = $_POST['banco'] . $_POST['sucursal'] . $_POST['dc'] . $_POST['cuenta'];
	}else{
		$pagos = $_POST['tipo'] . '-' . $_POST['numtarjeta'] . '-' . $_POST['codseg'] . ' Cad:' . $_POST['mes'] . '/' . $_POST['anio'];
	}
}
//escribo los datos que faltan en el pedido
if($_SESSION['tipo_usuario'] != 'agente') {
	if($_POST['entrega'] != '') {
		$ssql = 'UPDATE F_PCL SET FECPCL=\'' . date("Y-m-d H:i:s") . '\', DIRPCL=' . $_POST['entrega'] . ', REFPCL=\'' . $_POST['referencia'] . '\', OB1PCL=\'' . $_POST['observaciones'] . '\', PPOPCL=\'' . $_POST['pedidopor'] . '\' WHERE TIPPCL=\'' . $_POST['serie'] . '\' AND CODPCL=' . $_POST['numero'];
	}else{
		$ssql = 'UPDATE F_PCL SET FECPCL=\'' . date("Y-m-d H:i:s") . '\', REFPCL=\'' . $_POST['referencia'] . '\', OB1PCL=\'' . $_POST['observaciones'] . '\', PPOPCL=\'' . $_POST['pedidopor'] . '\' WHERE TIPPCL=\'' . $_POST['serie'] . '\' AND CODPCL=' . $_POST['numero'];
	}
}else{
	if($_POST['entrega'] != '') {
		$ssql = 'UPDATE F_PCL SET FECPCL=\'' . date("Y-m-d H:i:s") . '\', DIRPCL=' . $_POST['entrega'] . ', REFPCL=\'' . $_POST['referencia'] . '\', ALMPCL=\'' . $_POST['almacen'] . '\', OB1PCL=\'' . $_POST['observaciones'] . '\', PPOPCL=\'' . $_POST['pedidopor'] . '\' WHERE TIPPCL=\'' . $_POST['serie'] . '\' AND CODPCL=' . $_POST['numero'];
	}else{
		$ssql = 'UPDATE F_PCL SET FECPCL=\'' . date("Y-m-d H:i:s") . '\', REFPCL=\'' . $_POST['referencia'] . '\', ALMPCL=\'' . $_POST['almacen'] . '\', OB1PCL=\'' . $_POST['observaciones'] . '\', PPOPCL=\'' . $_POST['pedidopor'] . '\' WHERE TIPPCL=\'' . $_POST['serie'] . '\' AND CODPCL=' . $_POST['numero'];
	}
}
$rs = mysql_query($ssql, $conn);
$ssql = "SELECT * FROM F_PCL WHERE TIPPCL='" . $_POST['serie'] . "' AND CODPCL=" . $_POST['numero'];
$rs = mysql_query($ssql, $conn);
$prueba = mysql_fetch_array($rs);
//escribo el archivo y si todo está correcto le cambio el estado a enviado.
?>
<div class="menucolorfondo">
<?php
if(isset($_POST['serie']) and isset($_POST['numero'])) {
	$valorescribearchivo = escribearchivo($_POST['serie'], $_POST['numero'], $pagos);
	if($valorescribearchivo == '') {
		//Todo correcto
		$ssql = 'UPDATE F_PCL SET ESTPCL=\'1\' WHERE TIPPCL=\'' . $_POST['serie'] . '\' AND CODPCL=' . $_POST['numero'];
		mysql_query($ssql, $conn);
?>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<table width="70%" border="0" align="center">
		<tr>
			<td align="center">El Pedido ha sido procesado correctamente.</td>
		</tr>
	</table>
	<p>&nbsp;</p>
<?php }else{ ?>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<table width="70%" border="0" align="center">
		<tr>
			<td align="center"><?=$valorescribearchivo?></td>
		</tr>
	</table>
	<p>&nbsp;</p>
<?php
	}
}else{
?>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<table width="70%" border="0" align="center">
		<tr>
			<td align="center">No hay pedidos que procesar.</td>
		</tr>
	</table>
	<p>&nbsp;</p>
<?php
}
if($_SESSION['tipo_usuario'] == 'usuario') {
?>
	<a href="#"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolveralmenu.png" onclick="javascript:document.location.href='cliente.php';" border="0"></a>
<?php }else{ ?>
	<a href="#"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolveralmenu.png" onclick="javascript:document.location.href='agente.php';" border="0"></a>
<?php } ?>
</div>
<?php require_once('button.php'); ?>