<?php
function better_mysql_num_rows($conn, $ssql) {
	$result = mysql_query($ssql, $conn);
	$count = 0;
	while($datos = mysql_fetch_array($result)) {
		$count++;
	}
	return $count;
}
function obtienesubconsulta($ssql, $aux) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$result = mysql_query($ssql, $conn);
	$temp = $aux . '-1' . $aux;
	while($datos = mysql_fetch_array($result)) {
		$temp .= ',' . $aux . $datos['CODFAC'] . $aux;
	}
	return $temp;
}
function saltolinea($cadena) {
	$cadena = str_replace(chr(13), '<br>', $cadena);                                                                                
	return $cadena;
}
function quitardir($dir) {
	//funcion que quita el primer directorio de la ruta de las imagenes ya que no es el directorio web
	$dir = substr($dir, 1);
	for($i=2; $i<strlen($dir); $i++) {
		if(substr($dir, 1, 1) == '/') {
			return substr($dir, 1);
		}else{
			$dir = substr($dir, 1);
		}
	}
}
//lista factura de clientes entre dos fechas
function listarFact($fecha1, $fecha2, $cliente, $opcion) {
	//la fecha en odbc se guarda dd/mm/aaaa
	//por lo que nuestras fechas debemos formatearlas
	switch($opcion) {
		case 1:
			$mensaje = 'Listado de todas las facturas';
			break;
		case 2:
			$mensaje = 'Listado de Facturas de esta semana';
			break;
		case 3:
			$mensaje = 'Listado de facturas del mes de ' . mes(date(m, time()));
			break;
		case 4:
			if(date(m, time()) != 1) {
				$mes = date(m, time()) - 1;
			}else{
				$mes = 12;
			}
			$mensaje = 'Listado de facturas del mes de ' . mes($mes);
			break;
		case 5:
			$mensaje = 'Listado de Facturas entre ' . date("d/m/Y", $fecha1) . ' y ' . date("d/m/Y", $fecha2);
			break;
	}
	//controlar mysql y access
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs2 = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs2);
	$fecha1 = date("Y-m-d", $fecha1);
	$fecha2 = date("Y-m-d", $fecha2);
	$ssql = 'SELECT * FROM F_FAC WHERE CLIFAC="' . $cliente . '" AND (FECFAC>= "' . $fecha1 . '" AND FECFAC<="' . $fecha2 . '")';
	$rs = mysql_query($ssql, $conn);
	//Si por lo menos hay un resultado
	if(mysql_num_rows($rs) != 0) {
		escribecabecera($mensaje);
		$colorcelda = COLOROSCUROCELDA;
		while($datos2 = mysql_fetch_array($rs)) {
			if($colorcelda == COLORCLAROCELDA) {
				$colorcelda = COLOROSCUROCELDA;
			}else{
				$colorcelda = COLORCLAROCELDA;
			}
			escfac($datos2['CODFAC'] ,$datos2['TIPFAC'], $colorcelda);
		}
	}else{
		escribecabecera($mensaje);
		nodatos();
	}
	echo('</table>');
}
//lista las últimas 10 facturas  
function listarfac2($cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	$ssql = 'SELECT * FROM F_CFG';
	$rs2 = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs2);
	$ssql = 'SELECT * FROM F_FAC WHERE  CLIFAC="' . $cliente . '" ORDER BY  FECFAC DESC LIMIT 10';
	$rs = mysql_query($ssql, $conn);
	//Comprobamos que por lo menos haya un registro
	if(mysql_num_rows($rs) != 0) {
		//comprobamos si tiene 10 datos
		if(mysql_num_rows($rs) >= 10) {
			escribecabecera('Listado 10 últimas Facturas');
			$colorcelda = COLOROSCUROCELDA;
			for($i=0; $i<=9; $i++) {
				if($colorcelda == COLORCLAROCELDA) {
					$colorcelda = COLOROSCUROCELDA;
				}else{
					$colorcelda = COLORCLAROCELDA;
				}
				//ecribo la factura
				$datos = mysql_fetch_array($rs);
				escfac($datos['CODFAC'], $datos['TIPFAC'], $colorcelda);
			 }
		}else{
			escribecabecera('Listado 10 últimas Facturas');
			$colorcelda = COLOROSCUROCELDA;
			while($datos = mysql_fetch_array($rs)) {
				if($colorcelda == COLORCLAROCELDA) {
					$colorcelda = COLOROSCUROCELDA;
				}else{
					$colorcelda = COLORCLAROCELDA;
				}
				//Escribo la tabla con los datos
				escfac($datos['CODFAC'], $datos['TIPFAC'], $colorcelda);
			}
		}
	}else{
		//no se han encontrado datos
		escribecabecera("");
		nodatos();
	}
	echo('</table>');
}
//escribe las facturas pasandole el numero de facturas y la serie
function escfac($codfac, $serie, $colorcelda) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);   
	$ssql = 'SELECT * FROM F_FAC WHERE CODFAC=' . $codfac . ' AND TIPFAC=\'' . $serie . '\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//comvierto la fecha
	$fecha = cambiaf($datos['FECFAC']);
	//escribo los datos en la tabla
	echo('<tr>');
	echo('<td align="center" bgcolor="' . $colorcelda . '">');
	echo('<img src="plantillas/' . PLANTILLA . '/imagenes/ver.gif" onclick="Abrir_Ventana2(\'dfactura.php?tip=' . $datos['TIPFAC'] . '&cod=' . $datos['CODFAC'] . '\',\'850\',\'675\')" border="0" alt="Ver Factura" style="cursor:pointer">');
	echo('</td>');
	echo('<td align="left" bgcolor="' . $colorcelda . '">' . $datos['TIPFAC'] . '-' . $datos['CODFAC'] . '</td>');
	echo('<td align="left" bgcolor="' . $colorcelda . '">' . $fecha . '</td>');
	switch($datos['ESTFAC']) {
		case 0:
			echo('<td align="left" bgcolor="' . $colorcelda . '">Pendiente</td>');
			break;
		case 1:
			echo('<td align="left" bgcolor="' . $colorcelda . '">Pendiente Parcial</td>');
			break;
		case 2:
			echo('<td align="left" bgcolor="' . $colorcelda . '">Cobrado</td>');
	}
	echo('<td align="right" bgcolor="' . $colorcelda . '">');
	printf("%.2f", ($datos['BAS1FAC'] + $datos['BAS2FAC'] + $datos['BAS3FAC']));
	echo('</td>');
	echo('<td align="right" bgcolor="' . $colorcelda . '">');
	printf("%.2f", ($datos['IIVA1FAC'] + $datos['IIVA2FAC'] + $datos['IIVA3FAC']));
	echo('</td>');
	echo('<td align="right" bgcolor="' . $colorcelda . '">');
	printf("%.2f", ($datos['IREC1FAC'] + $datos['IREC2FAC'] + $datos['IREC3FAC']));
	echo('</td>');
	echo('<td align="right" bgcolor="' . $colorcelda . '">');
	printf("%.2f", $datos['IRET1FAC']);
	echo('</td>');
	echo('<td align="right" bgcolor="' . $colorcelda . '">');
	printf("%.2f", $datos['TOTFAC']);
	echo('</td>');
	echo('</td>');
	$datos = NULL;
}
function campos($codcampo) {
	//Devuelve el texto de la cabecera
	switch($codcampo) {
		case 'POSLFA':
			return 'Posición';
			break;
		case 'ARTLFA':
			return 'Artículo';
			break;
		case 'DESLFA':
			return 'Descripción';
			break;
		case 'CANLFA':
			return 'Cantidad';
			break;
		case 'DT1LFA':
			return '% Dto.';
			break;
		case 'DT2LFA':
			return '% Dto. 2';
			break;
		case 'DT3LFA':
			return '% Dto. 3';
			break;
		case 'PRELFA':
			return 'Precio Unitario';
			break;
		case 'TOTLFA':
			return 'Total';
			break;
		case 'IVALFA':
			return 'Tipo de IVA';
			break;
		case 'IINLFA':
			return 'IVA INCLUIDO';
			break;
		case 'DOCLFA':
			return 'Documento que creó el pedido';
			break;
		case 'DTPLFA':
			return 'Tipo de Dopcumento';
			break;
		case 'DCOLFA':
			return 'Codigo del documento';
			break;
		case 'BULLFA':
			return 'Bultos';
			break;
		case 'COMLFA':
			return 'Comision del Agente';
			break;
		case 'MEMLFA':
			return 'Número de serie Lote';
			break;
		case 'ALTLFA':
			return 'Alto';
			break;
		case 'ANCLFA':
			return 'Ancho';
			break;
		case 'FONLFA':
			return 'Fondo';
			break;
		case 'FFALFA':
			return 'Fecha Fabricaci&oacute;n';
			break;
		case 'FCOLFA':
			return 'Fecha de consumo preferente';
			break;
		case 'EANART':
			return 'C&oacute;digo de barras';
			break;
		case 'STOLFA':
			return 'Subtotal';
			break;
		case 'TDILFA':
			return 'Dimensi&oacute;n total';
			break;
		}
}
function fcampos($codcampo, $valor) {
	//Devuelve el texto de la cabecera
	switch($codcampo) {
		case 'POSLFA':
			printf("%.0f", $valor);
			break;
		case 'ARTLFA':
			echo $valor;
			break;
		case 'DESLFA':
			echo $valor;
			break;
		case 'CANLFA':
			printf("%.2f", $valor);
			break;
		case 'DT1LFA':
			printf("%.2f", $valor);
			break;
		case 'DT2LFA':
			printf("%.2f", $valor);
			break;
		case 'DT3LFA':
			printf("%.2f", $valor);
			break;
		case 'PRELFA':
			printf("%.2f", $valor);
			break;
		case 'TOTLFA':
			printf("%.2f", $valor);
			break;
		case 'IVALFA':
			printf("%.2f", tiposiva($valor));
			break;
		case 'IINLFA':
			if($valor != 0) {
				echo('INCLUIDO');
			}else{
				echo('No INCLUIDO');
			}
			break;
		case 'DOCLFA':
			if($valor == 'P') {
				echo('Presupuesto');
			}else{
				if($valor == 'C') {
					echo('Cliente');
				}else{
					if($valor == 'A') { echo('Albarán'); }
				}
			}
			break;
		case 'DTPLFA':
			return 'Tipo de Documento';
			break;
		case 'DCOLFA':
			return 'Codigo del documento';
			break;
		case 'BULLFA':
			printf("%.2f", $valor);
			break;
		case 'COMLFA':
			printf("%.2f", $valor);
			break;
		case 'MEMLFA':
			echo $valor;
			break;
		case 'ALTLFA':
			printf("%.2f", $valor);
			break;
		case 'ANCLFA':
			printf("%.2f", $valor);
			break;
		case 'FONLFA':
			printf("%.2f", $valor);
			break;
		case 'FFALFA':
			echo(cambiaf($valor));
			break;
		case 'FCOLFA':
			echo(cambiaf($valor));
			break;
		case 'EANART':
			echo(@$valor);
			break;
		case 'STOLFA':
			printf("%.2f", $valor);
			break;
		case 'TDILFA':
			printf("%.2f", $valor);
			break;
   }   
}   
function cambiaf($fecha) {
	ereg("([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha); 
	$lafecha = $mifecha[3] . "/" . $mifecha[2] . "/" . $mifecha[1];
	return $lafecha;
}   
function escribecabecera($titulo) {
	echo('<br>');
	echo('<h3 align="center"><strong>' . $titulo . '</strong></h3>');
	echo('<div class="menucolorfondo">');
	echo('<table width="100%" border="1" cellspacing="0" cellpadding="0">');
	echo('<tr>');
	echo('<th scope="col" width="70" class="cabecera">Ver Factura </th>');
	echo('<th scope="col" class="cabecera">Serie - n&uacute;mero </th>');
	echo('<th scope="col" class="cabecera">Fecha factura </th>');
	echo('<th scope="col" class="cabecera">Estado</th>');
	echo('<th scope="col" class="cabecera">Base Imponible </th>');
	echo('<th scope="col" class="cabecera">IVA</th>');
	echo('<th scope="col" class="cabecera">RE</th>');
	echo('<th scope="col" class="cabecera">Retenciones</th>');
	echo('<th scope="col" class="cabecera">Total </th>');
	echo('</tr>');
} 
function nodatos() {
	echo('<tr>');
	echo('<td align="center" valign="middle" height="25" colspan="9">No hay facturas con esos parametros de b&uacute;squeda</td>');
	echo('</tr>');
}
function tiposiva($tipo){
	$conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
	mysql_select_db (BD_DATABASE1); 
	$ssql= 'SELECT * FROM F_CFG';
	$rs=mysql_query($ssql,$conn);
	$datos=mysql_fetch_array ($rs);
	switch ($tipo){
		case 0:
			return $datos["PIV1CFG"];
			break;
		case 1:
			return $datos["PIV2CFG"];
			break;
		case 2:
			return $datos["PIV3CFG"];
			break;
	 }      
}
function mes($mes) {     
	switch ($mes){
		case 1: return ("Enero");
		case 2: return ("Febrero");
		case 3: return ("Marzo");
		case 4: return ("Abril");
		case 5: return ("Mayo");
		case 6: return ("Junio");
		case 7: return ("Julio");
		case 8: return ("Agosto");
		case 9: return ("Septiembre");
		case 10: return ("Octubre");
		case 11: return ("Noviembre");
		case 12: return ("Diciembre");
	}
}
function preciodesc($codart, $cliente) {
	$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
	mysql_select_db(BD_DATABASE1);
	//descuento fijo
	$ssql = 'SELECT DT1CLI, TARCLI FROM F_CLI WHERE CODCLI=' . $cliente;
	$rs = mysql_query($ssql, $conn);
	$ddesc = mysql_fetch_array($rs);
	//datos del articulo
	$ssql = 'SELECT * FROM F_ART WHERE CODART=\'' . $codart . '\'';
	$rs = mysql_query($ssql, $conn);
	$datos = mysql_fetch_array($rs);
	//datos de la tarifa tengo que saber si es autentificado o no
	$ssql = 'SELECT TCACFG FROM F_CFG WHERE DS1CFG <> ""';
	$rs2 = mysql_query($ssql, $conn);
	$cfg = mysql_fetch_array($rs2);
	if($cliente != '-1') {
		$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $cliente;
	}else{
		$ssql = 'SELECT PRELTA FROM F_LTA WHERE TARLTA=' . $cfg['TCACFG'] . ' AND ARTLTA=\'' . $codart . '\'';
		$rs3 = mysql_query($ssql, $conn);
		$precio = mysql_fetch_array($rs3);
		return ($precio['PRELTA']);
	}
	$rs2 = mysql_query($ssql, $conn);
	$tarifa = mysql_fetch_array($rs2);
	$tarifaCliente = ($ddesc['TARCLI'] != 0) ? ($ddesc['TARCLI']) : ($cfg['TCACFG']);
	//Ya tengo el articulo y la tarifa solo me falta saber el precio
	$ssql = 'SELECT PRELTA FROM F_LTA WHERE TARLTA=' . $tarifaCliente . ' AND ARTLTA=\'' . $codart . '\'';
	$rs3 = mysql_query($ssql, $conn);
	$precio = mysql_fetch_array($rs3);
	//Ya tenemos todos los datos
	//Ahora vamos a ver el descuento
	$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $tarifa['TCLCLI'] . '\' AND ARFDES=\'' . $codart . '\'';
	$rs5 = mysql_query($ssql, $conn);
	if(mysql_num_rows($rs5) != 0) {
		$descuento = mysql_fetch_array($rs5);
		if(($descuento['IMPDES'] . '') != '' and ($descuento['IMPDES'] . '') != '0') {
			return $precio['PRELTA'] - $descuento['IMPDES'];
		}else{
			return ($precio['PRELTA'] - ($precio['PRELTA'] * $descuento['PORDES'] / 100));
		}
	}else{
		$ssql = 'SELECT * FROM F_DES WHERE TCLDES=\'' . $tarifa['TCLCLI'] . '\' AND ARFDES=\'' . $datos['FAMART'] . '\'';
		$rs5 = mysql_query($ssql, $conn);
		if(mysql_num_rows($rs5) != 0) {
			$descuento = mysql_fetch_array($rs5); 
			return ($precio['PRELTA'] - ($precio['PRELTA'] * $descuento['PORDES'] / 100));
		}else{
			return ($precio['PRELTA']);
		}
	}
}
function espredefinida($tarifa, $cliente) {
   $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
   mysql_select_db (BD_DATABASE1);
   $ssql = 'SELECT * FROM F_CLI WHERE CODCLI='.$cliente;
   $rs= mysql_query ($ssql,$conn);
   $datos=mysql_fetch_array ($rs);
   if ($datos["TARCLI"] != $tarifa){
      return 1;
   }else{
      return 0;
   }  
}      
function decimal($numero) {
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql='SELECT NUMUNIAUT FROM F_AUT';
      $rs5=mysql_query($ssql,$conn);
      $decimal=mysql_fetch_array($rs5);
      return number_format($numero, $decimal["NUMUNIAUT"], '.', '');
}
function decimales() {
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql='SELECT NUMUNIAUT FROM F_AUT';
      $rs5=mysql_query($ssql,$conn);
      $decimal=mysql_fetch_array($rs5);
      return $decimal["NUMUNIAUT"];
}
function postseguro($valor) {
	$final = addslashes(htmlspecialchars(strip_tags($valor)));
	return $final;
}
?>