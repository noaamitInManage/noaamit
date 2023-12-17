<?php 
include("_inc/config.inc.php");
// load lang texts
include($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'] .".inc.php");

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<meta name="robots" content="noindex,nofollow">
	<link rel="StyleSheet" href="_public/main.css" type="text/css">
	<SCRIPT LANGUAGE="JavaScript">if (window.parent==window) location.href = 'frames.php'; </SCRIPT> 
	<script>
		function logOut(){
			if (confirm("<?php echo $_LANG['exit_question'];?>")) top.document.location = 'login.php?logout=yes&skip_auto_login=1';
		}
	</script>
</head>
<body topmargin=0 rightmargin=0 leftmargin=0 marginheight="0" marginwidth="0">

<table border=0 width="100%" height="100%" cellpadding=0 cellspacing=0 bgcolor="<?php  // echo $_color_normal;?>" style="color:<?php echo $_color_text;?>;"><tr>
	<td align=center valign=middle width=250>
		<a href="/" target="_blank"><img src="<?=_project_logo_big;?>" alt="" title="" border="0" height="40" /></a>
	</td>
	<td align=center valign=middle style="color:<?php echo $_color_lite;?>;font-size:21px;font-weight:bold;"><?php echo ($_SESSION['salatLangID']==1?"Salat - Content Management System":"סביבה לניהול תוכן - " . $_LANG['title_main']);?></td>
	<td align=center valign=middle width=150><input type="button" value="<?php echo $_LANG['exit_button'];?>" onclick="logOut();" style="border:solid 1px <?php echo $_color_dark;?>;color:<?php echo $_color_text;?>;font-size:13px;width:130px;height:20px;font-weight:bold;background-color:<?php echo $_color_normal;?>;"></td>
	</tr></table>
	<script type="text/javascript">
		var session_time=1800;
		var session_timerID=0;
		(function(){
			if(document.location.href.indexOf("navigation.php")>-1||document.location.href.indexOf("index.php")>-1){return false;}
			var div=document.createElement("div");
			div.style.position="absolute";
			div.style.top="11px";
			div.style.right="5px";
			div.style.border="2px dotted black";
			div.style.padding="6px 15px";
			div.style.width="30px";
			div.style.textAlign="center";
			div.style.backgroundColor="#FFFFFF";
			div.style.fontWeight="bold";
			div.id="session_timer";
			document.body.appendChild(div);
			var min = Math.floor(session_time/60).toString();
			var sec = Math.floor(session_time%60).toString();
			document.getElementById("session_timer").innerHTML = (min.length<2?"0"+min:min)+":"+(sec.length<2?"0"+sec:sec);
			session_time--;
			session_timerID=window.setInterval(function(){
				if(session_time==10&&<?php echo ($_SERVER['REMOTE_ADDR']==_inmanage_ip?'true':'false');?>){
					document.location.reload();
				}
				if(session_time==0){
				 //  document.location.reload();
					alert("*******************************************\n\nפג תוקף זמן החיבור!\n\n*******************************************");
					window.clearTimeout(session_timerID);
				}
				min = Math.floor(session_time/60).toString();
				sec = Math.floor(session_time%60).toString();
				document.getElementById("session_timer").innerHTML = (min.length<2?"0"+min:min)+":"+(sec.length<2?"0"+sec:sec);
				session_time--;
			}, 1000);
                        
		})();
	</script>
</body>
</html>