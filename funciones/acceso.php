
<link href="../plantillas/estandar/estilos/estilo.css" rel="stylesheet" type="text/css">
<div align="center">
	<p style="margin-top:100px">&nbsp;</p>
<style type="text/css">
div.rounded-box {
	position:relative;
	width: 160px;
	background-color: #E6E6E6;
	margin:10px;
	padding:5px;
}
/*********************
GLOBAL ATTRIBUTES
*********************/
div.top-left-corner, div.bottom-left-corner, div.top-right-corner, div.bottom-right-corner {
	position:absolute; width:20px; height:20px; background-color:#FFFFFF; overflow:hidden;
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
div.box-contents { position: relative; padding: 8px; color:#000000; }
</style>
	<div class="rounded-box">
    <div class="top-left-corner"><div class="top-left-inside">&bull;</div></div>
    <div class="bottom-left-corner"><div class="bottom-left-inside">&bull;</div></div>
    <div class="top-right-corner"><div class="top-right-inside">&bull;</div></div>
    <div class="bottom-right-corner"><div class="bottom-right-inside">&bull;</div></div>
    <div class="box-contents">
			<form id="form1" name="form1" method="post" action="index.php" />
				<p>Usuario:<br />
					<input name="loginadmin" type="text" id="usuario" size="20" /></p><br />
				<p>Password:<br />
					<input name="contraadmin" type="password" id="password" size="20" /></p><br /><br />
				<input type="image" style="border:none" src="../plantillas/<?php echo(PLANTILLA); ?>/imagenes/botonacceder.png" align="middle" /><br /><br />
      </form>
		</div> 
<!-- end div.box-contents --> 
	</div> 
<!-- end div.rounded-box -->
</div>
<script language="javascript">
    document.form1.usuario.focus();
</script>