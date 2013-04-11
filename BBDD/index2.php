<?php
require('../conf.inc.php');
$gestor=fopen('factusolweb.sql', "rb");
$contenido=fread($gestor, filesize('factusolweb.sql'));
fclose($gestor);
$inicio = 0;
$fin = 0;
$conex = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
$msb = mysql_select_db(BD_DATABASE1, $conex);
while (strpos($contenido, ";\r\n", $inicio)) {
	$fin = strpos($contenido, ";\r\n", $inicio) + 1;
	$sql = substr($contenido, $inicio, $fin-$inicio);
	$mq = mysql_query($sql, $conex);
	$inicio = $fin;
}
$fin = strpos($contenido, ";", $inicio) + 1;
$sql = substr($contenido, $inicio, $fin-$inicio);
$mq = mysql_query($sql, $conex);
unlink('factusolweb.sql');
// NO ES NECESARIO PERO ASI SABES QUE HA TERMINADO LA EJECUCION
echo('<br><br><br><br><center>Fin del Proceso.</center>');
?>
