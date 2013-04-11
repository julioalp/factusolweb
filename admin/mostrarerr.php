<?php
switch(mysql_errno()) {
	case '1044':
		echo('<b>Se ha denegado el acceso a la base de datos: </b>' . $_POST['BD1'] . '<b><br>La base de datos no existe y no dispone de privilegios para crearla.</b>');
		break;
	case '1045':
		echo('<b>El usuario: </b>' . $_POST['usuario'] . '<b> y/o la contraseña de acceso a la base de datos MySQL, no son los correctos. Verifique los datos</b>');
		break;
	case '1046':
		echo('<b>No se ha introducido el nombre de la base de datos de FactuSol Web.</b>');
		break;
	case '1049':
		echo('<b>La base de datos: </b>' . $_POST['BD1'] . '<b> no puede crearse. Debe crearla manualmente antes de ejecutar este instalador.</b>');
		break;
	case '1102':
		echo('<b>El nombre de la base de datos no es v&aacute;lido, por favor, elija otro.</b>');
		break;
	case '2003':
		echo('<b>La direcci&oacute;n del servidor MySQL es incorrecta.</b>');
		break;
	default:
		echo(mysql_errno() . ' - ' . mysql_error());
		break;
}
?>