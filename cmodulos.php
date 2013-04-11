<?php
require_once('conf.inc.php');
function cmodulofact() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG' ;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['ESM4CFG'] == 0) {
		echo($datos['TXM4CFG']);
		mysql_close($conn);
		exit();
	}
	mysql_close($conn);
}
function cmoduloart() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['ESM1CFG'] == 0) {
		echo($datos['TXM1CFG']);
		mysql_close($conn);
		exit();
	}
	mysql_close($conn);
}
function cmodulopedc() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['ESM2CFG'] == 0) {
		echo($datos['TXM2CFG']);
		mysql_close($conn);
		exit();
	}
	mysql_close($conn);
}
function cmodulopeda() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['ESM3CFG'] == 0) {
		echo($datos['TXM3CFG']);
		mysql_close($conn);
		exit();
	}
	mysql_close ($conn);
}
function imodulofact() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG' ;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if ($datos['PCFCFG'] == 0) {
		mysql_close($conn);
		echo('<br>&nbsp;Actualmente, la consulta de facturas está deshabilitada.');
		exit();
	}
	mysql_close ($conn);
}
function imoduloart() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['MICCFG'] == 0 && LOGEADO == 'no') {
		echo('<br>&nbsp;Actualmente, la consulta de artículos está deshabilitada.');
		mysql_close($conn);
		exit();
	}
	mysql_close($conn);
}
function imodulopedc() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['PPCCFG'] == 0) {
		echo('<br>&nbsp;Actualmente, los pedidos están deshabilitados.');
		mysql_close($conn);
		exit();
	}
	mysql_close($conn);
}
function imodulopeda() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['PPACFG'] == 0) {
		echo('<br>&nbsp;Actualmente, los pedidos están deshabilitados.');
		mysql_close($conn);
		exit();
	}
	mysql_close ($conn);
}
?>