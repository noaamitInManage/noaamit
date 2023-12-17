<?php 

include_once("../_inc/config.inc.php");
$_ProcessID = 7;

include_once($_project_server_path.$_salat_path."_static/langs/processes/".$_ProcessID.".".$_SESSION['salatLangID'].".inc.php");
include_once($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query);
if ($result->num_rows==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}
include_once($_project_server_path."salat2/_static/mainsettings.inc.php");
//include_once($_project_server_path."salat2/_static/shopproducts.inc.php");

$act = $_GET['act'];
if ($act=="") $act = $_POST['act'];
if ($act=="") $act = "show";

$show_not_array = array(16,9,10);
$radio_box_array = array(13);

if ($act == "show") {
	$query = "SELECT * FROM tb_settings ";
	if(count($show_not_array)){
		$query .= " WHERE id NOT IN (".implode(",",$show_not_array).")";
	}
	$result = $Db->query($query);//
}
	
if ($act=="after"){
	$clause = array();
	$settings = $_REQUEST['settings'];
	foreach ($settings as $setting_id=>$value){
		$query = "UPDATE tb_settings SET content='".escapesql($value)."' WHERE id=".$setting_id;
		mysql_unbuffered_query($query) or db_showError(__FILE__,__LINE__,$query);
	}
	$query = "SELECT * FROM tb_settings";
	$result = $Db->query($query) or db_showError(__FILE__,__LINE__);
	$arr = array();
	if (($result->num_rows)){
		while ($r = $Db->get_stream($result)){$arr[$r['id']] = $r['content'];};
	};
	$f = fopen(_salat_static_path."mainsettings.inc.php","w");
	fwrite($f,"<?php php\n\$sitesettingsArr = ".var_export($arr,true).";\n?>");
	fclose($f);
	header("location:?act=show");
	exit();
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<script language="JavaScript" src="../_public/formvalid.js"></script>
	<script language="JavaScript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script language="javascript">
		function doNew(){
			for (i=0 ; i<document.frmNew.elements.length ; i++){
				elm = document.frmNew.elements[i];
				attr = elm.getAttribute("required");
				if ((attr != null) && (attr != "")){
					if (elm.value==''){
						alert(attr);
						elm.focus();
					}
				}
			}
			document.frmNew.submit();
		}
	</script>
	<?php echo $_salat_style?>
</head>
<body> 
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="maindiv" style="text-align:center;">
<?php  if ($act=="show"){ ?>
	<span style="color:<?php echo $_color_normal;?>;font-size:16px;font-weight:bold;"><?php echo ($_LANG['salat_dir']=='rtl'?'הגדרות כלליות':'General Settings')?></span>
	<br><br>
	<?php  if ($_GET['err']!="") printError(); ?>
	<center>
		<form action="?act=after" method="post">
		<table border="0" cellpadding="3" cellspacing="1" width="600">
			<tr class="dottTbl">
				<td colspan="3">Edit settings</td>
			</tr>
			<?php while ($row = mysql_fetch_assoc($result)) {?>
			<tr>
				<td class="dottTblS"><?php echo $row['id']?></td>
				<td class="dottTblS"><?php echo $row['name']?> :</td>
				<td class="dottTblS" style="text-align:center;">
					<?php if(!in_array($row['id'],$radio_box_array)){?>
						<input type="text" value="<?php echo $row['content']?>" name="settings[<?php echo $row['id']?>]" />
					<?php }else{?>	
						<input type="radio" value="1" <?php echo $row['content']!='0'?'checked="checked"' : '';?> name="settings[<?php echo $row['id']?>]" id="yes_<?php echo $row['id']?>" /><label style="cursor:pointer;" for="yes_<?php echo $row['id']?>">Yes</label>
						<input type="radio" value="0" <?php echo $row['content']=='0'?'checked="checked"' : '';?> name="settings[<?php echo $row['id']?>]" id="no_<?php echo $row['id']?>" /><label style="cursor:pointer;" for="no_<?php echo $row['id']?>">No</label>
					<?php }?>
				</td>
			</tr>
			<?php }?>
			<tr>
				<td class="dottTbls" colspan="2" style="text-align:center;	">
					<input type="submit" value="Update" class="buttons" />
				</td>
			</tr>
		</table>
		</form>
	<?php }?>
	</center>
</div>
</body>
</html>
