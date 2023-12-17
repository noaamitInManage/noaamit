<?php 

if($_SERVER['REMOTE_ADDR'] != '62.219.212.139') die("Under Construction");

include_once("../_inc/config.inc.php");
$_ProcessID = 5;

if ($langID=='') $langID=$_SESSION['salatLangID'];

include_once($_project_server_path.$_salat_path."_static/langs/processes/".$_ProcessID.".".$langID.".inc.php");
include_once($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

$file = $_project_server_path.$_salat_path . '/_static/menus/modulesArr.'.$_SESSION["salatLangID"].'.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query);
if ($result->num_rows==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

include($_project_server_path.$_includes_path."modules.array.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

define("_PAGING_NumOfItems"			, 25);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}">{CONTENT}</a>');

$_Proccess_Main_DB_Table = "tb_settings";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

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

$_yesNo_arr = array(
	'0' => 'לא',
	'1' => 'כן',
);

$_keyType_arr = array(
   0 => '-- אנא בחר --',
   1 => 'תיבת טקסט',
   2 => 'תיבת טקסט מרובת שורות',
   3 => 'עורך טקסט',
   4 => 'רשימת בחירה'
);

$_showError_arr = array(
   0 => "שגיאה"
);

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

// get all categoris & articles and make them into one big happy combo :-) ... Roni S (©) 2010 Was Here !
$articles = array();
$res = $Db->query("SELECT tb1.id,tb1.title,tb2.title AS category_title FROM `tb_articles` AS tb1 LEFT JOIN `tb_article_categories` AS tb2 ON tb1.category_id=tb2.id ORDER BY tb1.id");
while($article = $Db->get_stream($res)) {
   $articles[$article['category_title']][$article['id']] = $article['title'];
}

$_allComboData_arr = array(
   "כן / לא" => $_yesNo_arr,
   "מאמרים" =>$articles
);

/**
 * ===============================================
 * How to: Populate Select Boxes with custom data
 * ===============================================
 * @author By Nir Azuelos
 * @since 9/12/10
 *
 * [+] In order to populate select boxes properly, do the following:
 *
 * Step 1.
 *    + Construct an array: Array(Value => Title, Value => Title)
 *    + For exmaple: array("Nir", "Roni", "Gal", "Albert")
 *
 *    - If you want to use option groups, construct the array in this manner:
 *       Array("Optgroup Title" => Array(Value => Title), "Optgroup Title" => Array(Value => Title))
 *    - For exmaple: array("People" => array("Rotem", "Jenny", "Eyal"), "Foods" => array("Pizza", "Sushi", "Salad"))
 *
 * Step 2.
 *    + Then add your constructed array to $_allComboData_arr.
 *    + For example: $_allComboData_arr["My Array"] = $_myCustomArray
 *
 * +---------------------------------------------------------------------------------------------------------------+
 * Here is a real-life example for auto-populating select box with data fecthed from mysql (Taken from Wobi.co.il):
 * +---------------------------------------------------------------------------------------------------------------+
 * $articles = array();
 * $res = mysql_query("SELECT tb1.id,tb1.title,tb2.title AS category_title FROM `tb_articles` AS tb1 LEFT JOIN `tb_article_categories` AS tb2 ON tb1.category_id=tb2.id ORDER BY tb1.id");
 * while($article = mysql_fetch_assoc($res)) {
 *    $articles[$article['category_title']][$article['id']] = $article['title'];
 * }
 * $_allComboData_arr["My Array"] = $articles
 * +-------------------------------------------------------+

 * It can't be any easier than that :-)
 */

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

if($act=='new' || $act=='new_edit'){
	if($obj_id){
		$query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
		$result 	= $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
		$row    	= $Db->get_stream($result);
		$submit	   	= $_LANG['BTN_UPDATE'];
	}else{
		$submit	  	= $_LANG['BTN_ADD'];
	}
	$submitStay = $submit . " {$_LANG['AND_STAY']}" ;
}elseif($act=='move'){
	reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}elseif($act=='after') {
	if($obj_id){
		$query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
		$result = $Db->query($query);
		$_REQUEST['inner_id'] = $obj_id;
		if($_Proccess_Has_MultiLangs){
			td_Update($_Proccess_Main_DB_Table,$_REQUEST['inner_id']);
		}
	}else{
		$query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
		$result = $Db->query($query);
		$newOrder = mysqli_result($result,0,0);
		$query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
		$result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
		$_REQUEST['inner_id'] = $Db->get_insert_id();
		if($_Proccess_Has_MultiLangs){
			td_Insert($_Proccess_Main_DB_Table,$_REQUEST['inner_id']);
		}
		if($_Proccess_Has_Ordering_Action){
			setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
		}
	}

	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
	}

	module_updateStaticFiles();

	if(!empty($_REQUEST['stay'])) {
		header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
	} else {
		header ("Location: ?act=show".$fwParams);
	}
	exit();
} elseif($act=='after_edit') {
	if($obj_id){
		$query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsEditArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
		$result = $Db->query($query);
		$_REQUEST['inner_id'] = $obj_id;
		if($_Proccess_Has_MultiLangs){
			td_Update($_Proccess_Main_DB_Table,$_REQUEST['inner_id']);
		}
	}

	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
	}

	module_updateStaticFiles();

	if(!empty($_REQUEST['stay'])) {
		header("Location: ?act=new_edit&id=" . $_REQUEST['inner_id'] . $fwParams);
	} else {
		header ("Location: ?act=show".$fwParams);
	}
	exit();
} elseif($act=="del"){
	$query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$result = $Db->query($query);
	if($_Proccess_Has_Ordering_Action){
		fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
	}
	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
	}
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}else{
	$whereArr = array();

	if($_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}

	$where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
	$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if(array_key_exists($_REQUEST['orderby'], $fieldsArr)){
		$query .= " ORDER BY `{$_REQUEST['orderby']}`";
		if($_REQUEST['ordertype']){
			$query .= ' '.$Db->make_escape($_REQUEST['ordertype']);
		}
	}elseif($_Proccess_Has_Ordering_Action){$query .= " ORDER BY order_num";}

	else {
	$query .= " ORDER BY title asc";
	}
	if(isset($_REQUEST['csv']) && ($_REQUEST['csv'])){
		query_to_csv($query,$_Proccess_Main_DB_Table.'_log_'.date('d_m_Y').'.csv',true);
		exit();
	}
	$resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table;
	updateStaticFile("SELECT `keyname`, `content` FROM `{$_Proccess_Main_DB_Table}`", '/_static/settings.inc.php', 'settingsArr', 'keyname', true, true);
}

