
<?php
//funcion para escribir el archivo 
function escribeusuario ($codigo,$cadena){
$fp=fopen('nclientes/' . $codigo . '.txt','w');
fwrite ($fp,$cadena);
fclose($fp);
}
//comprobar si existe ya en la bd o en los archivos de nuevos clientes.
//file_exists('archivo');
?>
