<?php

include_once("_inc/config_login.inc.php");
include_once($_project_server_path.$_salat_path."_inc/functions.inc.php");
include_once($_project_server_path.$_salat_path."_inc/captcha.functions.inc.php");
include_once($_project_server_path.$_salat_path."_static/languages.inc.php");

// Auto login for inManage office
// Added by Albet Harounian March 2008

$ipArr=explode('.',$_SERVER['REMOTE_ADDR']);

if(array_sum(array($ipArr[0],$ipArr[2],$ipArr[2]))==20){
	//$local_server=true;
}
if(configManager::is_dev_mode() || configManager::$allow_sysadmin_autologin){
	include_once($_project_server_path.$_salat_path."_static/sysusers.inc.php");
	foreach ($sysusersArr as $userID => $userDetails) {
		if($userDetails['username']==_sysadmin_username){
			$_SESSION['salatUserUName'] = _sysadmin_username;
			$_SESSION['salatUserID'] = $userID;
			$_SESSION['salatUserFName'] = $userDetails['fullname'];
			$_SESSION['salatUserEmail'] = $userDetails['email'];
			$_SESSION['salatSecureLoginTrys'] = null;
			unset($_SESSION['salatSecureLoginTrys']);
			setcookie("salatUserUName",$vars['txtUName'],time()+432000); // for 5 days = 120 hours
			header("location: ".$_html_nonsecured_path.$_salat_path."frames.php");
			exit();
		}
	}
}

