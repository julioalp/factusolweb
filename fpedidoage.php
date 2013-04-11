<?
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'agente') {
	header('Location: autentificage.php');
	exit();
}
require_once("cmodulos.php");
require_once('func.php');
imodulopeda();
cmodulopeda();
require_once('top.php');
?>

<?php
require_once('menumage.php');
require_once('conf.inc.php');
$conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
mysql_select_db(BD_DATABASE1);
$ssql = 'SELECT * FROM F_CLI WHERE CODCLI=' . $_SESSION['cliente'];
$rs = mysql_query($ssql, $conn);
$datos = mysql_fetch_array($rs); 
?>
<h3 align="center">Confirmaci&oacute;n del pedido para el cliente: <?=$datos['NOFCLI']?></h3>
<form id="form1" name="form1" method="post" action="confirmacion.php" class="menucolorfondo">
  <table width="100%" border="1">
    <tr>
      <td>
				<p>RESUMEN PEDIDO</p>
      	<table align="center" border="1" bordercolor="#000000" cellpadding="1" cellspacing="0">
        	<tr bordercolor="#999999" bgcolor="#cccccc">
						<th height="20">Cod</th>
						<th>Concepto</th>
						<th>Precio</th>
						<th>Unds</th>
						<th>Descuento</th>
						<th>Total</th>
					</tr>
<? escribir_pedido($_SESSION["cliente"]); ?>
				</table>
			</td>
		</tr>
		<tr>
			<td>Almacen 
<? almacen(); ?>    
			</td>
		</tr>
		<tr>
			<td><p>DIRECCION DE ENTREGA 
        <?
            //direcciones de la tabla de direcciones de clientes

            $ssql='SELECT * FROM F_DIR WHERE CLIDIR='. $_SESSION["cliente"];
            $rs=mysql_query($ssql,$conn);
            if (mysql_num_rows($rs)!=0){
                  echo ' <select name="entrega">';
                  while ($datos=mysql_fetch_array($rs)){
                        echo '<option value="'.$datos["CODDIR"].'">'.$datos["DOMDIR"].'</option>';      
                  }
                  echo'</select>';
            }else{
                  echo '<input type="hidden" name="entrega" id="entrega" value="">';
            }
        ?>
      </p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td><p>FORMA DE PAGO: 
        <?
            //Tipo de forma de pago
            $ssql='SELECT * FROM F_CLI WHERE CODCLI='.$_SESSION["cliente"];
            $rs=mysql_query($ssql,$conn);
            $datos=mysql_fetch_array($rs);
            $pago=$datos["FPACLI"];
            //Caracteristicas del tipo de pago
            $ssql='SELECT * FROM F_FPA WHERE CODFPA=\''.$pago.'\'';
            $rs=mysql_query($ssql,$conn);
            $datos=mysql_fetch_array($rs);
            echo $datos["DESFPA"].'<p>';
            //forma de pago asignada al cliente
            //tengo que ver si tengo que pedir m�s datos.
            switch ($datos["TIPFPA"]){
                  case 0: //no solicito m�s datos
                        break;
                  case 1:
                        echo'C.C.C.';
                        echo'<INPUT TYPE="text" NAME="banco" MAXLENGTH="4" SIZE="4">';
                        echo'<INPUT TYPE="text" NAME="sucursal" MAXLENGTH="4" SIZE="4">';
                        echo'<INPUT TYPE="text" NAME="dc" MAXLENGTH="2" SIZE="2">';
                        echo'<INPUT TYPE="text" NAME="cuenta" MAXLENGTH="10" SIZE="10">';
                        break;
                  case 2:
                        echo'Tipo de Tarjeta: ';                
                        echo'<select  name="tipo">';
				echo'<option value="VISA">Visa';
				echo'<option value="MASTERCARD">MasterCard';
				echo'<option value="AMEX">American Express';
                        echo'</select><br>';
                        echo'Numero: <input type="text" Name="numtarjeta" MAXLENGTH="16" SIZE="16"> Cod. Seguridad: <input type="text" Name="codseg" MAXLENGTH="3" SIZE="3"> <br>';
                        echo'Fecha de Caducidad: Mes ';
                        echo'<select  name="mes">';
				echo'<option value="01">01';
				echo'<option value="02">02';
				echo'<option value="03">03';
				echo'<option value="04">04';
				echo'<option value="05">05';
				echo'<option value="06">06';
				echo'<option value="07">07';
				echo'<option value="08">08';
				echo'<option value="09">09';
				echo'<option value="10">10';
				echo'<option value="11">11';
				echo'<option value="12">12';
			echo'</select>';
			echo ' A&ntilde;o ';
                  $year=date("Y");
			echo'<select  name="anio">';
				echo'<option value="'.$year.'">'.$year;
				echo'<option value="'.($year+1).'">'.($year+1);
				echo'<option value="'.($year+2).'">'.($year+2);
				echo'<option value="'.($year+3).'">'.($year+3);
				echo'<option value="'.($year+4).'">'.($year+4);
				echo'<option value="'.($year+5).'">'.($year+5);
				echo'<option value="'.($year+6).'">'.($year+6);
				echo'<option value="'.($year+7).'">'.($year+7);
				echo'<option value="'.($year+8).'">'.($year+8);
				echo'<option value="'.($year+9).'">'.($year+9);
				echo'<option value="'.($year+10).'">'.($year+10);
				echo'</select>';
            }
        ?>
      </p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td><p>OTROS</p>
        <p>Referencia: 
          <input name="referencia" type="text" id="referencia" size="12" maxlength="12" />
        </p>
        <p>Observaciones: 
          <input name="observaciones" type="text" id="observaciones" size="50" maxlength="50" />
        </p>
      <p>Pedido Realizado por: 
        <input name="pedidopor" type="text" size="40" maxlength="40" />
      </p></td>
    </tr>
	</table>
	<table border="0" width="100%">
		<tr>
			<td><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolver.png" onClick="javascript:window.history.back()" border="0" style="cursor:pointer"></td>
			<td align="right"><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonfinalizarpedido.png" onClick="form1.submit();" border="0" style="cursor:pointer"/></td>
		</tr>
	</table>
