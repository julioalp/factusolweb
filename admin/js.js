// JavaScript Document
function Alternar(Seccion) {
	document.getElementById("Servidor").style.display = "none";
	document.getElementById("menuServidor").style.backgroundColor = "#F5F5FF";
	document.getElementById("Administracion").style.display = "none";
	document.getElementById("menuAdministracion").style.backgroundColor = "#F5F5FF";
	document.getElementById("Aspecto").style.display = "none";
	document.getElementById("menuAspecto").style.backgroundColor = "#F5F5FF";
	document.getElementById("Avanzadas").style.display = "none";
	document.getElementById("menuAvanzadas").style.backgroundColor = "#F5F5FF";
	document.getElementById("Editar").style.display = "none";
	document.getElementById("menuEditar").style.backgroundColor = "#F5F5FF";
	document.getElementById("Instalar").style.display = "none";
	document.getElementById("menuInstalar").style.backgroundColor = "#F5F5FF";
	document.getElementById(Seccion).style.display = "";
	document.getElementById("menu"+Seccion).style.backgroundColor = "#EEEEFF";
	if(Seccion == "Aspecto") {
		document.getElementById("paleta").style.visibility = "visible";
	}else{
		document.getElementById("paleta").style.visibility = "hidden";
	}
}
function mandar() {
	document.getElementById("boton").style.visibility="hidden";
	document.getElementById("texto").style.visibility="visible";
	return true;
}
function estado(zona) {
	var bien = '../plantillas/estandar/imagenes/aceptar.gif';
	var mal = '../plantillas/estandar/imagenes/nor_ant.gif';
	switch(zona) {
		case 'Servidor':
			var v1 = document.getElementById("host").value.length;
			var v2 = document.getElementById("BD1").value.length;
			var v3 = document.getElementById("usuario").value.length;
			var v4 = document.getElementById("password").value.length;
			var total = v1 * v2 * v3 * v4;
			break;
		case 'Administracion':
			var v1 = document.getElementById("user").value.length;
			var v2 = document.getElementById("pass").value.length;
			var total = v1 * v2;
			break;
		case 'Aspecto':
			var v1 = document.getElementById("titulo").value.length;
			var v2 = document.getElementById("colorcabecera").value.length;
			var v3 = document.getElementById("colorcabecerabarra").value.length;
			var v4 = document.getElementById("colorcabeceratexto").value.length;
			var v5 = document.getElementById("coloroscuroceldas").value.length;
			var v6 = document.getElementById("colorclaroceldas").value.length;
			var total = v1 * v2 * v3 * v4 * v5 * v6;
			break;
		case 'Avanzadas':
			var v1 = document.getElementById("tiempoerror").value.length;
			var v2 = document.getElementById("memoria").value.length;
			var total = v1 * v2;
			break;
	}
	if(total) {
		document.getElementById('img' + zona).src = bien;
	}else{
		document.getElementById('img' + zona).src = mal;
	}
	var v1 = document.getElementById('imgServidor').src;
	var v2 = document.getElementById('imgAdministracion').src;
	var v3 = document.getElementById('imgAspecto').src;
	var v4 = document.getElementById('imgAvanzadas').src;
	if(v1.match('aceptar') && v2.match('aceptar') && v3.match('aceptar') && v4.match('aceptar')) {
		document.getElementById('estadoDatos').innerHTML = 'Completado';
		document.getElementById('boton').disabled = false;
	}else{
		document.getElementById('estadoDatos').innerHTML = 'Faltan datos';
		document.getElementById('boton').disabled = true;
	}
}
function createRequestObject(){
	var peticion;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
		peticion = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		peticion = new XMLHttpRequest();
	}
	return peticion;
}
var http = new Array();
function ObtDatos(url){
	var act = new Date();
	http[act] = createRequestObject();
	http[act].open('get', url);
	http[act].onreadystatechange = function() {
		if (http[act].readyState == 4) {
			if (http[act].status == 200 || http[act].status == 304) {
				var texto;
				texto = http[act].responseText;
				var DivDestino = document.getElementById("Prueba");
				DivDestino.innerHTML = texto;
			}
		}
	}
	http[act].send(null);
}
function compUsuario(Tecla) {
	var pruHos = document.getElementById("host").value;
	var pruNom = document.getElementById("BD1").value;
	var pruUsu = document.getElementById("usuario").value;
	var pruPas = document.getElementById("password").value;
	ObtDatos("login.php?h=" + pruHos + "&n=" + pruNom + "&u=" + pruUsu + "&p=" + pruPas);
}