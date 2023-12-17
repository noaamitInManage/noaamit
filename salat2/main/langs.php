<?php 

die("langs - 41");

include_once("../_inc/config.inc.php");
$_ProcessID = 41;

// load lang texts
include_once($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query) or die ("error checking user permissions<br>".mysql_error());
if ($result->num_rows==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

$act = $_GET['act'];
if ($act=="") $act = $_POST['act'];
if ($act=="") $act = "show";

if ($act=="new"){
	// show new form (with existing details)
	if ($_GET['id']!=""){
		if (!is_numeric($_GET['id'])){
			Header("Location: sysusers.php");
			exit();
		}else{
			// yes id - load permissions
			$query = "SELECT * FROM tb_sys_users WHERE (id=".$_GET['id'].")";
			$result = $Db->query($query) or die ("error loading users table");
			if ($result-num_rows>0){
				$item = $Db->get_stream($result);
				$query = "SELECT tb_sys_processes.id,tb_sys_processes.title,tb_sys_processes.parentid,tb_sys_user_permissions.sysuserid,tb_sys_processes.section,tb_sys_processes.tree FROM tb_sys_processes LEFT OUTER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid) AND (tb_sys_user_permissions.sysuserid=".$_GET['id'].") ORDER BY tb_sys_processes.section,tb_sys_processes.tree";
				$result = $Db->query($query) or die ("error loading user permissions table");
				$listcount = $result->num_rows;
				for ($i=0 ; $i<$listcount ; $i++){
					$row = $Db->get_stream($result);
					$list[$row['section']][$row['tree']][$row['parentid']][$row['id']] = Array('title'=>$row['title'],'sysuserid'=>$row['sysuserid']);
					//$list[$i] = $Db->get_stream($result);
				}
			}else{
				// no user - show all users
				Header("Location: sysusers.php");
				exit();
			}
		}
	}else{
		// no id - new user, get processes table
		$query = "SELECT id,title,parentid,section,tree FROM tb_sys_processes ORDER BY section,tree";
		$result = $Db->query($query) or die ("error loading processes table");
		if (mysql_numrows($result)>0){
			$listcount = $result->num_rows;
			for ($i=0 ; $i<$listcount ; $i++){
				$row = $Db->get_stream($result);
				$list[$row['section']][$row['tree']][$row['parentid']][$row['id']] = Array('title'=>$row['title']);
				//$list[$i] = $Db->get_stream($result);
			}
		}else{
			// no processes
			die("NO PROCESSES IN SYSTEM!");
		}
	}
}else if ($act=="after"){
	// do action: save new or update existing
	// txtFName, txtUName, txtEmail, txtPass, cmbIsActive, chkPerm[0...] -- ||
	$perms = $_POST['chkPerm'];
	$permscount = $_POST['permscount'];
	if ($_POST['id']!=""){
		if (!is_numeric($_POST['id'])){
			Header("Location: sysusers.php?act=new&err=2");
			exit();
		}else{
			// yes id - update existing
			$query = "UPDATE tb_sys_users SET fullname='".addslashes($_POST['txtFName'])."', username='".addslashes($_POST['txtUName'])."', email='".$_POST['txtEmail']."', isactive='".$_POST['cmbIsActive']."' WHERE (id=".$_POST['id'].")";
			mysql_unbuffered_query($query) or die ("error updating user");
			if ($_POST['txtPass']!=''){
				$query = "UPDATE tb_sys_users SET password='".md5($_POST['txtPass'])."' WHERE (id=".$_POST['id'].")";
				mysql_unbuffered_query($query) or die ("error updating user");
			}
			Header("Location: sysusers.php?act=static&id=".$_POST['id']);
			exit();
		}
	}else{
		// no id - just add new
		$query = "INSERT INTO tb_sys_users (id,fullname,username,password,email,isactive) VALUES (null,'".addslashes($_POST['txtFName'])."','".addslashes($_POST['txtUName'])."','".md5($_POST['txtPass'])."','".$_POST['txtEmail']."','".$_POST['cmbIsActive']."')";
		$Db->query($query) or die ("error creating user");
		$newuserid = mysql_insert_id();
		Header("Location: sysusers.php?act=static&id=".$newuserid);
		exit();
	}
}else if ($act=="del"){
	// delete selected user
	if (($_GET['id']=="") || (!is_numeric($_GET['id']))){ Header("Location: sysusers.php"); exit(); }
	$query = "DELETE FROM tb_sys_user_permissions WHERE (sysuserid=".$_GET['id'].")";
	mysql_unbuffered_query($query) or die ("error deleting user(1)");
	$query = "DELETE FROM tb_sys_users WHERE (id=".$_GET['id'].")";
	mysql_unbuffered_query($query) or die ("error deleting user(2)");
	Header("Location: sysusers.php?act=static");
	exit();
}else if ($act=="static"){
	// update users file
	$query = "SELECT * FROM tb_sys_users";
	$result = $Db->query($query);
	$fileStr = "";
	while ($row = $Db->get_stream($result)){
		if ($fileStr!='') $fileStr .= ",\n";
		$fileStr .= $row['id']." => Array('fullname'=>'".addslashes($row['fullname'])."','username'=>'".$row['username']."','password'=>'".$row['password']."','email'=>'".$row['email']."','isactive'=>'".$row['isactive']."')";
	}
	$fileStr = "<?php  \n\$sysusersArr = Array(\n$fileStr\n);\n ?>";
	@unlink($_project_server_path.$_salat_path."_static/sysusers.inc.php");
	$file = fopen($_project_server_path.$_salat_path."_static/sysusers.inc.php",'w');
	fwrite($file,$fileStr);
	fclose($file);
	if ((int)$_GET['id']>0){
		// update permissions file
		$query = "SELECT tb_sys_processes.id,tb_sys_processes.section,tb_sys_processes.tree,tb_sys_processes.parentid FROM tb_sys_processes RIGHT OUTER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid) AND (tb_sys_user_permissions.sysuserid=".(int)$_GET['id'].") WHERE (NOT ISNULL(tb_sys_processes.id))";
		$result = $Db->query($query);
		$fileStr = "";
		while ($row = $Db->get_stream($result)){
			$fileStr .= "\$treebyuserArr['".$row['section']."']['".$row['tree']."']['".$row['parentid']."'][] = '".$row['id']."'; \n";
		}
		$fileStr = "<?php  \n\$treebyuserArr = Array(); \n$fileStr ?>";
		@unlink($_project_server_path.$_salat_path."_trees/permissions/treebyuser".(int)$_GET['id'].".php");
		$file = fopen($_project_server_path.$_salat_path."_trees/permissions/treebyuser".(int)$_GET['id'].".php",'w');
		fwrite($file,$fileStr);
		fclose($file);
	}
	// return 
	header("location: sysusers.php?menurefresh=yes&tid=".$_GET['id']);
	exit();
}else{
	include_once($_project_server_path.$_salat_path."_static/sysusers.inc.php");
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="rtl">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<script language="JavaScript" src="../_public/formvalid.js"></script>
	<script language="JavaScript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script language="javascript">
		function doDel(lid){
			if (confirm("הנך עומד למחוק רשומה זו.\nהפריטים המקושרים לרשומה זו לא ימחקו באופן אוטומטי!\nהאם להמשיך ולמחוק רשומה זו?"))
				document.location = "?act=del&id=" + lid;
		}
		function doNew(){
			if (document.frmNew.txtUName.value != ""){
				document.frmNew.submit();
			}else{
				alert("חובה להזין שם משתמש");
				document.frmNew.txtUName.focus();
			}
		}
	</script>
</head>
<body onLoad="top.framMenu.NodeByKey('sysusers','click')">
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="maindiv">
<?php  if ($act=="show"){ ?>
	<span style="color:<?php echo $_color_normal;?>;font-size:16px;font-weight:bold;">משתמשים</span> | <a href="?act=new" class="mainlink">הוספת משתמש חדש</a>
	<br><br>
	<?php  if ($_GET['err']!="") printError(); ?>
	<table width="500" border="0" cellpadding="3" cellspacing="1" style="border:dotted 1px <?php echo $_color_normal;?>;">
	<tr style="color:<?php echo $_color_normal;?>;font-weight:bold;">
		<td align=center><b>קוד</b></td>
		<td align=center><b>שם מלא</b></td>
		<td align=center><b>שם משתמש</b></td>
		<td align=center><b>אימייל</b></td>
		<td align=center><b>האם פעיל</b></td>
		<td align=center><b>עריכה</b></td>
		<td align=center><b>מחיקה</b></td>
	</tr>
	<?php  foreach ($sysusersArr as $key => $val){ ?>
	<tr onMouseOver="this.className='mover';" onMouseOut="this.className='mout';"> <!-- DD4B2E -->
		<td align=center><?php echo $key;?></td>
		<td align=center><?php echo stripslashes($val['fullname']);?></td>
		<td align=center><?php echo $val['username'];?></td>
		<td align=center dir=ltr><?php echo $val['email'];?></td>
		<td align=center><img src='../images/<?php echo $val['isactive'];?>.gif' border=0 align=absmiddle></td>
		<td align=center><a href="?act=new&id=<?php echo $key;?>"><img src="../images/edit.png" border=0 align=absmiddle height=16 width=16></a></td>
		<td align=center><a href="javascript:doDel(<?php echo $key;?>);"><img src="../images/delete.png" border=0 align=absmiddle height=16 width=16></a></td>
	</tr>
	<?php  } if (COUNT($sysusersArr)==0) print "<tr><td colspan=7 align=center>לא קיימים משתמשים במערכת</td></tr>"; ?>
</table>
<?php  }else if ($act=="new"){ ?>
	<span style="color:<?php echo $_color_normal;?>;font-size:16px;font-weight:bold;">משתמשים</span> | <a href="?act=show" class="mainlink">הצג הכל</a>
	<br><br>
	<?php  if ($_GET['err']!="") printError(); ?>
	<form name="frmNew" action="?act=after" method="post">
	<input type=hidden name="id" value="<?php  print $item['id']; ?>">
	<input type=hidden name="permscount" value="<?php  print $listcount; ?>">
	<table width="80%" border="0" cellpadding="3" cellspacing="1" style="border:dotted 1px <?php echo $_color_normal;?>;">
		<tr><td colspan=2><b>פרטי משתמש</b></td></tr>
		<tr>
			<td>שם מלא:</td>
			<td><input type=text name="txtFName" id="txtFName" value="<?php  print htmlspecialchars($item['fullname']); ?>"></td>
		</tr>
		<tr>
			<td>שם משתמש:</td>
			<td><input type=text name="txtUName" id="txtUName" value="<?php  print htmlspecialchars($item['username']); ?>"></td>
		</tr>
		<tr>
			<td>סיסמא:</td>
			<td><input type=text name="txtPass" id="txtPass" value="">&nbsp;שינוי ערך, ישנה את הסיסמא</td>
		</tr>
		<tr>
			<td>אימייל:</td>
			<td><input type=text name="txtEmail" id="txtEmail" value="<?php  print $item['email']; ?>"></td>
		</tr>
		<tr>
			<td>האם פעיל:</td>
			<td><select name="cmbIsActive" id="cmbIsActive"><?php  BuildCombo($yesnoArr,$item['isactive']); ?></select></td>
		</tr>
		<tr><td colspan=2><b>הרשאות משתמש</b></td></tr>
		<tr><td colspan=2>
		<?php 
		$i = 0;
		foreach ($list as $section_id => $sec_arr){
			print "<span style='width:15px;'></span><big>".$sectionsTop[$section_id]."</big><br>";
			foreach ($sec_arr as $tree_id => $tree_arr){
				print "<span style='width:30px;'></span><b>".$sectionsCombo[$section_id][$tree_id]."</b><br>";
				$i = ShowProcess($tree_arr,$sysprocessArr[$section_id][$tree_id],((is_array($sysprocessArr[$section_id][$tree_id]['-1']))?'-1':'0'),50,$i);
			}
		} 
		?>
		</td></tr>
		<tr><td colspan=2>
			<input type=button value="<?php  if ($item['id']=="") print 'חדש'; else print 'עדכן'; ?>" onClick="javascript:doNew();" style="border:solid 1px <?php echo $_color_dark;?>;color:<?php echo $_color_text;?>;font-size:15px;width:80px;height:22px;font-weight:bold;background-color:<?php echo $_color_normal;?>;" />
			&nbsp;&nbsp;&nbsp;
			<input type=button value="ביטול" onClick="javascript:history.back(-1);" style="border:solid 1px <?php echo $_color_dark;?>;color:<?php echo $_color_text;?>;font-size:15px;width:80px;height:22px;font-weight:bold;background-color:<?php echo $_color_normal;?>;" />
		</td></tr>
	</table>
	</form>
<?php  }else if ($act=="after"){ ?>
	do update or insert new
<?php  }else if ($act=="del"){ ?>
	do delete
<?php  } ?>
</div>

<?php  if ($_GET['menurefresh']=="yes"){ ?>
<script>top.framMenu.MenuRefreshToU('all','<?php echo $_GET['tid'];?>');</script>
<?php  } ?>

</body>
</html>