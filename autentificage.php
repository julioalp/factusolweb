<?
session_start();
if ($_SESSION['autentificado'] == 'SI' and $_SESSION['tipo_usuario'] == 'agente') { 
	header('Location: agente.php');
	exit();
}
require_once('top.php');
?>

<?php
require_once('conf.inc.php');
?>
<div align="center">
	<p><br /><br /><br /></p>
<? echo @$_GET['ERROR'];?>
<br /><br />
<style type="text/css">
div.rounded-box {
	position:relative;
	width: 150px;
	background-color: #E6E6E6;
	margin: 3px;
}
/*********************
GLOBAL ATTRIBUTES
*********************/
div.top-left-corner, div.bottom-left-corner, div.top-right-corner, div.bottom-right-corner {
	position:absolute; width:20px; height:20px; background-color:#FFF; overflow:hidden;
}
div.top-left-inside, div.bottom-left-inside, div.top-right-inside, div.bottom-right-inside {
	position:relative; font-size:150px; font-family:arial; color:#E6E6E6; line-height: 40px;
}
/*********************
SPECIFIC ATTRIBUTES
*********************/
div.top-left-corner { top:0px; left:0px; }
div.bottom-left-corner {bottom:-1px; left:0px;}
div.top-right-corner {top:0px; right:0px;}
div.bottom-right-corner {bottom: -1px; right:0px;}
div.top-left-inside {left:-8px;}
div.bottom-left-inside {left:-8px; top:-18px;}
div.top-right-inside {left:-25px;}
div.bottom-right-inside {left:-25px; top:-18px;}
div.box-contents { position: relative; padding: 8px; color:#000; }
</style>
	<div class="rounded-box">
		<div class="top-left-corner"><div class="top-left-inside">&bull;</div></div>
    <div class="bottom-left-corner"><div class="bottom-left-inside">&bull;</div></div>
    <div class="top-right-corner"><div class="top-right-inside">&bull;</div></div>
    <div class="bottom-right-corner"><div class="bottom-right-inside">&bull;</div></div>
    <div class="box-contents">
			<form id="form1" name="form1" method="post" action="sessionage.php">
	    	<p>Agente:<br /><input name="agente" type="text" id="usuario" size="20" /></p><br />
    		<p>Password:<br /><input name="password" type="password" id="password" size="20" /></p>
				<br />
		    <input type="hidden" name="enviado" value="true"/>
    		<input name="image2" type="image" style="border:none" src="plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonacceder.png" />
		  </form>
	  </div> 
<!-- end div.box-contents --> 
	</div> 
<!-- end div.rounded-box -->
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</div>
<script language="javascript">
    document.form1.usuario.focus();
</script>
<?php require_once('button.php'); ?>