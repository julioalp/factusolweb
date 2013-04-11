<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'usuario') {
	header('Location: autentifica.php');
	exit();
}
require_once('cmodulos.php');
imodulofact();
cmodulofact();
require_once('conf.inc.php');
require_once('func.php');
?> 
<html>
<head>
<title></title>
<link href="plantillas/<?php echo(PLANTILLA); ?>/estilos/estilo.css" rel="stylesheet" type="text/css" />
<base target="_self">
</head>
<body onLoad="this.focus()">

<table width="644" border="0" align="center">
  <tr>
    <td width="50%"><?php logo(); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="50%"><?php empresa(); ?><br></td>
    <td style="border:1px">
			<table width="80%" border="1" cellpadding="3" cellspacing="0" align="center">
        <tr>
          <td><?php cliente($_GET['tip'], $_GET['cod']); ?></td>
        </tr>
      </table>
		</td>
  </tr>
  <tr>
    <td width="50%"><?php cabecera($_GET['tip'], $_GET['cod']); ?></td>
  </tr>
  <tr>
    <td colspan="2">
			<?php detalle($_GET['tip'], $_GET['cod']); ?>
		</td>
  </tr>
  <tr>
    <td colspan="2"><?php pie($_GET['tip'], $_GET['cod']); ?></td>
  </tr>
  <tr>
    <td colspan="2">
			<hr> 
	    <br><br>
  	  <input type="button" name="imprimir" value="Imprimir" onClick="window.print();"> 
    </td>
  </tr>
