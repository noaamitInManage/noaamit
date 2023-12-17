<?php

include("_inc/config.inc.php");

// load lang texts

include($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

// tree class
include($_project_server_path.$_salat_path."tree/tree.class.php");

// tree per user

include($_project_server_path.$_salat_path."_trees/treebysection.php");
include($_project_server_path.$_salat_path."_trees/permissions/treebyuser".$_SESSION['salatUserID'].".php");//$treebyuserArr

foreach ($treebyuserArr AS $section=>$proccess) {
	foreach ($proccess['main'] AS $proc_id=>$childs) {
		ksort($treebyuserArr[$section]['main'][$proc_id]);
	}
}

$_SALATFRAMESCOLS_OPEN = "180,*";
$_SALATFRAMESCOLS_CLOSE = "50,*";
if ($_LANG['salat_dir']=='rtl'){
	$_SALATFRAMESCOLS_CLOSE = "*,50";
	$_SALATFRAMESCOLS_OPEN = "*,180";
}

// set 'section' and 'tree'
$section = $_GET['section'];
if ($section=="") $section = $sectionsTop_Default;

$sel_tree = $_GET['sel_tree'];
if ($sel_tree=="") $sel_tree = $sectionsCombo_Default;

// check if static-php-tree-file exists, if so show it, else create it > save it > and show it

$_statictreefile = $_project_server_path.$_salat_path."_static/trees/".$section.".".$sel_tree.".".$_SESSION['salatUserID'].".tree.".$_SESSION['salatLangID'].".php";

// if static files should be re-written (categories, permissions...)
if ($_GET['dorefresh']=='this'){
	// delete the current tree
	@unlink($_statictreefile);
}elseif ($_GET['dorefresh']=='all'){
	// delete all trees
	// also load system languages - to delete by
	include($_project_server_path.$_salat_path."_static/languages.inc.php");
	foreach ($sectionsCombo as $key_sec => $val_sec){
		foreach ($val_sec as $key_tree => $val_tree){
			foreach ($langsArr as $lid => $lval){
				@unlink($_project_server_path.$_salat_path."_static/trees/".$key_sec.".".$key_tree.".".($_GET['tid']!=''?$_GET['tid']:$_SESSION['salatUserID']).".tree.".$lid.".php");
			}
		}
	}
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<meta name="robots" content="noindex,nofollow">
	<link rel="StyleSheet" href="_public/main.css" type="text/css">
	<SCRIPT LANGUAGE="JavaScript"> if (window.parent==window) location.href = 'frames.php'; </SCRIPT>
	<?php  include($_project_server_path.$_salat_path."tree/tree.css.php"); ?>
	<?php  include($_project_server_path.$_salat_path."tree/tree.js.php"); ?>
	<script language="javascript">
	imgPlusTop.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/p_node_r.gif";
	imgPlusBottom.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/pb_node_r.gif";
	imgMinusTop.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/m_node_r.gif";
	imgMinusBottom.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/mb_node_r.gif";
	imgFolderOpen.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/folderopen.gif";
	imgFolderClose.src = "tree/icons_<?php echo $_LANG['salat_dir'];?>/folder_close.gif";
	</script>
	<?php  include($_project_server_path.$_salat_path."_trees/".$section.".".$sel_tree.".js.php"); ?>
	<script language="javascript">
	function doCngSection(section){
		document.location = "menu.php?section=" + section;
	}
	function doCngTree(tree){
		document.location = "menu.php?section=<?php echo $section;?>&sel_tree=" + tree;
	}
	function MenuRefresh(dor){
		document.location = "../menu.php?section=<?php echo $section;?>&sel_tree=<?php echo $sel_tree;?>&dorefresh=" + dor;
	}
	function MenuRefreshToU(dor,tid){
		document.location = "../menu.php?section=<?php echo $section;?>&sel_tree=<?php echo $sel_tree;?>&dorefresh=" + dor + "&tid=" + tid;
	}
	function ShowHelpFrame(){
		if (top.document.getElementById("top").rows=="50,*,1"){
			top.document.getElementById("top").rows="50,*,75";
			top.framHelp.document.location = "help.php?section=<?php echo $section;?>&sel_tree=<?php echo $sel_tree;?>&page=" + top.framMain.document.location;
			document.getElementById("helpbutton").value = "<?php echo $_LANG['help_hide'];?>";
		}else{
			top.document.getElementById("top").rows="50,*,1";
			document.getElementById("helpbutton").value = "<?php echo $_LANG['help_show'];?>";
		}
	}
	function ShowMenuFrame(){
		if (top.document.getElementById("framMid").cols=="<?php echo $_SALATFRAMESCOLS_OPEN;?>"){
			top.document.getElementById("framMid").cols="<?php echo $_SALATFRAMESCOLS_CLOSE;?>";
			document.getElementById("menubutton").value = "<?php echo $_LANG['menu_show'];?>";
			document.getElementById("menudiv").style.display = "none";
		}else{
			top.document.getElementById("framMid").cols="<?php echo $_SALATFRAMESCOLS_OPEN;?>";
			document.getElementById("menubutton").value = "<?php echo $_LANG['menu_hide'];?>";
			document.getElementById("menudiv").style.display = "block";
		}
	}
	</script>
	<?php echo $_salat_style;?>
</head>
<body topmargin=0 rightmargin=1 leftmargin=1 bottommargin=2>
<!--<input type="button" value="<?php echo $_LANG['menu_hide'];?>" id="menubutton" name="menubutton" onclick="ShowMenuFrame(); this.blur()" style="width:100%;height:16px;font-family:arial;font-size:11px;color:gray;border:solid 1px litegray;background-color:litegray;font-family:arial;">-->
<br>
<div id="menudiv">
<div style="color:<?php echo $_color_text;?>;width:148px;text-align:center;height:22px;background-color:#0573C7;font-size:16px;font-weight:bold;padding-top:2px;padding-right:6px;padding-left:6px;"><?php echo $_LANG['salat_menu'];?></div>

<?php
// show tree
if (is_file($_statictreefile)){
	// if statis file exists, show it
	include($_statictreefile);
}else{
	// else, create and show it
	// load the acctual tree stracture
	include($_project_server_path.$_salat_path."_trees/".$section.".".$sel_tree.".tree.".$_SESSION['salatLangID'].".php");
	ob_start();
?>

	<div style="height:5px;"></div>
	<?php  if (COUNT($sectionsTop)>1){ ?>
		<?php  foreach ($sectionsTop as $key => $val){ ?>
			<input type="button" value="<?php echo $val;?>" class="<?php echo ($section==$key?'menubuttonselected':'menubutton');?>" onclick="doCngSection('<?php echo $key;?>')">&nbsp;
		<?php  } ?>
		<div style="height:5px;"></div>
	<?php  } ?>

	<?php  if (COUNT($sectionsCombo[$section])>1){ ?>
		<select id="treeselector" name="" onchange="doCngTree(this.value)" class="menuoptions">
		<?php  foreach ($sectionsCombo[$section] as $key => $val){ ?>
		<option value="<?php echo $key;?>" <?php echo ($sel_tree==$key?'selected':'');?>><?php echo $val;?></option>
		<?php  } ?>
		</select>
		<div style="height:5px;"></div>
	<?php  } ?>

	<?php  BuildTree($tree,$treebyuserArr[$section][$sel_tree]); ?>

	<div style="height:3px;"></div>
	<img height="10" width="160" src="images/menubottom.gif" border="0" />

	<br /><br /><br />
	<?php  /*
	<div align="center"><input type="button" id="helpbutton" value="<?php echo $_LANG['help_show'];?>" onclick="ShowHelpFrame();" style="color:<?php echo $_color_text;?>;border:solid 1px <?php echo $_color_dark;?>;background-color:<?php echo $_color_lite;?>;"></div>
	*/ ?>
	<script>
	// close help frame if opened
	top.document.getElementById("top").rows="50,*,1";
	</script>
	<?php
	// get output buffer
	$content = ob_get_contents();
	// save to static file
	$file = fopen($_statictreefile,'w');
	fwrite($file,$content);
	fclose($file);
	// clean output buffer
	ob_end_clean();
	// show static file content
	if (is_file($_statictreefile)) include($_statictreefile);
	else print "Error Occured: unable to write Static Tree File!<br>Please report your system admin regarding this problem.";
	?>
<?php  } ?>

</div>

</body>
</html>