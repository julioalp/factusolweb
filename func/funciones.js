
function goToUrl(selObj, goToLocation) {
	eval("document.location.href = '" + goToLocation + "&pagina=" + selObj.options[selObj.selectedIndex].value + "'");
}
function Abrir_Ventana(theURL, w, h) {
	var windowprops = "top=0,left=0,toolbar=no,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=" + w + ",height=" + h;
	window.open(theURL,"producto",windowprops);
}
function redireccionar(seccion, familia, variable) {
	var pagina = '';
	if (variable == 1) {
		pagina = 'pproductos.php?seccion=' + seccion;
	}else{
		pagina = 'pproductos.php?seccion=' + seccion + '&familia=' + familia;
	}
	location.href = pagina;
}
function Abrir_Ventana2(theURL, w, h) {
	var windowprops = "top=0,left=0,toolbar=no,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=" + w + ",height=" + h;
	window.open(theURL, "producto", windowprops);
}
function verifForm() {
	if(document.rango.opcion[5].checked) {
		if((document.rango.fecha1.value == "") || (document.rango.fecha2.value == "")) {
			alert('Por favor introduzca las dos fechas');
			return false;
		}
	}else{
		return true;
	}
}
function Comprar(cod, tar, cant) {
	location.href = "carrito.php?cod=" + cod + "&tar=" + tar + "&cant=" + cant;
}
function redireccionar2(seccion, familia, variable) {
	var pagina = '';
	if (variable == 1) {
		pagina = 'productos.php?seccion=' + seccion;
	}else{
		pagina = 'productos.php?seccion=' + seccion + '&familia=' + familia;
	}
	location.href = pagina;
}
function borrarcli() {
	var estado;
	if(confirm("¿Esta seguro que quiere borrar su pedido?")) {
		document.location.href = 'carrito.php?accion=2';
	}else{
		return false;
	}
}
function comenzarcli() {
	var estado;
	if(confirm("¿Esta seguro que quiere comenzar de nuevo su pedido?")) {
		document.location.href = 'carrito.php?accion=4';
	}else{
		return false;
	}
}
function borrarage() {
	var estado;
	if(confirm("¿Esta seguro que quiere borrar su pedido?")) {
		document.location.href = 'carritoage.php?accion=2';
	}else{
		return false;
	}
}
function borrarpedagente(url) {
	var estado;
	if(confirm("¿Esta seguro que quiere borrar el pedido?")) {
		document.location.href = url;
	}else{
		return false;
	}
}  
function comenzarage() {
	var estado;
	if(confirm("¿Esta seguro que quiere comenzar de nuevo su pedido?")) {
		document.location.href = 'carritoage.php?accion=4';
	}else{
		return false;
	}
}
function redireccionarp(seccion, familia, variable) {
	var pagina = '';
	if (variable == 1) {
		pagina = 'agproductos.php?seccion=' + seccion;
	}else{
		pagina = 'agproductos.php?seccion=' + seccion + '&familia=' + familia;
	}
	location.href = pagina;
}
function verifForm2(formulario) {
	var estado=0;
	for (i=0; i<(formulario.elements.length-2); i++) {
		if(formulario.elements[i].value == "") {
			if(formulario.elements[i].name=="CUWCLI" || formulario.elements[i].name=="CAWCLI" || formulario.elements[i].name=="NOFCLI" || formulario.elements[i].name=="DOMCLI" || formulario.elements[i].name=="CPOCLI" || formulario.elements[i].name=="POBCLI" || formulario.elements[i].name=="PROCLI" || formulario.elements[i].name=="NIFCLI" || formulario.elements[i].name=="STELCFG" || formulario.elements[i].name=="SEMACFG") estado=1;
		}
	}
	if (estado==1) {
		alert('¡Los campos marcados con "*" no pueden estar vacíos!');
		return false;
	}else{
		/* no dejamos que el usuario tenga espacios en blanco*/
		var esquema = / /
		if (esquema.test(formulario.elements['CUWCLI'].value)) {
			alert ('El usuario no debe contener espacios en blanco');
			return false;
		}else{  
      return true;
		}
	}
}
function redireccionar5() {
	location.href=pagina;
}
function borrarart(linea) {
	var estado;
	if(confirm("¿Esta seguro que quiere borrar este artículo?")) {
		document.location.href = '?accion=5&linea=' + linea;
	}else{
		return false;
	}
}
function borrarartage(linea) {
      var estado;
      if (confirm("¿Esta seguro que quiere borrar este artículo?")){
            document.location.href='?accion=5&linea=' + linea;
      }else{return false;}
} 

