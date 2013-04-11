<?php
require_once('conf.inc.php');
require_once('func.php');
require_once('top.php');
?>

<?php
function escribeusuario($codigo, $cadena, $campos, $valores) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG' ;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if(is_writable('BBDD/' . $datos['CCLCFG'])) {
		if(CLIENTENUMAUT == 'si') {
			$var = escribeuserbd($campos, $valores);
		}else{
			$var = 0;
		}
		$fp = fopen('BBDD/' . $datos['CCLCFG'] . $codigo . '.txt','w');
		$cadena = $var . "\ \n" . $cadena;
		$cadena = str_replace('NOFCLI:', 'SNOMCFG:', $cadena);
		$cadena = str_replace('DOMCLI:', 'SDOMCFG:', $cadena);
		$cadena = str_replace('CPOCLI:', 'SCPOCFG:', $cadena);
		$cadena = str_replace('POBCLI:', 'SPOBCFG:', $cadena);
		$cadena = str_replace('PROCLI:', 'SPROCFG:', $cadena);
		$cadena = str_replace('NIFCLI:', 'SNIFCFG:', $cadena);
		$cadena = str_replace('FPACLI:', 'SFPACFG:', $cadena);
		fwrite($fp, $cadena);
		fclose($fp);
		chmod('BBDD/' . $datos['CCLCFG'] . $codigo . '.txt', 0666);
		return 1;
	}else{
		return 0;
	}
}
function escribeuserbd($campos, $valores) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = "SELECT * FROM F_CLI ORDER BY CODCLI DESC";
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	$ssql = 'SELECT * FROM F_CFG';
	$rs1 = mysql_query($ssql, $conn);
	$datos1 = mysql_fetch_array($rs1);
	$campos .= 'CODCLI, TARCLI, NOCCLI';
	$valores .= ($datos['CODCLI'] + 1) . ', ' . $datos1['TCACFG'] . ', \'' . $_POST['NOFCLI'] . '\'';
	$ssql = 'INSERT INTO F_CLI (' . $campos . ') VALUES (' . $valores . ')';
	$rs = mysql_query($ssql, $conn);
	return ($datos['CODCLI'] + 1);
}
//compruebo si existe el cliente en la bd
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = "SELECT CUWCLI FROM F_CLI WHERE CUWCLI='" . strtolower($_POST['CUWCLI']) . "'";
$rs = mysql_query($ssql, $conn);
$sqlcfg = 'SELECT * FROM F_CFG';
$rscfg = mysql_query($sqlcfg, $conn);
$cfg = mysql_fetch_array($rscfg);
if(mysql_num_rows($rs) != 0) {
	//el usuario ya está dado de alta//
	echo'<p>&nbsp;</p>';
	echo'<p>&nbsp;</p>';
	echo'<p align="center"><b><font size="4">El usuario ya está dado de alta</font></b></p>';
	echo'<p>&nbsp;</p>';
	echo'<p>&nbsp;</p>';
}else {
	//el usuario no existe en la bd
	//ahora compruebo si existe en algún archivo
	if (file_exists('BBDD/'.$cfg["CCLCFG"] . strtolower($_POST['CUWCLI']) . '.txt')){
		//el usuario está pendiente de validación
		echo'<p>&nbsp;</p>';
		echo'<p>&nbsp;</p>';
		echo'<p align="center"><b><font size="4">El usuario que usted introdujo está pendiente de validación</font></b></p>';
		echo'<p>&nbsp;</p>';
		echo'<p>&nbsp;</p>';
	}else{
		//hay que crear el archivo con el usuario y los datos. y la sentencia SQL
		//recorro toda la matriz de campos rellenos
		$campos = '';
		$valores = '';
		$cadena = '';
		foreach($_POST as $nombre_var => $valor_var) {
			$cadena .= $nombre_var . ":" . $valor_var . "\n";
			if($nombre_var != 'Terminado' and substr($nombre_var, (strlen($nombre_var)-3)) != 'CFG') {
				$campos .= $nombre_var . ',';
				$valores .= "'" . $valor_var . "',";
			}
		}
		//escribo el usuario en el archivo de texto.
		$error = escribeusuario(strtolower($_POST['CUWCLI']), $cadena, $campos, $valores);
		if($error != 0) {
			echo'<p>&nbsp;</p>';
			echo'<p>&nbsp;</p>';
			echo'<p align="center"><b><font size="4">Cliente guardado correctamente.</font></b></p>';
			echo'<p>&nbsp;</p>';
			echo'<p>&nbsp;</p>';
		}else{
			echo'<p>&nbsp;</p>';
			echo'<p>&nbsp;</p>';
			echo'<p align="center"><b><font size="4">Ha habido un error en el alta de cliente. Intentelo más tarde.</font></b></p>';
			echo'<p>&nbsp;</p>';
			echo'<p>&nbsp;</p>';
		} 
	}
}
echo'<a href="#"><img src="plantillas/' . PLANTILLA . '/imagenes/botonvolver.png" onclick="javascript:window.history.back()" border ="0"/></a>';
require_once('button.php');
?>