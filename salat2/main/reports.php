<?php 

include_once("../_inc/config.inc.php");
$_ProcessID = 4;

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query) or die ("error checking user permissions<br>".mysql_error());
if (($result->num_rows)==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

//

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="rtl">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<script language="JavaScript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script language="javascript">
		//
	</script>
</head>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="maindiv">
<span style="color:<?php echo $_color_normal;?>;font-size:16px;font-weight:bold;">דוחות מערכת</span>
<br><br>
לא קיימות הגדרות מערכת בשלב זה
</div>

</body>
</html>