function EsNumerico(val) {
    num = parseFloat(val);
    if (val!=''+num){alert (num); return false;}
    return true;
}

function sumaresta(elemento, val, dec, cantidad) {
	var numero = document.getElementById(elemento);
	var aux = 0;
	aux = parseInt(numero.value);
	if(val=='+') { aux += cantidad; }
	if(val=='-') { aux -= cantidad; }
	numero.value = aux;
	if(numero.value < cantidad) { numero.value = cantidad; }
	numero.value=NumberFormat(numero.value, dec, '.', '');
}
function sumres(elemento, val, dec) {
	var numero = document.getElementById(elemento);
	var aux = 0;
	aux = parseInt(numero.value);
	if(val == '+') { aux+= 1; }
	if(val == '-') { aux-= 1; }
	numero.value = aux;
	if(numero.value < 0) { numero.value = 0; }
	if(numero.value > 100) { numero.value = 100; }
	numero.value = NumberFormat(numero.value, dec, '.', '');
}

function sumaresta2(elemento, val){

	var numero = document.getElementById(elemento);

	var aux = 0;
	
	aux = parseInt(numero.value);
	
	if (val=='+'){aux += 1;}
	if (val=='-'){aux -= 1;}
	
	numero.value = aux;
			
	if (numero.value<1){numero.value = 1;}
	
}

function redireccionar3 ()
{
      var pagina= 'pedagente.php'
      location.href=pagina
      
}

var nav4 = window.Event ? true : false;
function acceptNum(evt,text){ 
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57 
var key = nav4 ? evt.which : evt.keyCode; 
if (text.indexOf('.')== "-1"){
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46 );
}else { return (key <= 13 || (key >= 48 && key <= 57));}
}

   
   function NumberFormat(num, numDec, decSep, thousandSep){
    var arg;
    var Dec;
    Dec = Math.pow(10, numDec); 
    if (typeof(num) == 'undefined') return; 
    if (typeof(decSep) == 'undefined') decSep = ',';
    if (typeof(thousandSep) == 'undefined') thousandSep = '.';
    if (thousandSep == '.')
     arg=/./g;
    else
     if (thousandSep == ',') arg=/,/g;
    if (typeof(arg) != 'undefined') num = num.toString().replace(arg,'');
    num = num.toString().replace(/,/g, '.'); 
    if (isNaN(num)) num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * Dec + 0.50000000001);
    cents = num % Dec;
    num = Math.floor(num/Dec).toString(); 
    if (cents < (Dec / 10)) cents = "0" + cents; 
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
     num = num.substring(0, num.length - (4 * i + 3)) + thousandSep + num.substring(num.length - (4 * i + 3));
    if (cents.length<numDec);
            for(i=cents.length; i<numDec;i++)
                  cents=cents+'0';
     if (Dec == 1)
     return (((sign)? '': '-') + num);
    else
     return (((sign)? '': '-') + num + decSep + cents);
   } 

   function EvaluateText(cadena, obj){
    opc = false; 
    if (cadena == "%d")
     if (event.keyCode > 47 && event.keyCode < 58)
      opc = true;
    if (cadena == "%f"){ 
     if (event.keyCode > 47 && event.keyCode < 58)
      opc = true;
     if (obj.value.search("[.*]") == -1 && obj.value.length != 0)
      if (event.keyCode == 46)
       opc = true;
    }
    if(opc == false)
     event.returnValue = false; 
   }
   
   