function saveFieldType() {
   global $obj_id, $_Proccess_Main_DB_Table,$Db;
   $fieldType = $Db->make_escape($_POST['fieldtype']);
   $fieldSql = "SELECT `fieldtype` FROM `{$_Proccess_Main_DB_Table}` WHERE `id` = {$obj_id} LIMIT 1";
   $fieldRes = $Db->query($fieldSql);
   $fieldArr = $Db->get_stream($fieldRes);
   $noDataCurroptionArr = array(
      array(1, 2)
   );
   if($fieldArr['fieldtype'] == $fieldType) return $fieldType;
   foreach ($noDataCurroptionArr as $value)
      if(in_array($fieldArr['fieldtype'], $value) && in_array($fieldType, $value)) return $fieldType;
   $fixSql = "UPDATE `{$_Proccess_Main_DB_Table}` SET `content` = '' WHERE `id` = {$obj_id}";
   $Db->unbuffered_query($fixSql);
   return $fieldType;
}

function drawContentCombo($fieldContent) {
   global $obj_id, $_Proccess_Main_DB_Table, $_allComboData_arr,$Db;
    $fieldSql = "SELECT `combokey` FROM `{$_Proccess_Main_DB_Table}` WHERE `id` = {$obj_id} LIMIT 1";
    $fieldRes = $Db->query($fieldSql);
    $fieldArr = $Db->get_stream($fieldRes);

    $comboHtml = '<select name="content" id="content">';
    array_unshift($_allComboData_arr[$fieldArr['combokey']], "-- אנא בחר --");

    foreach($_allComboData_arr[$fieldArr['combokey']] as $key => $value) {
       if(is_array($value)) {
          $comboHtml .= '<optgroup label="'.htmlspecialchars($key).'" style="font-style:normal;">'.PHP_EOL;
          foreach($value as $subKey => $subValue) {
             $comboHtml .= '<option value="'.addslashes($subKey).'"'.($subKey == $fieldContent ? 'selected="selected"' : '').'>'.htmlspecialchars($subValue).'</option>';
          }
          $comboHtml .= '</optgroup>';
       } else {
          $comboHtml .= '<option value="'.addslashes($value).'"'.($value == $fieldContent ? 'selected="selected"' : '').'>'.htmlspecialchars($value).'</option>';
       }
    }
    $comboHtml .= '</select>';
    return $comboHtml;
}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
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
	</script>
	<script type="text/javascript" src="/_media/js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript">
	  $(function() {
	     var comboSelection = '<span style="margin-right: 6px; display: none;" id="fieldComboSelection">&raquo;<select style="margin-right: 6px;" name="combokey">';
	     <?php  foreach($_allComboData_arr as $key => $value) { ?>
	        comboSelection += '<option value="<?php echo addslashes($key)?>"<?php echo ($key == $row['combokey'] ? 'selected="selected"' : '')?>><?php echo htmlspecialchars($key)?></option>';
	     <?php  } ?>
	     comboSelection += '</select></span>';
	     $("#fieldType").after(comboSelection);
	     toggleComboSelectionBox();
	     $("#fieldType").live("change", function() { toggleComboSelectionBox(); });
        $("#settingsForm").live("submit", function() {
           if($("#fieldType").val() == 0) {
              alert("אנא בחר סוג");
              return false;
           }
           if($("#content").val() == "-- אנא בחר --") {
              alert("אנא בחר תוכן");
              return false;
           }
           else document.getElementById('loader').style.display='';this.style.display='none';
        });
	  });

	  function toggleComboSelectionBox() {
	     if($("#fieldType").val() == 4) $("#fieldComboSelection").fadeIn();
	     else $("#fieldComboSelection").fadeOut();
	  }
   </script>
	<style type="text/css">
		.normTxt.hover td{
			background:wheat;
		}
	</style>
	<?php echo $_salat_style;?>