</table> 
</body>
</html>
<?php
//funciones
function pie($tip, $cod) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_FAC WHERE TIPFAC=\'' . $tip . '\' AND CODFAC=' . $cod;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['OB1FAC'] != '' or $datos['OB2FAC'] != '') {
		echo'Observaciones:';
		echo'<table width="100%" border="1" cellpadding="0" cellspacing="0">';
		echo'<tr>';
		echo'<td>'.$datos["OB1FAC"].'<br>'.$datos["OB2FAC"].'</td>';
		echo'</tr>';
		echo'</table>';
	}
	if ($datos["OBRFAC"]!="0"){
		$ssql='SELECT * FROM F_DIR WHERE CLIDIR='.$_SESSION["cod_factusol"].' AND CODDIR='.$datos["OBRFAC"];
		$rs2=mysql_query($ssql,$conn);
		$datos2=mysql_fetch_array($rs2);
		if (mysql_num_rows($rs2)!=0){
			echo'<br>Entregar en:';
			echo'<table width="100%" border="1" cellpadding="0" cellspacing="0">';
			echo'<tr>';
			echo'<td>'.$datos2["DOMDIR"].'<br>'.$datos2["CPODIR"]." - ".$datos2["POBDIR"].' - '.$datos2["PRODIR"].'</td>';
			echo'</tr>';
			echo'</table>';
		}
	}
	if ($datos["AATFAC"]!=""){
		echo('<br><p>A la atención de: ' . $datos['AATFAC'] . '</p><br>');
	}
	if ($datos["REAFAC"]!="" or $datos["PEDFAC"]!="" ){
      echo'<table width="100%" border="1" cellpadding="0" cellspacing="0">';
      echo'<tr>';
      if ($datos["REAFAC"]!=""){echo'<td class="cabecera">Referencia</td>';}
      if ($datos["PEDFAC"]!=""){echo'<td class="cabecera">N&deg; de su pedido</td>';}
      if ($datos["FPEFAC"]!="01/01/1900"){echo'<td class="cabecera">Fecha de su pedido</td>';}
      echo('</tr><tr>');
      if ($datos["REAFAC"]!=""){echo'<td>'.$datos["REAFAC"].'</td>';}
      if($datos['PEDFAC'] != '') { echo('<td>' . $datos['PEDFAC'] . '</td>'); }
      if ($datos["FPEFAC"]!="01/01/1900"){echo'<td>'.cambiaf($datos["FPEFAC"]).'</td>';}
  echo'</tr>';
	echo'</table>';
	}
	if ($datos["VENFAC"]!=""){
		echo('<br><p>Vencimientos:<br>');
		$inicio = 0;
		while($cadena = substr($datos['VENFAC'], $inicio, $inicio + 27)) {
			echo(substr($cadena, 0, 10));
			$importe = substr($cadena, strlen($cadena) - 16, 15);
			$importe = str_replace(',', '.', $importe);
			echo(' - Importe: ');
			printf("%.2f", $importe);
			echo('<br>');
			$inicio += 27;
			$cadena = substr($datos['VENFAC'], $inicio, $inicio + 27);
		}
	}
}
function detalle($tip, $cod) {
	//Campos que deven ir en el detalle
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	echo('<table width="100%" border="1" cellspacing="0" cellpadding="0">' . "\r\n");
	echo('<tr>' . "\r\n");
	for($i=1; $i<=12; $i++) {
		if($datos['CA' . $i . 'CFG'] != '') {
			echo('<td align="center" class="cabecera">');
			echo(campos($datos['CA' . $i . 'CFG']));
			echo('</td>' . "\r\n");
		}
	}
	echo('</tr>' . "\r\n");
	$ssql = 'SELECT * FROM F_LFA WHERE TIPLFA=\'' . $tip . '\' AND CODLFA=' . $cod;
	$rs = mysql_query($ssql, $conn);
	while($datos2 = mysql_fetch_array($rs)) {
		echo('<tr>' . "\r\n");
		$b = 0;
		for($i=1; $i<=12; $i++) {
			if($datos['CA' . $i . 'CFG'] != '') {
				if($datos['CA' . $i . 'CFG'] != 'CANLFA' and $datos['CA' . $i . 'CFG'] != 'DT1LFA' and $datos['CA' . $i . 'CFG'] != 'DT2LFA' and $datos['CA' . $i . 'CFG'] != 'DT3LFA' and $datos['CA' . $i . 'CFG'] != 'PRELFA' and $datos['CA' . $i . 'CFG'] != 'TOTLFA' and $datos['CA' . $i . 'CFG'] != 'IVALFA' and $datos['CA' . $i . 'CFG'] != 'POSLFA') {
					echo('<td>');
				}else{
					echo('<td align="right">');
				}
				switch($datos['CA' . $i . 'CFG']) {
					case 'STOLFA':
						if($datos2['ALTLFA'] == 0 and $datos2['ANCLFA'] == 0 and $datos2['FONLFA'] == 0) {
							$volumen = 1;
						}else{
							if($datos2['ALTLFA'] == 0) { $alto = 1; }else{ $alto = $datos2['ALTLFA']; }
							if($datos2['ANCLFA'] == 0) { $ancho = 1; }else{ $ancho = $datos2['ANCLFA']; }
							if($datos2['FONLFA'] == 0) { $fondo = 1; }else{ $fondo = $datos2['FONLFA']; }
							$volumen = $alto * $ancho * $fondo;
						}
						$artpre = $datos2['CANLFA'] * $datos2['PRELFA'] * $volumen;
						fcampos($datos['CA' . $i . 'CFG'], $artpre);
						break;
					case 'TDILFA':
						if($datos2['ALTLFA'] == 0 and $datos2['ANCLFA'] == 0 and $datos2['FONLFA'] == 0) {
							$volumen = 0;
						}else{
							if($datos2['ALTLFA'] == 0) { $alto = 1; }else{ $alto = $datos2['ALTLFA']; }
							if($datos2['ANCLFA'] == 0) { $ancho = 1; }else{ $ancho = $datos2['ANCLFA']; }
							if($datos2['FONLFA'] == 0) { $fondo = 1; }else{ $fondo = $datos2['FONLFA']; }
							$volumen = $alto * $ancho * $fondo;
						}
						fcampos($datos['CA' . $i . 'CFG'], $volumen);
						break;
					default:
						fcampos($datos['CA' . $i . 'CFG'], $datos2[$datos['CA' . $i . 'CFG']]);
						break;
				}
				$b++;
				echo('</td>' . "\r\n");
			}
		}      
		echo('</tr>' . "\r\n");
	}
	$ssql = 'SELECT * FROM F_FAC WHERE TIPFAC=\'' . $tip . '\' AND CODFAC=' . $cod;
	$rs = mysql_query($ssql, $conn);
	$datos2 = mysql_fetch_array($rs);
	echo '<tr>' . "\r\n";
	echo '<td colspan="' . $b . '">' . "\r\n";
	echo '<table width="100%" cellspacing="0" cellpadding="0"><tr>' . "\r\n";
	//escribo la cabecera//
	if ($datos2["PIVA1FAC"]!=0 or $datos2["PIVA2FAC"]!=0 or $datos2["PIVA3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">TIPO</td>' . "\r\n";}
	if ($datos2["IDTO1FAC"]!=0 or $datos2["IDTO2FAC"]!=0 or $datos2["IDTO3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">DESCUENTO</td >' . "\r\n";}
	if ($datos2["IPPA1FAC"]!=0 or $datos2["IPPA2FAC"]!=0 or $datos2["IPPA3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">PRONTO PAGO</td >' . "\r\n";}
	if ($datos2["IPOR1FAC"]!=0 or $datos2["IPOR2FAC"]!=0 or $datos2["IPOR3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">PORTES</td >' . "\r\n";}
	if ($datos2["IFIN1FAC"]!=0 or $datos2["IFIN2FAC"]!=0 or $datos2["IFIN3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">FINANCIACION</td >' . "\r\n";}
	echo '<td class="cabecerapeq" align="left">BASE</td>' . "\r\n";
	if ($datos2["IIVA1FAC"]!=0 or $datos2["IIVA2FAC"]!=0 or $datos2["IIVA3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">I.V.A.</td >' . "\r\n";}
	if ($datos2["IREC1FAC"]!=0 or $datos2["IREC2FAC"]!=0 or $datos2["IREC3FAC"]!=0) { echo '<td class="cabecerapeq" align="left">R.E.</td >' . "\r\n";}
	echo('</tr>');
	// escribo los datos
	for ($i=1;$i<=3;$i++){
		echo'<tr>';       
		if (($datos2["PIVA1FAC"]!=0 or $datos2["PIVA2FAC"]!=0 or $datos2["PIVA3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["PIVA".$i."FAC"]);echo'</td>' . "\r\n"; }
		if (($datos2["IDTO1FAC"]!=0 or $datos2["IDTO2FAC"]!=0 or $datos2["IDTO3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["IDTO".$i."FAC"]);echo'</td>' . "\r\n"; }
		if (($datos2["IPPA1FAC"]!=0 or $datos2["IPPA2FAC"]!=0 or $datos2["IPPA3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["IPPA".$i."FAC"]);echo'</td>' . "\r\n"; }
		if (($datos2["IPOR1FAC"]!=0 or $datos2["IPOR2FAC"]!=0 or $datos2["IPOR3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["IPOR".$i."FAC"]);echo'</td>' . "\r\n";}
		if (($datos2["IFIN1FAC"]!=0 or $datos2["IFIN2FAC"]!=0 or $datos2["IFIN3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["IFIN".$i."FAC"]);echo'</td>' . "\r\n"; }
		if ($datos2["BAS".$i."FAC"]!=0){
			echo '<td>';
			printf("%.2f",$datos2["BAS".$i."FAC"]);
			echo '</td>' . "\r\n";
		}
		if (($datos2["IIVA1FAC"]!=0 or $datos2["IIVA2FAC"]!=0 or $datos2["IIVA3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0) { echo '<td>';printf("%.2f",$datos2["IIVA".$i."FAC"]);echo'</td>' . "\r\n"; }
		if (($datos2["IREC1FAC"]!=0 or $datos2["IREC2FAC"]!=0 or $datos2["IREC3FAC"]!=0) and $datos2["BAS".$i."FAC"]!=0){ echo '<td>';printf("%.2f",$datos2["IREC".$i."FAC"]);echo'</td>' . "\r\n";}
		echo'</tr>' . "\r\n";
	}
	echo'</table>' . "\r\n";
	echo '<table width="100%" cellspacing="0" cellpadding="0"><tr>' . "\r\n";
	echo '<td class="cabecera">';
	if ($datos2["IRET1FAC"]!=0){
		echo 'Retencion: ';
		printf("%.2f",$datos2["IRET1FAC"]);
	}
	echo'</td><td class="cabecera" align="right">Total: ';
	printf("%.2f",$datos2["TOTFAC"]);
	echo' </td></tr></table>' . "\r\n";
	echo '</td>' . "\r\n";
	echo '</tr>' . "\r\n";
	echo '</table>' . "\r\n";
}  
function formapago($tip, $cod) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_FAC WHERE TIPFAC=\'' . $tip . '\' AND CODFAC=' . $cod;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	$ssql = 'SELECT * FROM F_FPA WHERE CODFPA=\'' . $datos['FOPFAC'] . '\'';
	$rs = mysql_query($ssql, $conn);
	$datos2 = mysql_fetch_array($rs);
	return $datos2['DESFPA'];
}
function cabecera($tip,$cod) {
	$conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
	mysql_select_db (BD_DATABASE1);
	$ssql='SELECT * FROM F_FAC WHERE TIPFAC=\''.$tip.'\' AND CODFAC='.$cod;
	$rs=mysql_query($ssql,$conn);
	$datos=mysql_fetch_array($rs);
	echo'<table width="100%" border="0">';
	echo'<tr>';
	echo'<td width="38%">DOCUMENTO</td>';
	echo'<td width="38%">N&Uacute;MERO</td>';
	echo'<td width="24%">FECHA</td>';
	echo'</tr>';
	echo'<tr>';
	echo'<td>Factura</td>';
	echo'<td>'.$tip.'-'.$cod.'</td>';
	echo'<td>'.cambiaf($datos["FECFAC"]).'</td>';
	echo'</tr>';
	echo'</table> </td>';
	echo'<td>&nbsp;</td>';
	echo'</tr>';
	echo'<tr>';
	echo'<td colspan="2"><table width="100%" border="0">';
	echo'<tr>';
	echo'<td>C.I.F/N.I.F</td>';
	echo'<td>AGENTE</td>';
	echo'<td>FORMA DE PAGO</td>';
	echo'<td>ESTADO</td>';
	echo'</tr>';
	echo'<tr>';
	echo'<td>'.$datos["CNIFAC"].'</td>';
	echo'<td>'.$datos["AGEFAC"].'</td>';
	echo('<td>' . formapago($tip, $cod) . '</td>');
	switch ($datos["ESTFAC"]){
		case 0:
			echo '<td>PENDIENTE</td>';
			break;
		case 1:
			echo '<td>PENDIENTE PARCIAL</td>';
			break;
		case 2:
			echo '<td>COBRADA</td>';
			break;
		case 3:
			echo '<td>DEVUELTA</td>';
			break;
		case 4:
			echo '<td>ANULADA</td>';
			break;
	}
/*	if ($datos["ESTFAC"]!=1){
		echo '<td>Pendiente</td>';
	}else{
		echo '<td>Girado</td>';
	}*/
	echo'</tr>';
	echo'</table>';
}
function cliente($tip, $cod) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_FAC WHERE TIPFAC=\'' . $tip . '\' AND CODFAC=' . $cod;
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	echo($datos['CNOFAC'] . '<br>');
	echo($datos['CDOFAC'] . '<br>');
	echo($datos['CCPFAC'] . '      ' . $datos['CPOFAC'] . '<br>');
	echo($datos['CPRFAC'] . '       ' . $datos['CLIFAC']);
}
function logo() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if( ($datos['LOCCFG'] != 1) && ($datos['LOGCFG'] != '') ) {
		echo('<img src="' . $datos['LOGCFG'] . '" />');
	}else{
		echo('<img src="plantillas/' . PLANTILLA . '/imagenes/IND.gif" />');
	}
}
function empresa() {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	if($datos['LOCCFG'] != 0) {
		echo($datos['NOMCFG'] . '<br>');
		echo($datos['DOMCFG'] . '<br>');
		echo($datos['CPOCFG'] . ' ' . $datos['POBCFG'] . '<br>');
		echo($datos['PROCFG'] . '<br>');
		echo($datos['NIFCFG'] . '<br>');
	}
}
//Fin de las funciones
?>