// set lang
if (isset($_GET['langid']) && intval($_GET['langid'])>0){
	$_SESSION['salatLangID'] = intval($_GET['langid']);
	header("location: ".$_html_nonsecured_path.$_salat_path."login.php");
	exit();
}
// set default HEBREW
if (isset($_SESSION['salatLangID']) && $_SESSION['salatLangID']==0){
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

if (isset($vars['txtPass']) && $vars['txtPass']!=""){
	$_POST['rnd'] = (int)$_POST['rnd'];
	$_POST['rndchk'] = (int)$_POST['rndchk'];
	if ($_SESSION['salatSecureLoginTrys'] < $constMaxTrys) {
		if (extension_loaded("gd")) {
			$datekey = date("F j");
			$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'].'1'.$_POST['rnd'].$datekey));
			$code = substr($rcode,4,$codelen);
			//$code=
			$cptTest = json_decode(captcha_decrypt($_SESSION['cpt'],$_SESSION['_salat_v2_hk']),true);
			//file_put_contents('../_static/test2.txt',var_export($test,true));
			if($_POST['rndchk']!=$cptTest['r']) {
				//$SalatUser->logAction('login','captcha incorrect');
				$errors[] = 'Wrong verification code';	
				$params['reload_captcha'] = '1';
			}else{
			    $_POST['rndchk'] = "ok";
				$code = "ok";	
			}
		}else{
			// no GD (image library), so let in
			$_POST['rndchk'] = "ok";
			$code = "ok";
		}
		$query = "SELECT id,fullname,email FROM tb_sys_users WHERE ((username='".$vars['txtUName']."') AND (password='".hash("sha256","_in".$vars['txtPass']."ge_")."') AND (isactive='yes'))";

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
			setcookie("salatUserUName",$vars['txtUName'],time()+432000); // for 5 days = 120 hours
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

$salat_relative_path = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']);
$salat_relative_path = str_replace(basename($salat_relative_path),"",$salat_relative_path);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
   <title>Salat2 2013</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<meta name="robots" content="noindex,nofollow">
	<link rel="StyleSheet" href="_public/main.css" type="text/css">
	<script language="javascript">
		function doEnter(){
			if (document.frmEnter.txtUName.value!=""){
				if (document.frmEnter.txtPass.value!=""){
					if(document.frmEnter.rndchk.value!=""){
						return true;
					}else{
						alert("Security code must be entered");
						return false;
					}
				}else{
					alert("Passsword must be entered");
					return false;
				}
			}else{
				alert("Username must be entered");
				document.frmEnter.txtUName.focus();
				return false;
			}
		}
	</script>
</head>
<body marginheight="0" marginwidth="0" rightmargin="0" bottommargin="0" leftmargin="0" topmargin="0" background="/salat2/_public/background_inmanage.png">
<div id="logo" style="position:absolute;top:150px;left:42%;">
    <img src="/salat2/_public/inmanage.png">
</div>
<div id="contents_logo" style="position:absolute;top:150px;left:42%;">
<table width="100%" height="100%" style="position:absolute;top:100px;left:42%;" dir="<?php echo $_LANG['salat_dir'];?>" border="0" cellspacing="0">
    <tr><td colspan="4" height="18"></td></tr><tr>

	<td align="center" valign="middle">
		<?php 
		if (isset($vars['msg']) && $vars['msg']==1) echo "<span class=error>".$_LANG['login_err']."</span>";
		elseif (isset($vars['msg']) && $vars['msg']==2) echo "<span class=error>".$_LANG['login_ok']."</span>";
		elseif (isset($vars['msg']) && $vars['msg']==3) echo "<span class=error>".$_LANG['login_3trys']."</span>";
		if (!isset($_SESSION['salatSecureLoginTrys']) || $_SESSION['salatSecureLoginTrys'] < $constMaxTrys){
		?>
		<form name="frmEnter" action="login.php" method="POST" onsubmit="javascript: return doEnter();">
		<input type="hidden" name="langid" value="<?php echo $_SESSION['salatLangID'];?>">

		<table width="315" cellpadding="5" cellspacing="3" border="0" style="font-weight:bold;padding:5px;font-size:15px;color:#fdfcfc;text-shadow: #050505 0 -1px 0;font-weight: bold;">
            <?php if(_salat_is_multilang) { ?>
			<tr>
				<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['language'];?>:</td>
				<td align="<?php echo $_LANG['align_right'];?>"><a href="javascript:document.location.href='<?php echo $salat_relative_path;?>login.php?langid=<?php echo ($_SESSION['salatLangID']==1 ? '2' : '1');?>';" style="color:white"><?php echo ($_SESSION['salatLangID']==1?"Change to Hebrew":"שנה שפה לאנגלית");?></a></td>
			</tr>
			<?php } ?>
            <tr>
				<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['username'];?>:</td>
				<td align="<?php echo $_LANG['align_right'];?>"><input type="text" name="txtUName" style="width: 200px; -moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px;background:#fdfcfc;border: solid 1px transparent;color:#333333;padding: 8px;font-size:14px;" value="<?php echo (isset($_COOKIE['salatUserUName']) ? $_COOKIE['salatUserUName'] : '');?>" dir="ltr" /></td>
			</tr>
			<tr>
				<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['password'];?>:</td>
				<td align="<?php echo $_LANG['align_right'];?>"><input type="password" name="txtPass" style="width: 200px; -moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px;background:#fdfcfc;border: solid 1px transparent;color:#333333;padding: 8px;font-size:14px;" dir="ltr" /></td>
			</tr>
			<tr>
				<td align="<?php echo $_LANG['align_left'];?>"><?php echo $_LANG['seccode'];?>:</td>
				<td align="<?php echo $_LANG['align_right'];?>" valign="middle"><input type="hidden" name="rnd" value="<?php echo $rnd;?>" />
                    <input type="text" style="width: 200px; -moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px;background:#fdfcfc;border: solid 1px transparent;color:#333333;padding: 8px;font-size:14px;margin-bottom:20px;" name="rndchk" size="<?php echo ($codelen+1);?>" maxlength="<?php echo $codelen;?>" />
                    <img src="/salat2/_inc/login.captcha.php"  style="margin-left:2px;border: 1px solid #fdfcfc;border-radius:4px;width:195px;height:95px;" align="absmiddle" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><br><input type="submit" value="<?php echo $_LANG['enter'];?>" style="cursor:pointer;border-radius:4px;height:30px;width:70px;font-size:16px; background-color:#0573C7; margin-left:220px; font-weight:bold;  color: #ffffff; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #84868b; background-image: -moz-linear-gradient(top, #84868b, #000); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#84868b), to(#000)); background-image: -webkit-linear-gradient(top, #84868b, #000);   background-image: -o-linear-gradient(top, #84868b, #000);
                    background-image: linear-gradient(to bottom, #84868b, #000); background-repeat: repeat-x; border-color: #84868b #0044cc #000; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);"  /></td>
			</tr>
		</table>
            <!-- <img src="images/loginbottom.gif" border="0" width="300" height="11" />  -->
    		</form>
		<?php }?>
		<br /><br />
		<!--<hr size="1" width="550" color="#cccccc" /> -->
	</td>

</tr>
</table>
</div>
<script>try{document.frmEnter.txtUName.focus();}catch(e){}</script>

</body>
</html>