</head>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<?php  if($_SESSION['salatUserID'] == 1) { ?>
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="הוסף הגדרה חדשה" />
<?php  } ?>
<div class="maindiv">
<br/>
<?php  if($act=="show"){ ?>
	<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
		<tr class="dottTbl">
			<?php  $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td width="70">סדר</td>
			<?php  } ?>
			<td width="50">&nbsp;</td>
		</tr>
		<?php  for($count = mysql_num_rows($resultArr['result']),$i=0;$row = mysql_fetch_assoc($resultArr['result']);$i++){ ?>
		<tr class="normTxt" >
			<?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?>&nbsp;</td>
			<?php  } ?>
			<td class="dottTblS" style="width: 180px;">
				<?php  if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
				  <?php  if ($_SESSION['salatUserID'] == 1) { ?>
				  <input type="button" class="buttons" value="ערוך מבנה" onclick="javascript: location.href='?act=new&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp; <?php  } ?>
				  <input type="button" class="buttons" value="ערוך תוכן" onclick="javascript: location.href='?act=new_edit&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp;
				<?php  } if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)){ ?>
				  <?php  if ($_SESSION['salatUserID'] == 1) { ?>
				  <input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>" onclick="javascript:doDel(<?php echo $row['id'];?>, '<?php echo (int)$row['order_num'].$fwParams;?>');" /> &nbsp; <?php  } ?>
				<?php  } ?>
			</td>
		</tr>
		<?php  } if ($resultArr['paging']){ ?>
		<tr class="normTxt">
			<td class="dottTblS" colspan="<?php echo $columns_count+1+($_Proccess_Has_Ordering_Action?1:0);?>" align="center"><?php echo $resultArr['paging'];?></td>
		</tr>
		<?php  } ?>
		</table><br/>
		<?php  if (mysql_num_rows($resultArr['result'])==0){ ?>
			אין נתונים
		<?php  }else if($_Proccess_Has_Csv_Export){?>
			<div class="csv" style="direction: rtl;">
				<a href="?act=show<?=$fwParams;?>&csv=1" >
					<img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="16" height="16">

					<?=$_LANG['EXPORT_TO_CSV'];?>
				</a>
			</div>
		<?} ?>
<?php  }elseif($act=="new") {
   if($_SESSION['salatUserID'] != 1) die("אין הרשאות"); ?>
	<form  name="form" id="settingsForm" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="act" value="after" />
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
		<tr class="dottTbl">
			<td colspan="2">הוספה / עריכה</td>
		</tr>
		<?php 
		fields_get_form_fields($fieldsArr, $row, ($obj_id==0?'add':'new'), false);
		?>
		<tr>
			<td class="dottTblS" colspan="3">
				<input type="submit" name="send" value="<?php echo $submit;?>" class="buttons" />
				<input type="submit" name="stay" value="<?php echo $submitStay;?>" class="buttons" />
				<div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> מעבד נתונים, נא להמתין . . .</div>
			</td>
		</tr>
	</table>
	</form>
<?php  }elseif($act=="new_edit") {
   if(!$obj_id) die("שגיאה"); ?>
	<form  name="form" id="settingsForm" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="act" value="after_edit" />
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
		<tr class="dottTbl">
			<td colspan="2">עריכת תוכן</td>
		</tr>
		<?php 
		switch($row['fieldtype']) {
		   case 0: $fieldsEditArr['content']['input']['type'] = "label"; break;
		   case 1: $fieldsEditArr['content']['input']['type'] = "text"; break;
		   case 2: $fieldsEditArr['content']['input']['type'] = "textarea"; break;
		   case 3: $fieldsEditArr['content']['input']['type'] = "htmltext"; break;
		   case 4: $fieldsEditArr['content']['input']['type'] = "label";
	   	break;
		}
		fields_get_form_fields($fieldsEditArr, $row, ($obj_id==0?'add':'new'), false);
		?>
		<tr>
			<td class="dottTblS" colspan="3">
				<input type="submit" name="send" value="<?php echo $submit;?>" class="buttons" />
				<input type="submit" name="stay" value="<?php echo $submitStay;?>" class="buttons" />
				<div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> מעבד נתונים, נא להמתין . . .</div>
			</td>
		</tr>
	</table>
	</form>
<?php  } ?>
</div>
</body>
</html>
