<?
include_once("../_inc/config.inc.php");
$_ProcessID = 5;

if ($langID=='') $langID=$_SESSION['salatLangID'];

include_once($_project_server_path.$_salat_path."_static/langs/processes/".$_ProcessID.".".$langID.".inc.php");
include_once($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

$file = $_project_server_path.$_salat_path . '/_static/menus/modulesArr.'.$_SESSION["salatLangID"].'.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query) or die ("error checking user permissions");
if (($result->num_rows)==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

include($_project_server_path.$_includes_path."modules.array.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

define("_PAGING_NumOfItems"			, 100);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}">{CONTENT}</a>');

$_Proccess_Main_DB_Table = "tb_sitepages";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = true;

$_Proccess_Has_MultiLangs = false;

/**
 *  option to make the csv export from db
 */
$_Proccess_Has_Csv_Export = false;

/**
 * Array of HardCoded Rows that system users can not EDIT
 */
$_Proccess_HC_RowsID_Arr_NOT_EDITABLE = array(
);

/**
 * Array of HardCoded Rows that system users can not DELETE
 */
$_Proccess_HC_RowsID_Arr_NOT_DELETABLE = array(

);

/**
 * Array of forwarded parameters from $_REQUEST
 */
$_Proccess_FW_Params = array(
);

$_yesNo_arr = array("No", "Yes");

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
	include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
	$fwParams[]=$param.'='.$_REQUEST[$param];
}
$fwParams = htmlspecialchars('&'.implode('&', $fwParams));

$act = $_REQUEST['act'];
if ($act=='') $act="show";
$obj_id = (int)$_REQUEST['id'];

if($act=='new'){
	if($obj_id){
		$query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
		$result 	= $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
		$row    	= $Db->get_stream($result);
		$submit	   	= $_LANG['BTN_UPDATE'];
	}else{
		$submit	  	= "Save";
	}
	$submitStay = $submit . ' And stay here';
}elseif($act=='move'){
	reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}elseif($act=='after'){
	if($obj_id){
		$query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
		$result = $Db->query($query);// or db_showError(__FILE__, __LINE__, $query);
		$_REQUEST["inner_id"] = $_REQUEST["mdl_id"];
		if($_Proccess_Has_MultiLangs){
			td_Update($_Proccess_Main_DB_Table,$obj_id);
		}
	}else{
		$query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
		$result = $Db->query($query);
		$newOrder = mysqli_result($result,0,0);
		$query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
		$result = $Db->query($query) ;//or db_showError(__FILE__, __LINE__, $query);
		$_REQUEST["inner_id"] = $_REQUEST["mdl_id"];
		if($_Proccess_Has_MultiLangs){
			td_Insert($_Proccess_Main_DB_Table,$obj_id);
		}
		if($_Proccess_Has_Ordering_Action){
			setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $obj_id);
		}
	}

	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
		meta_UpdateTags();
	}

	module_updateStaticFiles();

	if(!empty($_REQUEST['stay'])) {
		header("Location: ?act=new&id=" . $obj_id . $fwParams);
	} else {
		header ("Location: ?act=show".$fwParams);
	}
	exit();
}elseif($act=="del"){
	$query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$result = $Db->query($query);
	if($_Proccess_Has_Ordering_Action){
		fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
	}
	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
		meta_DeleteTags();
	}
	module_updateStaticFiles();
	header("location:?act=show".$fwParams);
	exit();
}else{
	$whereArr = array();
	$_REQUEST['orderby'] = 'mdl_id';
	$_REQUEST['ordertype'] = 'asc';
	if($_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}

	$where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
	$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if(array_key_exists($_REQUEST['orderby'], $fieldsArr)){
		$query .= " ORDER BY `{$_REQUEST['orderby']}`";
		if($_REQUEST['ordertype']){
			$query .= ' '.$Db->make_escape($_REQUEST['ordertype']);
		}
	}elseif($_Proccess_Has_Ordering_Action){$query .= " ORDER BY order_num";}
	if(isset($_REQUEST['csv']) && ($_REQUEST['csv'])){
		query_to_csv($query,$_Proccess_Main_DB_Table.'_log_'.date('d_m_Y').'.csv',true);
		exit();
	}
	$resultArr = getSqlPagingArr($query);
}


