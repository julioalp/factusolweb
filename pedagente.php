<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'agente') {
	header('Location: autentificage.php');
	exit();
}
require_once('func.php');
//meto el cliente en una variable de session
if(isset($_POST['select']) and postseguro($_POST['select']) != -1) { $_SESSION['cliente'] = postseguro($_POST['select']); }
if(isset($_GET['cli'])) {
	echo('');
	$_SESSION['cliente'] = postseguro($_GET['cli']);
	if(postseguro($_GET['borrar']) != 'si') {
		echo("<script>location.href='agproductos.php';</script>");
	}
}
require_once('cmodulos.php');
imodulopeda();
cmodulopeda();
require_once('top.php');
?>

<?php
require_once('menumage.php');
if(postseguro(@$_GET['borrar']) == 'si') { borrar_pedido($_SESSION['cod_factusol'], postseguro($_GET['cli'])); }
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
		$datos1=mysql_fetch_array($rs1);
		$numpedido = $datos1['CODPCL'];
		//BORRO LAS LINEAS DE PEDIDO
		$ssql = 'DELETE FROM F_LPC WHERE TIPLPC=\'' . $datos['SPACFG'] . '\' AND CODLPC=' . $numpedido;
		mysql_query($ssql, $conn);
		//BORRO EL PEDIDO
		$ssql = 'DELETE FROM F_PCL WHERE TIPPCL=\'' . $datos['SPACFG'] . '\' AND CODPCL=' . $numpedido;
		mysql_query($ssql, $conn);         
	}
}
function cliente($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT NOFCLI FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs); 
	return $datos['NOFCLI'];
}
function cliagentes($codagente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
  $sql = 'SELECT NFCCFG FROM F_CFG';
  $rs = mysql_query($sql, $conn);
  $datos = mysql_fetch_array($rs);
	if($datos['NFCCFG'] == 1) {
		$ssql = 'SELECT CODCLI, NOFCLI FROM F_CLI ORDER BY NOFCLI';
	}else{
		$ssql = 'SELECT CODCLI, NOFCLI FROM F_CLI WHERE AGECLI=' . $codagente . ' ORDER BY NOFCLI';
	}
	$rs = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs) != 0) {	//si existe alguno
		echo('clientes: <select name="select" onchange="cliente.submit();">');
		if($cliente != -1) {
			echo('<option value="-1" selected="selected">Todos</option>');
		}else{
			echo('<option value="-1" selected="selected">Todos</option>');
		}
		$selec = '';
		while($datos = mysql_fetch_array($rs)) {
			if($datos['CODCLI'] == $cliente) { $selec = 'selected="selected"'; }
			echo('<option value="' . $datos['CODCLI'] . '" ' . $selec . '>' . $datos['CODCLI'] . " - " . $datos['NOFCLI'] . '</option>');
			$selec = '';
		} 
		echo('</select>');
	}else{
		echo('No existen clientes asociados a este agente comercial');
	}
}
function pedidos_curso($agente, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT SPACFG FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	//busco los pedidos por serie, cliente y estado 
	if($cliente != -1) {
		//si se ha selecciona algún cliente
		$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\' AND CLIPCL=' . $cliente; 
	}else{
		//sino muestro todos    
		$ssql = 'SELECT * FROM F_PCL WHERE AGEPCL=' . $agente . ' AND TIPPCL=\'' . $datos1['SPACFG'] . '\' AND ESTPCL=\'0\'';
	}
	mysql_select_db(BD_DATABASE1);
	$rs = mysql_query($ssql, $conn);
	echo('<table width="100%" border="1" cellspacing="0" cellpadding="1">');
	echo('<tr align="center">');
	echo('<td class="cabecera">Pedido</td>');
	echo('<td class="cabecera">Cliente</td>');
	echo('<td class="cabecera">Fecha</td>');
	echo('<td width="260" class="cabecera">Opciones</td>');
	echo('</tr>');
	//Escribo todos los pedidos asociados al agente que estén pendientes
	if(mysql_num_rows($rs) != 0) {
		while($datos = mysql_fetch_array($rs)) {
			echo('<tr>');
			$numero = $datos['CODPCL'];
			for($i=strlen($datos['CODPCL']) ;$i<=5; $i++) {
				$numero = '0' . $numero;
			}
			echo('<td width="55" align="center">' . $datos['TIPPCL'] . $numero . '</td>');
			echo('<td>' . $datos['CLIPCL'] . ' - ' . cliente($datos['CLIPCL']) . '</td>');
			echo('<td width="70" align="center">' . cambiaf($datos['FECPCL']) . '</td>');
			echo('<td align="right" valign="middle">');
			echo('<a href="pedagente.php?cli=' . $datos['CLIPCL'] . '"><img src="plantillas/' . PLANTILLA . '/imagenes/botonretomarpedido.png" border="0"></a>');
			echo('&nbsp; <a href="#">');
			echo('<img src="plantillas/' . PLANTILLA . '/imagenes/botoncancelarpedido.png" border="0" onclick="borrarpedagente(\'pedagente.php?cli=' . $datos['CLIPCL'] . '&borrar=si\')"></a>');
			echo('</td>');
			echo('</tr>');
		}
	}else{
		echo('<tr>');
		if($cliente != -1) {
			echo('<td colspan="4" align="center">');
			echo('<br>No se han encontrado pedidos en curso para este cliente<br><br>');
			echo('<a href="#"><img src="plantillas/' . PLANTILLA . '/imagenes/botonnuevopedido.png" onclick="javascript:document.location.href=\'agproductos.php\';" border="0"></a></td>');
		}else{
			echo('<td colspan="4" align="center" height="60"><br>');
			echo('<p>No existen pedidos en curso.<br>Seleccione un cliente para realizar un pedido nuevo</p></td>');
		}
		echo'</tr>';      
	}
	echo('</table>');
}
//formulario para búsqueda de pedidos por parte de clientes.
?>
<div class="menucolorfondo">
	<br>
	<form id="form1" name="cliente" method="post" action="pedagente.php">
		Pedidos en curso, filtrado por
<?php
if(!isset($_POST['select'])) {
	cliagentes($_SESSION['cod_factusol'], '-1');
}else{
	cliagentes($_SESSION['cod_factusol'], postseguro($_POST['select']));
}
?>
		<br><br>
	</form>
<?php
//escribo el listado de pedidos.
if(!isset($_POST['select'])) {
	pedidos_curso($_SESSION['cod_factusol'], '-1');                                             
}else{
	pedidos_curso($_SESSION['cod_factusol'], postseguro($_POST['select']));
}
?>
	<br />
</div>
<?php require_once('button.php'); ?>