function ComprobarCero(elemento) {
	var aux = '';
	var numero = document.getElementById(elemento);
	aux = parseFloat(numero.value);
	if(aux == 0) {
		alert("Debe introducir una cantidad mayor de 0");
		return false;
	}else{
		return true;
	}
}
// this function is needed to work around 
// a bug in IE related to element attributes
function hasClass(obj) {
	 var result = false;
	 if (obj.getAttributeNode("class") != null) {
			 result = obj.getAttributeNode("class").value;
	 }
	 return result;
}   

 function stripeTables(id, colorfc, colorac) {
    // the flag we'll use to keep track of 
    // whether the current row is odd or even
    var even = false;
	// Set the alternate color in the method call arguments
	var evenColor; 
	// hard coded here and applies to all tables.
	/*
	*********
	*********
	*/
    var oddColor = "#" + colorfc;
    /*
	*********
	*********
	*/ 
	// hard coded here and applies to all tables.
	 // Populate 2 arrays with the arguments,
	 // separating the colors from the ID's.
	 var colorArray = new Array();
	 var cArrayCount = 0;
	 var IdArray = new Array();
	 var IdArrayCount = 0;
	 // This script assumes that the arguements always
	 // come in pairs: ID / evenColor. So the first
	 // argument will always be the ID.
	 for (i_id = 0; i_id < arguments.length; i_id++) {
		// Since the function arguments are formatted in ID/color pairs,
		// and the first argument is an ID, when %2 == 0 
		// it will be a element ID and not a color.
		if (i_id%2 == 0) {
			IdArray[IdArrayCount] = arguments[i_id];
			IdArrayCount++;
		}
		else {
			colorArray[cArrayCount] = arguments[i_id];
			cArrayCount++;			
		}
	 }
	 // Populate 2 arrays with arguments
	 /*
	 // Testing code for the arrays
	 alert("Color Array has: "+ colorArray.length);
	 alert("ID Array has: "+IdArray.length);
	 for (a = 0; a < colorArray.length; a++) {
	 	alert(colorArray[a]);	 	
	 }
	  for (a = 0; a < IdArray.length; a++) {
	 	alert(IdArray[a]);	 	
	 }
	 // Testing code for the arrays
	 */
	 // color the rows for each table as defined in the function arguments
	 for (a = 0; a < IdArray.length; a++) {	 	 
		 	     evenColor = colorArray[a]; 		
		 		// obtain a reference to the desired table
				// if no such table exists, abort
				var table = document.getElementById(IdArray[a]);
				if (! table) { return; }		 
				// by definition, tables can have more than one tbody
				// element, so we'll have to get the list of child
				// &lt;tbody&gt;s 
				var tbodies = table.getElementsByTagName("tbody");
				// and iterate through them...
				for (var h = 0; h < tbodies.length; h++) {
				 // find all the &lt;tr&gt; elements... 
				  var trs = tbodies[h].getElementsByTagName("tr");
				  // ... and iterate through them
				  for (var i = 0; i < trs.length; i++) {
					// avoid rows that have a class attribute
					// or backgroundColor style
					if (! hasClass(trs[i]) &&
						! trs[i].style.backgroundColor) {
					  // get all the cells in this row...
					  var tds = trs[i].getElementsByTagName("td");
					  // and iterate through them...
					  for (var j = 0; j < tds.length; j++) {
						var mytd = tds[j];
						// avoid cells that have a class attribute
						// or backgroundColor style
						if (! hasClass(mytd) &&
							! mytd.style.backgroundColor) {
					
						  mytd.style.backgroundColor = even ? "#" + colorfc : "#" + colorac;
						}
					  }
					}
					// flip from odd to even, or vice-versa
					even =  ! even;
				  }
				}
		} // for loop		
  }