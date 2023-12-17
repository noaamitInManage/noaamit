<?php 

ob_start();
include("_inc/config_login.inc.php");
include($_project_server_path.$_salat_path."_inc/functions.inc.php");
include($_project_server_path.$_salat_path."_static/languages.inc.php");
$_SESSION['salatLangID'] = 2;

// set lang
if ((int)$_GET['langid']>0){
	$_SESSION['salatLangID'] = (int)$_GET['langid'];
	header("location: ".$_html_nonsecured_path.$_salat_path."login.php");
	exit();
}

// set default HEBREW
if ((int)$_SESSION['salatLangID']==0){
	$_SESSION['salatLangID'] = 2;
	header("location: ".$_html_nonsecured_path.$_salat_path."login.php");
	exit();
}

// load lang texts
include($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'] .".inc.php");

$constMaxTrys = 3;
$codelen = 5;
$rnd = mt_rand(0, pow(10,$codelen));

// do login
if ($vars['txtPass']!=""){
	$_POST['rnd'] = (int)$_POST['rnd'];
	$_POST['rndchk'] = (int)$_POST['rndchk'];
	if ($_SESSION['salatSecureLoginTrys'] < $constMaxTrys) {
		if (extension_loaded("gd")) {
			$datekey = date("F j");
			$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'].'1'.$_POST['rnd'].$datekey));
			$code = substr($rcode,4,$codelen);
		}else{
			// no GD (image library), so let in
			$_POST['rndchk'] = "ok";
			$code = "ok";
		}
		$query = "SELECT id,fullname,email FROM tb_sys_users WHERE ((username='".$Db->make_escape($vars['txtUName'])."') AND (password='".md5($vars['txtPass'])."') AND (isactive='yes'))";
		$result = $Db->query($query) or  lErr('','',$query);
		if (($result->num_rows>0) && ($_POST['rndchk']==$code)){
			$arr = $Db->get_stream($result);
			// do login and send to frames.php
			$_SESSION['salatUserUName'] = $vars['txtUName'];
			$_SESSION['salatUserID'] = $arr['id'];
			$_SESSION['salatUserFName'] = $arr['fullname'];
			$_SESSION['salatUserEmail'] = $arr['email'];
			$_SESSION['salatLangID'] = (int)$_POST['langid'];
			$_SESSION['salatSecureLoginTrys'] = null;
			unset($_SESSION['salatSecureLoginTrys']);
			// set cookie
			setcookie("salatbvdUserUName",$vars['txtUName'],time()+432000); // for 5 days = 120 hours
			mysqli_free_result($result);
			echo "<script>document.location = '".$_html_nonsecured_path.$_salat_path."frames.php';</script>";
			exit();
		}else{
			$_SESSION['salatSecureLoginTrys']++;
			if ($_SESSION['salatSecureLoginTrys'] < $constMaxTrys) {
				header("location: ".$_html_nonsecured_path.$_salat_path."login.php?msg=1&byuser=".$vars['txtUName']);
				exit();
			}else{
				header("location: ".$_html_nonsecured_path.$_salat_path."login.php?msg=3&byuser=".$vars['txtUName']);
				exit();
			}
		}
	}else{
		header("location: ".$_SERVER['PHP_SELF']."?msg=3");
		exit();
	}
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?= $_LANG['salat_dir'];?>">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<meta name="robots" content="noindex,nofollow">
	<link rel="StyleSheet" href="_public/main.css" type="text/css">
	<script language="javascript">
		function doEnter(){
			if (document.frmEnter.txtUName.value!=""){
				if (document.frmEnter.txtPass.value!=""){
					document.frmEnter.submit();
				}else{
					alert("Passsword must be entered");
					document.frmEnter.txtPass.focus();
				}
			}else{
				alert("Username must be entered");
				document.frmEnter.txtUName.focus();
			}
		}
	</script>
</head>
<body marginheight="0" marginwidth="0" rightmargin="0" bottommargin="0" leftmargin="0" topmargin="0">

<table width="100%" height="100%" dir="<?=$_LANG['salat_dir'];?>" border="0" cellspacing="0"><tr><td colspan="4" height="18" bgcolor="<?=$_color_normal;?>"></td></tr><tr>
	<td width="180" align="center" valign="middle" bgcolor="<?php echo $_color_normal;?>" style="color:<?php echo $_color_text;?>;font-size:16px;font-family:tahoma;">
		<b><big><big>Salat2</big><br /><br /><br />Content<br /><br />Management<br /><br />System</big></b>
		<br /><br /><br /><br /><br />
		<small>powered by<br />inManage.co.il</small>
	</td>
	<td align="center" valign="middle">
		<?php 
		if ($vars['msg']==1) echo "<span class=error>".$_LANG['login_err']."</span>";
		elseif ($vars['msg']==2) echo "<span class=error>".$_LANG['login_ok']."</span>";
		elseif ($vars['msg']==3) echo "<span class=error>".$_LANG['login_3trys']."</span>";
		if ($salatSecureLoginTrys < $constMaxTrys){
		?>      
		<form name="frmEnter" action="login.php" method="POST">
		<input type="hidden" name="langid" value="<?php echo $_SESSION['salatLangID'];?>">
		<img src="images/loginheader_<?php echo $_SESSION['salatLangID'];?>.gif" border="0" width="50" height="22" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<table width="305" cellpadding="5" cellspacing="3" border="0" style="border:dotted 1px <?php echo $_color_normal;?>;color:<?php echo $_color_dark;?>;"><tr>
		<!--
			<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['language'];?>:</td>
			<td align="<?php echo $_LANG['align_right'];?>"><a href="javascript:document.location.href='login.php?langid=<?php echo (3-$_SESSION['salatLangID']);?>';" style="color:<?php echo $_color_dark;?>;"><?php echo ($_SESSION['salatLangID']==1?"Change to Hebrew":"שנה שפה לאנגלית");?></a></td>
		</tr><tr>
		-->
			<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['username'];?>:</td>
			<td align="<?php echo $_LANG['align_right'];?>"><input type="text" name="txtUName" style="border: solid 1px <?php echo $_color_normal;?>;color:<?php echo $_color_dark;?>;" value="<?php echo $_COOKIE['salatbvdUserUName'];?>" dir="ltr" onKeyUp="if (window.event.keyCode==13) doEnter()"></td>
		</tr><tr>
			<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['password'];?>:</td>
			<td align="<?php echo $_LANG['align_right'];?>"><input type="password" name="txtPass" style="border: solid 1px <?php echo $_color_normal;?>;color:<?php echo $_color_dark;?>;" dir="ltr" onKeyUp="if (window.event.keyCode==13) doEnter()"></td>
		</tr><tr>
			<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['seccode'];?>:</td>
			<td align="<?php echo $_LANG['align_right'];?>" valign="middle"><input type="hidden" name="rnd" value="<?php echo $rnd;?>"><input onkeyup='if (event.keyCode==13) doEnter()' type="text" style="border: solid 1px <?php echo $_color_normal;?>;color:<?php echo $_color_dark;?>;" name="rndchk" SIZE="<?php echo ($codelen+1);?>" MAXLENGTH="<?php echo $codelen;?>">&nbsp;<img src="_inc/createsecureimage.inc.php?rnd=<?php echo $rnd;?>&cl=<?php echo $codelen;?>&type=1" height="20" width="70" border='1' align="absmiddle" /></td>
		</tr><tr>
			<td colspan="2" align="center"><br><input type="button" value="<?php echo $_LANG['enter'];?>" onClick="doEnter()" style="border:solid 1px <?php echo $_color_dark;?>;color:<?php echo $_color_text;?>;font-size:15px;width:80px;height:22px;font-weight:bold;background-color:<?php echo $_color_normal;?>;" /></td>
		</tr></table>
		<img src="images/loginbottom.gif" border="0" width="300" height="11" />
		</form>
		<?php }?>
		<br /><br />
		<hr size="1" width="550" color="<?php echo $_color_normal;?>" />
	</td>
	<td width="1" bgcolor="<?php echo $_color_dark;?>"></td>
	<td width="50" bgcolor="<?php echo $_color_normal;?>"></td>
</tr>
<tr><td height="3" bgcolor="<?php echo $_color_normal;?>"></td><td colspan="2" height="3" bgcolor="<?php echo $_color_dark;?>"></td><td height="3" bgcolor="<?php echo $_color_normal;?>"></td></tr>
<tr><td colspan="4" height="18" bgcolor="<?php echo $_color_normal;?>"></td></tr></table>

<script>try{document.frmEnter.txtUName.focus();}catch(e){}</script>
       
</body>
</html>
