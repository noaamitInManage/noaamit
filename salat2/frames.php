<?php
include("_inc/config.inc.php");
// load lang texts

include($_project_server_path.$_salat_path."_static/langs/".$_SESSION["salatLangID"].".inc.php");
if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0')){

	exit('הינך גולש בגרסה ישנה של דפדפן אינטרנט אקספלור , אנא שדרג.');
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Pragma" CONTENT="no-cache">
	<meta name="robots" content="noindex,nofollow">
	<link rel="StyleSheet" href="_public/main.css" type="text/css">
	<title><?php echo $_LANG["salat_title"];?></title>
	
</head>

<frameset id="top" name="top"  cols="100%" rows="100%" frameborder="0" border="0" framespacing="0">
	<?php  if ($_LANG["salat_dir"]=="rtl"){ ?>
		<frame  src="main.php" name="framMain" id="framMain" scrolling="auto" frameborder="0" border="0" style="border-right:dotted 1px <?php echo $_color_normal;?>;">
	<?php  }else{ ?>
		<frame  src="main.php" name="framMain" id="framMain" scrolling="auto" frameborder="0" border="0">
	<?php  } ?>
</frameset>
<noframes><?php echo $_LANG["salat_noframes"];?></noframes>

</html>
