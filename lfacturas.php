<?php
session_start();
if($_SESSION['autentificado'] != 'SI' or $_SESSION['tipo_usuario'] != 'usuario') {
	header('Location: autentifica.php');
	exit();
}
require_once('top.php');
?>

<?php
require_once('menum.php');
require_once('cmodulos.php');
require_once('func.php');
//compruebo si el modulo está activo.
imodulofact ();
cmodulofact ();
//cojo los datos del formulario anterior y compruebo que se han puesto todas las fechas
switch($_POST['opcion']) {
	case "10";
		listarfac2($_SESSION['cod_factusol']);
		break;
	case "todas";
		$fecha1 = 0;
		$fecha2 = time();
		listarFact($fecha1, $fecha2, $_SESSION['cod_factusol'], 1);
		break;
	case "semana";
		//averiguo el día de la semana
		$dia = date("l");
		switch($dia) {
			case $dia == "Monday";
				$dia = 1;
				break;
			case $dia == "Tuesday";
				$dia = 2;
				break;
			case $dia == "Wednesday";
				$dia = 3;
				break;
			case $dia == "Thursday";
				$dia = 4;
				break;
			case $dia == "Friday";
				$dia = 5;
				break;
			case $dia == "Saturday";
				$dia = 6;
				break;
			case $dia == "Sunday";
				$dia = 7;
				break;
		}
		$fecha1 = time() - (($dia - 1) * 24 * 60 * 60);
		$fecha2 = time();
		listarFact($fecha1, $fecha2, $_SESSION['cod_factusol'], 2);
		break;
	case "mes";
		//Calculo el primero de més
		$fecha1 = time() - ((date("j") - 1) * 24 * 60 * 60);
		$fecha2 = time();
		listarFact($fecha1, $fecha2, $_SESSION['cod_factusol'], 3);
		break;
	case "mesant";    
		//averiguo del día del mes  y el año
		$year = date("Y");
		if(date("m") != 1) {
			$mes = date("m") - 1;
		}else{
			$mes = 12;
			$year -= 1;
		}
		//averiguo los días del mes que tiene ese mes en el calendario gregoriano
		$num = cal_days_in_month(CAL_GREGORIAN, $mes, $year);
		$fecha1 = mktime(1, 1, 1, $mes, 1, $year);
		$fecha2 = mktime(1, 1, 1, $mes, $num, $year);
		listarFact($fecha1, $fecha2, $_SESSION['cod_factusol'], 4);
		break;
	case "fechas";
		$fecha1 = $_POST['fecha1'];
		$fecha2 = $_POST['fecha2'];
		$matriz = explode('/', $fecha1);
		$matriz2 = explode('/', $fecha2);
		$fecha1 = mktime(1, 1, 1, $matriz[1], $matriz[0], $matriz[2]);
		$fecha2 = mktime(1, 1, 1, $matriz2[1], $matriz2[0], $matriz2[2]);
		//comprobamos que fecha2 es mayor que fecha 1 y si no lo cambiamos
		if(date("d/m/y", $fecha1) > date("d/m/y", $fecha2)) {
			$aux = $fecha1;
			$fecha1 = $fecha2;
			$fecha2 = $aux;
		}
		listarFact($fecha1, $fecha2, $_SESSION['cod_factusol'], 5);
		break;
}
?>
<br />
<p><img src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonvolver.png" onclick="javascript:window.history.back()" border="0" style="cursor:pointer"></p>
</div>
<?php require_once('button.php'); ?>