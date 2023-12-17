<?php 

include_once("../_inc/config.inc.php");
$_ProcessID = 4;

include_once($_project_server_path.$_salat_path."_static/langs/processes/".$_ProcessID.".".$_SESSION['salatLangID'].".inc.php");

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query) or die ("error checking user permissions<br>".mysql_error());
if ($result->num_rows==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

$act = $_GET['act'];
if ($act=="") $act = $_POST['act'];
if ($act=="") $act = "show";

$MetaTagsKeys = Array(
	1 => Array('title'=>"description",'title_rtl'=>'תיאור אתר','title_ltr'=>'Description','hint_rtl'=>'בלה בלה בלה','hint_ltr'=>''),
	2 => Array('title'=>"keywords",'title_rtl'=>"מילות חיפוש",'title_ltr'=>'Keywords','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	3 => Array('title'=>"headline",'title_rtl'=>"כותרת קצרה",'title_ltr'=>'Short','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	4 => Array('title'=>"abstract",'title_rtl'=>"תיאור אבסטרקטי",'title_ltr'=>'Abstract','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	5 => Array('title'=>"page-topic",'title_rtl'=>"נושא אתר באנגלית",'title_ltr'=>'Topic','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	6 => Array('title'=>"page-type",'title_rtl'=>"תחום אתר באנגלית",'title_ltr'=>'Field','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	7 => Array('title'=>"classification",'title_rtl'=>"קטלוג מקצועי",'title_ltr'=>'Theme','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	8 => Array('title'=>"category",'title_rtl'=>"קטגוריה",'title_ltr'=>'Category','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	9 => Array('title'=>"rating",'title_rtl'=>"דירוג",'title_ltr'=>'Rating','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה"),
//	10 => Array('title'=>"copyright",'title_rtl'=>"זכויות יוצרים",'title_ltr'=>'Copy Rights','hint_ltr'=>'','hint_rtl'=>"בלה בלה בלה")
);

if ($act=="after"){
	// do action: save new or update existing
	$fileStr = "";
	$fileStr_Code = "";
	foreach ($MetaTagsKeys as $key => $arrval) {
		$fileStr .= "<meta name=\"".$arrval['title']."\" content=\"".addslashes($_POST['value_'.$key])."\">";
		if ($fileStr_Code!='') $fileStr_Code .= ",";
		$fileStr_Code .= $key."=>\"".addslashes($_POST['value_'.$key])."\"";
	}
	// just write this to meta tags not to edit
//	$fileStr .= "<meta name=\"Reply-To\" content=\"Ron Bentata <ronb@inmanage.co.il>\">";
//	$fileStr .= "<meta name=\"author\" content=\"inManage (inmanage.co.il) - Ron Bentata <ronb@inmanage.co.il>\">";
//	$fileStr .= "<meta name=\"SECURITY\" content=\"Public\">";
//	$fileStr .= "<meta name=\"distribution\" content=\"Global\">";
//	$fileStr .= "<meta name=\"audience\" content=\"all\">";
//	$fileStr .= "<meta name=\"distribution\" content=\"GLOBAL\">";
//	$fileStr .= "<meta name=\"revisit-after\" content=\"5 days\">";
//	$fileStr .= "<meta name=\"robots\" content=\"index, follow\">";
//	$fileStr .= "<meta name=\"resource-type\" content=\"document\">";
//	$fileStr .= "<meta name=\"doc-type\" content=\"WebPage\">";
	// save metatags file
	@unlink($_project_server_path.$_salat_path."_static/metaheaders.inc.php");
	$file = fopen($_project_server_path.$_salat_path."_static/metaheaders.inc.php",'w');
	fwrite($file,$fileStr);
	fclose($file);
	// save coding file
	$fileStr_Code = "<?php  \$metatags = Array(".$fileStr_Code."); ?>";
	@unlink($_project_server_path.$_salat_path."_static/metas.inc.php");
	$file = fopen($_project_server_path.$_salat_path."_static/metas.inc.php",'w');
	fwrite($file,$fileStr_Code);
	fclose($file);
	// return 
	Header("Location: meta.php");
	exit();
}else{
	include_once($_project_server_path.$_salat_path."_static/metas.inc.php");
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_ProcessLang['salat_dir'];?>">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<script language="JavaScript" src="../_public/formvalid.js"></script>
	<script language="JavaScript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script language="javascript">
		function doNew(){
			/*
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
			*/
			document.frmNew.submit();
		}
	</script>
	<?php echo $_salat_style?>
</head>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="maindiv">
<?php  if ($act=="show"){ ?>
	<span class="titleTxt"><?php echo ($_LANG['salat_dir']=='rtl'?'תגי מטה':'Meta Tags')?></span>
	<br><br>
	<?php  if ($_GET['err']!="") printError(); ?>
	<form name="frmNew" action="?act=after" method="post">
	<table border="0" cellpadding="3" cellspacing="1" class="dottTblo">
		<?php  foreach ($MetaTagsKeys as $key => $valarr){ ?>
		<tr>
			<td width="120" valign="top" class="normTxt" style="font-whight:bold">
<?php echo $_salat_icon?>
			&nbsp;<b><?php echo $valarr['title_'.$_LANG['salat_dir']];?>:</b></td>
			<td valign="top"><input type="text" name="value_<?php echo $key;?>" id="value_<?php echo $key;?>" required="יש להזין <?php echo $valarr['heb'];?>" size="40" value="<?php echo htmlspecialchars($metatags[$key]);?>" style="border: solid 1px <?php echo $_color_normal;?>;" dir="<?php echo $_LANG['salat_dir'];?>" /></td>
			<td width="200" valign="top"><?php echo $valarr['hint'];?></td>
		</tr>
		<?php  } ?>
		<tr><td colspan=3>
			<input type=button value="<?php echo ($_LANG['salat_dir']=='rtl'?'עדכן':'Update')?>" onClick="javascript:doNew();" class="btn" />
		</td></tr>
	</table>
	</form>
<?php  }else if ($act=="after"){ ?>
	do update
<?php  } ?>
</div>

</body>
</html>