</form>
<?php
function almacen()
{
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql="SELECT * FROM F_ALM";
      $rs=mysql_query($ssql,$conn);
      if (mysql_num_rows($rs)!=0){
            echo'<select name="almacen" >';
            while ($datos=mysql_fetch_array($rs)){
                  echo'<option value="'.$datos["CODALM"].'">'.$datos["NOMALM"].'</option>';
            }
      }
}
function recargo ($cliente)
{
     $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql='SELECT * FROM F_CLI WHERE CODCLI='.$cliente;
      $rs=mysql_query($ssql,$conn);
      $datos=mysql_fetch_array($rs);
      if ($datos["REQCLI"]==0){
            return 0; //no tiene
      }else{
            return 1; //si tiene
      }
  //Averiguar si el cliente tiene recargo
}	
function escribir_bases($cliente)
{
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      //busco la serie para pedidos de clientes
      $ssql='SELECT * FROM F_CFG';
      $rs1=mysql_query($ssql,$conn);
      $datos1=mysql_fetch_array($rs1);
      mysql_select_db (BD_DATABASE1);
      //busco los pedidos por serie, cliente y estado      
      $ssql='SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1["SPACFG"] . '\' AND ESTPCL=\'0\'';
      $rs=mysql_query($ssql,$conn);
      if (mysql_num_rows($rs)!=0){
            $datos=mysql_fetch_array ($rs);
            //cojo el detalle del pedido por orden de lineas
            $ssql='SELECT * FROM F_LPC WHERE CODLPC=' . $datos["CODPCL"] . ' AND TIPLPC=\'' . $datos["TIPPCL"] . '\' ORDER BY POSLPC ASC'; 
            $rs1=mysql_query($ssql,$conn);
            if (mysql_num_rows($rs1)!=0){
            $base1=0;
            $iva1=0;
            $base2=0;
            $iva2=0;
            $base3=0;
            $iva3=0;
            $desc1=0;
            $desc2=0;
            $desc3=0;
            $ppago1=0;
            $ppago2=0;
            $ppago3=0;
            $finan1=0;
            $finan2=0;
            $finan3=0;
            $re1=0;
            $re2=0;
            $re3=0;
            $neto1=0;
            $neto2=0;
            $neto3=0;
                  while ($linea=mysql_fetch_array ($rs1)){
                        switch (iva($linea["ARTLPC"],$cliente)){
                              //cliente exento de iva
                              case -1:
                                    //if ($linea["IINLPC"]!=0){
                                    //      $iva=ivaart($linea["ARTLPC"]);
									
                                    	  $base1=$base1+number_format($linea["TOTLPC"],2,'.',''); //$base1+(number_format($linea["TOTLPC"],2,'.','')/(1+($iva/100)));
//										  echo number_format($linea["TOTLPC"],2,'.','');
                                    //}else{
                                    //      $base1=$base1+number_format($linea["TOTLPC"],2,'.','');
                                    //}
                                    $neto1=$neto1+number_format($linea["TOTLPC"],2,'.','');
                                    $desc1=$desc1+($base1*$datos["PDTO1PCL"]/100);
                                    $ppago1=$ppago1+($base1*$datos["PPPA1PCL"]/100);
                                    $finan1=$finan1+($base1*$datos["PFIN1PCL"]/100);
                                    /*if (recargo($_SESSION["cliente"])){
                                          $re1=$re1+($base1*$datos["PREC1PCL"]/100);
                                    }*/
                                    break;
                              case 0:
                                    
                                    if ($linea["IINLPC"]!=0){
                                          $iva=ivaart($linea["ARTLPC"]);
                                          $base=number_format($linea["TOTLPC"],2,'.','')/(1+($iva/100));
                                    }else{
                                          $base=number_format($linea["TOTLPC"],2,'.','');
                                    }
                                    $neto1=$neto1+$base;
                                    $desc1=$desc1+($base*$datos["PDTO1PCL"]/100);
                                    $ppago1=$ppago1+($base*$datos["PPPA1PCL"]/100);
                                    $finan1=$finan1+($base*$datos["PFIN1PCL"]/100);
                                    $base1=$base1+($base-($base*$datos["PDTO1PCL"]/100)-($base*$datos["PPPA1PCL"]/100)+($base*$datos["PFIN1PCL"]/100));
                                    $iva1= $iva1+($base*$datos1["PIV1CFG"]/100);
                                    if (recargo($_SESSION["cliente"])){
                                          $re1=$re1+($base*$datos["PREC1PCL"]/100);
                                    }
                                    break;
                              case 1:
                                    if ($linea["IINLPC"]!=0){
                                          $iva=ivaart($linea["ARTLPC"]);
                                          $base=number_format($linea["TOTLPC"],2,'.','')/(1+($iva/100));
                                          
                                    }else{
                                          $base=number_format($linea["TOTLPC"],2,'.','');
                                    }
                                    $neto2=$neto2+$base;
                                    $desc2=$desc2+($base*$datos["PDTO2PCL"]/100);
                                    $ppago2=$ppago2+($base*$datos["PPPA2PCL"]/100);
                                    $finan2=$finan2+($base*$datos["PFIN2PCL"]/100);
                                    $base2=$base2+($base-($base*$datos["PDTO2PCL"]/100)-($base*$datos["PPPA2PCL"]/100)+($base*$datos["PFIN2PCL"]/100));
                                    $iva2= $iva2+($base*$datos1["PIV2CFG"]/100);
                                    if (recargo($_SESSION["cliente"])){
                                          $re2=$re2+($base*$datos["PREC2PCL"]/100);
                                    }
                                    break;
                              case 2:
                                    if ($linea["IINLPC"]!=0){
                                          $iva=ivaart($linea["ARTLPC"]);
                                          $base=number_format($linea["TOTLPC"],2,'.','')/(1+($iva/100));
                                          $base3=$base3+($base);
                                    }else{
                                          $base=number_format($linea["TOTLPC"],2,'.','');
                                          $base3=$base3+($base);
                                    }
                                    $neto3=$neto3+$base;
                                    $desc3=$desc3+($base*$datos["PDTO3PCL"]/100);
                                    $ppago3=$ppago3+($base*$datos["PPPA3PCL"]/100);
                                    $finan3=$finan3+($base*$datos["PFIN3PCL"]/100);
                                    $base3=$base3+($base-($base*$datos["PDTO3PCL"]/100)-($base*$datos["PPPA3PCL"]/100)+($base*$datos["PFIN3PCL"]/100));
                                    $iva3= $iva3+($base*$datos1["PIV3CFG"]/100);
                                    if (recargo($_SESSION["cliente"])){
                                          $re3=$re3+($base*$datos["PREC3PCL"]/100);
                                    }
                                    break;
                          }//end switch    
                  }//end while
                  //formate todas las cantidades a 2 decimales
                  $base1=number_format($base1,2,'.','');			  
                  $iva1=number_format($iva1,2,'.','');
                  $base2=number_format($base2,2,'.','');
                  $iva2=number_format($iva2,2,'.','');
                  $base3=number_format($base3,2,'.','');
                  $iva3=number_format($iva3,2,'.','');
                  $desc1=number_format($desc1,2,'.','');
                  $desc2=number_format($desc2,2,'.','');
                  $desc3=number_format($desc3,2,'.','');
                  $ppago1=number_format($ppago1,2,'.','');
                  $ppago2=number_format($ppago2,2,'.','');
                  $ppago3=number_format($ppago3,2,'.','');
                  $finan1=number_format($finan1,2,'.','');
                  $finan2=number_format($finan2,2,'.','');
                  $finan3=number_format($finan3,2,'.','');
                  $re1=number_format($re1,2,'.','');
                  $re2=number_format($re2,2,'.','');
                  $re3=number_format($re3,2,'.','');
                  $neto1=number_format($neto1,2,'.','');
                  $neto2=number_format($neto2,2,'.','');
                  $neto3=number_format($neto3,2,'.','');		  
                  $base1=$neto1-$desc1-$ppago1+$finan1;
                  $base2=$neto2-$desc2-$ppago2+$finan2;
                  $base3=$neto3-$desc3-$ppago3+$finan3;
                  echo'<table width="100%" align="center" border="1" bordercolor="#000000" bgcolor="#FFFFFF" cellpadding="1" cellspacing="0">';
                  echo'<tr bordercolor="#999999" bgcolor="#cccccc">';
                  echo'<th>Tipo Iva</th>';
                  echo'<th>Descuento</th>';
                  echo'<th>Pronto Pago</th>';
                  echo'<th>Financiaci&oacute;n</th>';
                  echo'<th>Base</th>';
                  echo'<th>Importe Iva</th>';
                  echo'<th>R.E.</th>';
                  echo'<th>Total</th>';
                  echo'</tr>';
                  if (iva($linea["ARTLPC"],$cliente)!=-1){
                       //no exento de iva
                       if ($base1!=0){
                        echo'<tr bordercolor="#999999">';
                        echo'<td align="right">';
                        printf("%.2f",$datos1["PIV1CFG"]);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$desc1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$ppago1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$finan1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$iva1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$re1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base1+$iva1+$re1);
                        echo'</td>';
                        echo'</tr>';
                        $totfer=$base1+$iva1+$re1;
                        }
                        if ($base2!=0){
                        echo'<tr>';
                        echo'<td align="right">';
                        printf("%.2f",$datos1["PIV2CFG"]);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$desc2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$ppago2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$finan2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$iva2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$re2);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base2+$iva2+$re2);
                        echo'</td>';
                        echo'</tr>';
                        $totfer=$base2+$iva2+$re2;
                        }
                        if ($base3!=0){
                        echo'<tr>';
                        echo'<td align="right">';
                        printf("%.2f",$datos1["PIV3CFG"]);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$desc3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$ppago3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$finan3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$iva3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$re3);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base3+$iva3+$re3);
                        echo'</td>';
                        echo'</tr>';
                        $totfer=$base3+$iva3+$re3;
                        }
                  }else{
                        echo'<tr>';
                        echo'<td>Exento</td>';
                        echo'<td align="right">';
                        printf("%.2f",$desc1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$ppago1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$finan1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base1);
                        echo'</td>';
                        echo'<td align="right">';
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$re1);
                        echo'</td>';
                        echo'<td align="right">';
                        printf("%.2f",$base1+$re1);
                        echo'</td>';
                        echo'</tr>';
                        $totfer=$base1+$base2+$base3+$iva1+$iva2+$iva3+$re1+$re2+$re3;
                       //exento de iva
                  }
                  //escribo las bases en el pedido,ivas, netos, recargos...
                  $ssql='UPDATE F_PCL SET TOTPCL='.($base1+$base2+$base3+$iva1+$iva2+$iva3+$re1+$re2+$re3).',IREC1PCL='.$re1.',IREC2PCL='.$re2.',IREC3PCL='.$re3.',IFIN1PCL='.$finan1.',IFIN2PCL='.$finan2.',IFIN3PCL='.$finan3.',IPPA1PCL='.$ppago1.',IPPA2PCL='.$ppago2.',IPPA3PCL='.$ppago3.',IDTO1PCL='.$desc1.',IDTO2PCL='.$desc2.',IDTO3PCL='.$desc3.',NET1PCL='.($neto1).',NET2PCL='.($neto2).',NET3PCL='.($neto3).',BAS1PCL='.$base1.',BAS2PCL='.$base2.',BAS3PCL='.$base3.',IIVA1PCL='.$iva1.',IIVA2PCL='.$iva2.',IIVA3PCL='.$iva3.' WHERE CODPCL='.$datos["CODPCL"].' AND TIPPCL=\''.$datos["TIPPCL"].'\'';
				  $result=mysql_query($ssql,$conn);
                  echo '<tr bordercolor="#999999">';
                  echo'<td colspan="6" bgcolor="#999999" align="right"><strong>Total:</strong></td>';
                  echo'<td colspan="2" bgcolor="#999999" align="right"><strong>';
                  printf("%.2f",$base1+$base2+$base3+$iva1+$iva2+$iva3+$re1+$re2+$re3);
                  echo'</strong></td>';
                  echo'</tr>';
                  echo'</table>';
                  echo'</font>';
                  //escribo son campos ocultos con la serie y el numero de factura para pasarselo a la pagina de confirmaci�n
                  echo'<input type="hidden" name="serie" value="'.$datos["TIPPCL"].'" />';
                  echo'<input type="hidden" name="numero" value="'.$datos["CODPCL"].'" />';
            }   
      }
}   
function tip_iva_art($art)      
{
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql="SELECT * FROM F_ART WHERE CODART LIKE '".$art."'";
      $rs=mysql_query($ssql,$conn);
      $datos=mysql_fetch_array($rs);
      return $datos['TIVART'];
}  
function ivaart($art)
{
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql="SELECT * FROM F_CFG";
      $rs=mysql_query($ssql,$conn);
      $datos=mysql_fetch_array($rs);
      $tipiva=tip_iva_art($art); 
      switch ($tipiva){
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
function iva ($art,$cliente)
{
      //primero compruebo el el cliente est� o no exento de iva si est� exento devuelvo -1
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      $ssql='SELECT IVACLI, TIVCLI FROM F_CLI WHERE CODCLI='.$cliente;
      $rs=mysql_query($ssql,$conn);
      $datos=mysql_fetch_array($rs);
      if ($datos["IVACLI"]==0){
            switch ($datos["TIVCLI"]){
                  case 0: 
                        return tip_iva_art($art);
                        break;                 
                  case 1:
                         return 0;
                         break;
                  case 2:
                         return 1;
                         break;
                  case 3:
                         return 2;
                         break;
                  }
      }else{         
            return -1;
      }
}
function escribir_pedido ($cliente)
{
//parto de la base que solo hay un pedido en curso
      $conn= mysql_connect (BD_HOST,BD_USERNAME,BD_PASSWORD);
      mysql_select_db (BD_DATABASE1);
      //busco la serie para pedidos de clientes
      $ssql='SELECT SPACFG FROM F_CFG';
      $rs1=mysql_query($ssql,$conn);
      $datos1=mysql_fetch_array($rs1);
      mysql_select_db (BD_DATABASE1);
      //busco los pedidos por serie, cliente y estado      
      $ssql='SELECT * FROM F_PCL WHERE CLIPCL=' . $cliente . ' AND TIPPCL=\'' . $datos1["SPACFG"] . '\' AND ESTPCL=\'0\'';
      $rs=mysql_query($ssql,$conn);
      if (mysql_num_rows($rs)!=0){
            $datos=mysql_fetch_array ($rs);
            //cojo el detalle del pedido por orden de lineas
            $ssql='SELECT * FROM F_LPC WHERE CODLPC=' . $datos["CODPCL"] . ' AND TIPLPC=\'' . $datos["TIPPCL"] . '\' ORDER BY POSLPC ASC'; 
            $rs1=mysql_query($ssql,$conn);
            if (mysql_num_rows($rs1)!=0){
            $total=0;
            $descuento=0;
            $colorcelda = COLOROSCUROCELDA;
                  while ($linea=mysql_fetch_array ($rs1)){
									if($colorcelda == COLORCLAROCELDA) {
										$colorcelda = COLOROSCUROCELDA;
									}else{
										$colorcelda = COLORCLAROCELDA;
									}
                        echo'<tr bordercolor="#cccccc" bgcolor="#ffffff">';
                        echo'<td bgcolor="' . $colorcelda . '">'.$linea["ARTLPC"].'</td>';
	                  echo'<td bgcolor="' . $colorcelda . '">'.$linea["DESLPC"].'</td>';
                        echo'<td align="right" bgcolor="' . $colorcelda . '">';
						if ($linea["IINLPC"]==0){
							$base=$linea["PRELPC"];
						}else{
							$iva=ivaart($linea["ARTLPC"]);
                            $base=($linea["PRELPC"]/(1+$iva/100));
						}
                        printf("%.2f",$base);                        
                        echo'</td>';
      	            echo'<td align="right" bgcolor="' . $colorcelda . '">';
                        echo (decimal($linea["CANLPC"]));
                        echo'</td>';
      	            echo'<td align="right" bgcolor="' . $colorcelda . '">';
                        printf("%.2f",$linea["DT1LPC"]);
                        echo'%</td>';
      	            echo'<td align="right" bgcolor="' . $colorcelda . '">';
      	            printf("%.2f",($base*$linea["CANLPC"])-($base*$linea["CANLPC"]*$linea["DT1LPC"]/100));
                        echo'</td>';
      	            echo'</tr>';
      	     }
                 echo'<tr bordercolor="#999999" align="center" >' ;
                 echo '<td colspan="6">';
                 escribir_bases($_SESSION["cliente"]);
                 //decuentos
                 echo '</td>';
                 echo'</tr>';
      	}else{
                  echo '<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">';
                  echo '<td colspan="7" align="center">NO SE HAN ENCONTRADO ARTICULOS</td>';
                  echo '</tr>';
                  echo'<tr bordercolor="#999999" align="center" >' ;
                  echo '<td colspan="2"><font color="#000000" face="Verdana" size="2"><b>Total:</b></font></td>';
                  echo'<td>';
                  echo'</td><td>&nbsp;</td><td>&nbsp;</td>';
                  //echo'<td><font color="#cccccc">.</font></td>';
                  echo'</tr>';
            }
      }else{
            echo'<table border=0>';
            echo '<tr bordercolor="#cccccc" align="center" bgcolor="#ffffff">';
            echo '<td colspan="7" align="center">NO SE HA ENCONTRADO PEDIDO EN CURSO </td>';
            echo '</tr>';
            echo'<tr bordercolor="#999999" align="center" bgcolor="#cccccc">' ;
            echo '<td colspan="2"><font color="#000000" face="Verdana" size="2"><b>Total:</b></font></td>';
            echo'<td>';
            echo'</td><td>&nbsp;</td><td>&nbsp;</td>';
            echo'</tr>';
      }
}
//Fin de las funciones
?>
<?php require_once('button.php'); ?>