function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table;

	/*
	updateStaticFile("SELECT * FROM {$_Proccess_Main_DB_Table}",
					'/_static/modules.inc.php',
					'modulesArr','id',true);
	*/
	$Db = Database::getInstance();
	$query = "SELECT `id`, `mdl_id`, `mdl_name`, `is_static` FROM {$_Proccess_Main_DB_Table}";
	$result = $Db->query($query);
	$moduleNameArr = array();
	while ($row = $Db->get_stram($result)){
		$moduleNameArr[$row['mdl_id']][$row['is_static']]=$row['mdl_name'];
	}
	updateStaticFile($moduleNameArr, '/_static/modules.inc.php', 'moduleNameArr');

}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?=$_LANG['salat_dir'];?>">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<script type="text/javascript" src="/salat2/htmleditor/fckeditor/fckeditor.js"></script>
	<script type="text/javascript" src="../_public/datetimepicker.js"></script>
	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">
	function doDel(rowID,ordernum){
		if(confirm("<?=$_LANG['DEL_MESSAGE'];?>")){
			document.location.href = "?act=del&id="+rowID+"&order_num="+ordernum;
		}
	}

	function validateForm(clickEle) {
      document.getElementById("loader").style.display = "";
      if(clickEle) clickEle.style.display = "none";
      return true;
	}
	</script>
	<style type="text/css">
		.normTxt.hover td{
			background:wheat;
		}
	</style>
</head>
<?=$_salat_style;?>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?=$_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?=$fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?=$fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
<div class="maindiv">
<br/>
<? if($act=="show"){ ?>
	<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
		<tr class="dottTbl">
			<? $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
			<? if($_Proccess_Has_Ordering_Action){ ?>
			<td width="70">סדר</td>
			<? } ?>
			<td width="50">&nbsp;</td>
		</tr>
		<? for($count = mysql_num_rows($resultArr['result']),$i=0;$row = mysql_fetch_assoc($resultArr['result']);$i++){ ?>
		<tr class="normTxt" >
			<? $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
			<? if($_Proccess_Has_Ordering_Action){ ?>
			<td class="dottTblS"><?=outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
			<? } ?>
			<td class="dottTblS" style="width: 100px; ">
				<? if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
				<input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>" onclick="javascript: location.href='?act=new&id=<?=$row['id'].$fwParams;?>';" /> &nbsp;
				<? } if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)){ ?>
				<input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>" onclick="javascript:doDel(<?=$row['id'];?>, '<?=(int)$row['order_num'].$fwParams;?>');" /> &nbsp;
				<? } ?>
			</td>
		</tr>
		<? } if ($resultArr['paging']){ ?>
		<tr class="normTxt">
			<td class="dottTblS" colspan="<?=$columns_count+1+($_Proccess_Has_Ordering_Action?1:0);?>" align="center"><?=$resultArr['paging'];?></td>
		</tr>
		<? } ?>
		</table><br/>
		<? if (mysql_num_rows($result)==0){ ?>
			אין נתונים
		<?php  }else if($_Proccess_Has_Csv_Export){?>
			<div class="csv" style="direction: rtl;">
				<a href="?act=show<?=$fwParams;?>&csv=1" >
					<img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="16" height="16">

					<?=$_LANG['EXPORT_TO_CSV'];?>
				</a>
			</div>
		<?} ?>
<? }elseif($act=="new"){ ?>
	<form  name="form" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="act" value="after" />
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
		<tr class="dottTbl">
            <td colspan="2"><?=$_LANG['TXT_ADD&EDIT'];?></td>
		</tr>
		<? fields_get_form_fields($fieldsArr, $row, ($obj_id==0?'add':'new'), false);
		if($_Proccess_Has_MetaTags){ ?>
		<tr>
			<td class="dottTblS" colspan="2">
			<?
			$_META_FORM="form";
			$_META_TITLE="title";
			$_META_DESC="summary";
			$lang = "en";
			$_REQUEST['id']=$row['mdl_id'];
			include_once($_project_server_path.$_salat_path."_inc/metaform.inc.php");
			?>
			</td>
		</tr><? } ?>
		<tr>
			<td class="dottTblS" colspan="3">
				<input type="submit" name="send" value="<?=$submit;?>" onclick="return validateForm(this);" class="buttons" />
				<input type="submit" name="stay" value="<?=$submitStay;?>" onclick="return validateForm(this);" class="buttons" />
				<div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> Processing . . .</div>
			</td>
		</tr>
	</table>

	</form>
<? } ?>
</div>
</body>
</html>