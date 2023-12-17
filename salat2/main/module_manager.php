<?php 
include_once("../_inc/config.inc.php");
$this_dir = basename(dirname(__FILE__));

$_ProcessID = get_module_id(basename(__FILE__),$this_dir); // 05/10/11

error_reporting(E_ALL);
ini_set('display_errors', '0');

if (!isset($langID) || $langID=='') $langID=$_SESSION['salatLangID'];

//include_once($_project_server_path.$_salat_path."_static/langs/processes/".$_ProcessID.".".$langID.".inc.php");
include_once($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'].".inc.php");

$file = $_project_server_path.$_salat_path . '/_static/menus/modulesArr.'.$_SESSION["salatLangID"].'.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=".$_ProcessID."))";
$result = $Db->query($query);
if($result->num_rows==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

include($_project_server_path.$_includes_path."modules.array.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

include($_project_server_path.$_includes_path."class/autoModuleManager.class.inc.php");

$_Proccess_Main_DB_Table = "tb_sys_processes";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = false;

/**
 *  option to make the csv export from db
 */
$_Proccess_Has_Csv_Export = false;


$parentProcesses=array();
$query="SELECT id,title FROM `{$_Proccess_Main_DB_Table}`";
$result=$Db->query($query);
$parentProcesses[0]='-- Choose --';
while($row = $Db->get_stream($result)) {
	$parentProcesses[$row['id']]=$row['title'];
}

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

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
	include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
	$fwParams[]=$param.'='.$_REQUEST[$param];
}
$fwParams = htmlspecialchars('&'.implode('&', $fwParams));

define("_PAGING_NumOfItems"			, 250);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}">{CONTENT}</a>');

if(isset($_REQUEST['act']) && $_REQUEST['act']) {
	$act = $_REQUEST['act'];
} else {
	$act="show";
}

$obj_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

if($act=='new'){
	if($obj_id){
		$query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
		$result 	= $Db->query($query);
		$row    	= $Db->get_stream($result);
		$submit	   	= $_LANG['BTN_UPDATE'];
	}else{
		$row    	= null;
		$submit	  	= $_LANG['BTN_ADD'];
	}
	$submitStay = $submit . ' and stay in this page';
}elseif($act=='move'){
	reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $obj_id);
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}elseif($act=='after'){
	if($obj_id){
		$query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
		$result = $Db->query($query);
		$_REQUEST['inner_id'] = $obj_id;
		if($_Proccess_Has_MultiLangs){
			//td_Update($_Proccess_Main_DB_Table,$_REQUEST['inner_id']);
		}
	}else{

	    if($_SESSION['salatUserID'] == 1) {
            // auto create module files, tables and static folder
            $element_name = str_replace('.php','',$_REQUEST['page']);
            $Auto_module = new autoModuleManager($element_name, $_REQUEST['section'], $_REQUEST['title']);
            if(!$Auto_module->check_files_exist() && !$Auto_module->check_tables_exist()) {
                $Auto_module->create_tables();
                $Auto_module->create_files();
                $Auto_module->create_static_folder();
            } else {
                //die('<hr /><pre>' . print_r('table/files with this name is already exist', true) . '</pre><hr />');
            }
        }

		$query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
		$result = $Db->query($query);
		$_REQUEST['inner_id'] = $Db->get_insert_id();
		if($_Proccess_Has_MultiLangs){
			//td_Insert($_Proccess_Main_DB_Table,$_REQUEST['inner_id']);
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
}elseif($act=="del"){
	$query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$result = $Db->query($query);
	if($_Proccess_Has_Ordering_Action){
		fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
	}
	if($_Proccess_Has_MetaTags){
		include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
	}
	module_updateStaticFiles();
	header("location:?act=show".$fwParams);
	exit();
}else{
	$whereArr = array();
	
	if(isset($_REQUEST['topcat_id']) && $_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}
	
	$where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
	$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if(isset($_REQUEST['orderby']) && array_key_exists($_REQUEST['orderby'], $fieldsArr)){
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
	$Db = Database::getInstance();

    $flatModuleProcessesArr=array();
    $query="SELECT `id`,CONCAT(`page`,'_',`section`)AS `file`
                   FROM {$_Proccess_Main_DB_Table} ";
    $result=$Db->query($query);


    while($row = $Db->get_stream($result)) {
    	$flatModuleProcessesArr[$row['id']]=$row['file'];
    }
    @unlink($_SERVER['DOCUMENT_ROOT'].'/salat2/_static/moduleProcesses.inc.php');
    updateStaticFile($flatModuleProcessesArr, 
					                '/salat2/_static/moduleProcesses.inc.php', 
					                      'moduleProcessesArr');
}

include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<link rel="StyleSheet" href="../_public/faq.css" type="text/css">
	<script type="text/javascript" src="/salat2/htmleditor/fckeditor/fckeditor.js"></script>
	<script type="text/javascript" src="../_public/datetimepicker.js"></script>
	<link rel="stylesheet" href="/_media/css/plugins/jquery.autocomplete.css" type="text/css">
	<link rel="stylesheet" href="/salat2/_public/jquery-ui.css">

	<script type="text/javascript" src="../htmleditor/ckeditor/ckeditor.js<?=($act != 'show') ? "?t=" . time() : "";?>"></script>
	<script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
	<script type="text/javascript" src="../_public/jquery-ui.js"></script>
	<script type="text/javascript" src="/_media/js/plugins/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="../_public/colorpicker.min.js"></script>
	<script type="text/javascript" src="../_public/media_select.js"></script>
	<script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
	
	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">
		function doDel(rowID,ordernum){
			if(confirm("<?=$_LANG['DEL_MESSAGE'];?>")){
				<? 	$lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id='.$module_lang_id:'';?>
				document.location.href = "?act=del<?=$lang_fw;?>&id="+rowID+"&order_num="+ordernum;
			}
		}
		$(function(){
			$('#show_order_num').focusout(function(){
				var order_num=$(this).val();
				$.post("/salat2/_ajax/ajax.index.php", '&file=module_management_service&act=check_order&order_number='+ order_num,function(result){
					if(result.msg!=''){
						alert('ערך סידור ' + order_num + ' כבר קיים, תנסה את מספר סידור הבא: ' + result.msg);
						$('#show_order_num').val(result.msg);
					}
				},"json");
			});

            <?php if($act=="new" && !$obj_id){ ?>
			$('input[name="page"], input[name="section"]').change(function(){
				var name = $('input[name="page"]').val();
				var section = $('input[name="section"]').val();
                name = name.replace(".php","");

				if(name != '' && section != '') {
                    $.post("/salat2/_ajax/ajax.index.php", '&file=module_management_service&act=check_exist&name='+ name + '&section=' + section,function(result){
                        if(result.err!=''){
                            alert(result.msg);
                        }
                    },"json");
                }
			});
			<? } ?>
		});



	</script>
	<style type="text/css">
		.normTxt.hover td{
			background:wheat;
		}
	</style>
</head>
<?php echo $_salat_style;?>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
<div class="maindiv">
<br/>
<?php  if($act=="show"){ ?>
	<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
		<tr class="dottTbl">
			<?php  $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td width="70">סדר</td>
			<?php  } ?>
			<td width="100">&nbsp;</td>
		</tr>
		<?php  for($count = $resultArr['result']->num_rows,$i=0;$row = $Db->get_stream($resultArr['result']);$i++){ ?>
		<tr class="normTxt" >
			<?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
			<?php  } ?>
			<td class="dottTblS">
				<?php  if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
				<input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>" onclick="javascript: location.href='?act=new&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp;
				<?php  } if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)){ ?>
				<input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>" onclick="javascript:doDel(<?php echo $row['id'];?>, '<?php echo (int)$row['order_num'].$fwParams;?>');" /> &nbsp;
				<?php  } ?>
			</td>
		</tr> 
		<?php  } if ($resultArr['paging']){ ?>
		<tr class="normTxt">
			<td class="dottTblS" colspan="<?php echo $columns_count+1+($_Proccess_Has_Ordering_Action?1:0);?>" align="center"><?php echo $resultArr['paging'];?></td>
		</tr> 
		<?php  } ?>
		</table><br/>
		<?php  if ($resultArr['result']->num_rows==0){ ?>
			אין נתונים
		<?php  }else if($_Proccess_Has_Csv_Export){?>
			<div class="csv" style="direction: rtl;">
				<a href="?act=show<?=$fwParams;?>&csv=1" >
					<img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="16" height="16">

					<?=$_LANG['EXPORT_TO_CSV'];?>
				</a>
			</div>
		<?} ?>
<?php  }elseif($act=="new"){ ?>
	<form  name="form" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="act" value="after" />
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
		<tr class="dottTbl">
			<td colspan="2">הוספה / עריכה</td>
		</tr>
		<?php  fields_get_form_fields($fieldsArr, $row, ($obj_id==0?'add':'new'), false);
		if($_Proccess_Has_MetaTags){ ?>
		<tr>
			<td class="dottTblS" colspan="2">
			<?php 
			$_META_FORM="form";
			$_META_TITLE="title";
			$_META_DESC="summary";
			$lang = "en";
			include_once($_project_server_path.$_salat_path."_inc/metaform.inc.php");
			?>
			</td>
		</tr><?php  } ?>
		<tr>
			<td class="dottTblS" colspan="3">
				<input type="submit" name="send" value="<?php echo $submit;?>" class="buttons" onclick="document.getElementById('loader').style.display='';this.style.display='none';" />
				<input type="submit" name="stay" value="<?php echo $submitStay;?>" class="buttons" onclick="document.getElementById('loader').style.display='';this.style.display='none';" />
				<div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> מעבד נתונים, נא להמתין . . .</div>
			</td>
		</tr>
	</table>
	
	</form>
<?php  } ?>
</div>
</body>
</html>