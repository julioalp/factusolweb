<?php
$conn = @mysql_connect($_GET['h'], $_GET['u'], $_GET['p']);
$nbd = @mysql_select_db($_GET['n']);
if($conn) {
	echo('Conexi&oacute;n correcta<br>');
	if($nbd) {
		echo('BD correcta');
	}else{
		echo('BD inexistente');
	}
}else{
	echo('Conexi&oacute;n incorrecta<br>');
}
?>