<?php
include_once("../_inc/config.inc.php");

$this_dir = basename(dirname(__FILE__));

$_ProcessID = get_module_id(basename(__FILE__),$this_dir); // 05/10/11


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

include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011


$_Proccess_Main_DB_Table = "tb_push";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = false;

$_Proccess_Has_GenricSearch = false;
/**
 *  option to make the csv export from db
 */
$_Proccess_Has_Csv_Export = false;

/**
 *  add 'main_media' field in the proccess db table
 */

$_Proccess_Has_MediaLink = true;

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
$_Proccess_FW_Params = array('lang_id');

$_yesNo_arr = array(
	'0' => 'לא',
	'1' => 'כן',
);

$platformsArr = array(
	'0' => 'הכל',
	'1' => 'Android',
	'2' => 'iPhone'
);

$gendersArr = array(
	'0' => 'הכל',
	'1' => 'זכר',
	'2' => 'נקבה'
);



$users_listArr = get_sys_users();

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
	//include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
	$fwParams[]=$param.'='.$_REQUEST[$param];
}

$fwParams = htmlspecialchars('&'.implode('&', $fwParams));

define("_PAGING_NumOfItems"			, 25);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}'.$fwParams.'">{CONTENT}</a>');

$act = $_REQUEST['act'];
if ($act=='') $act="show";
$obj_id = (int)$_REQUEST['id'];

if($act=='new'){
	if($obj_id){
		$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";

		if($_Proccess_Has_MultiLangs) {
			$query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`='{$module_lang_id}'";
		}

		$result 	= $Db->query($query);
		if($result->num_rows==0){
			$query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`=".default_lang_id;
			$result 	= $Db->query($query);
		}
		$row    	= $Db->get_stream($result);
		$submit	   	= $_LANG['BTN_UPDATE'];
	}else{
		$submit	  	= "שמור";
	}
	$submitStay = $submit . ' and stay in this page';
}elseif($act=='move'){
	reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}elseif($act=='after') {
//die('<hr /><pre>' . print_r($_REQUEST, true) . '</pre><hr />');
	$_REQUEST['send_from_ts'] = strtotime($_REQUEST['send_from_ts']);
	unset($fieldsArr['csv_file']);

	if ($obj_id) {  // ### UPDATE QUERY ###
		$_REQUEST['inner_id'] = $obj_id;
		if ($_Proccess_Has_MultiLangs) {
			multi_lang_update_query($_Proccess_Main_DB_Table, $_REQUEST['inner_id'], $fieldsArr, $module_lang_id);
		} else {
			$query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
			$result = $Db->query($query);
		}
	} else {
		$query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
		$result = $Db->query($query);
		//$newOrder = mysqli_result($result, 0, 0);

		if ($_Proccess_Has_MultiLangs) {
			multi_lang_insert_query($_Proccess_Main_DB_Table, $fieldsArr, $module_lang_id);

		} else {
			$query = "INSERT INTO {$_Proccess_Main_DB_Table}(" . fields_implode(', ', $fieldsArr) . ") VALUES (" . fields_implode(',', $fieldsArr, $_REQUEST) . ")";
			$result = $Db->query($query);
			$_REQUEST['inner_id'] = $Db->get_insert_id();
		}

		if ($_Proccess_Has_Ordering_Action) {
			setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
		}
	}

	if ($_Proccess_Has_MetaTags) {
		include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
		meta_UpdateTags();
	}

	module_updateStaticFiles();

	if(!empty($_REQUEST['send'])) {
		$sendingStatus = sendCsvToPushQueue($_REQUEST['inner_id']);

		if($sendingStatus == "1") {
			$query = "UPDATE {$_Proccess_Main_DB_Table} SET sent = sent + 1, last_update = '" . time() . "' WHERE id = '{$obj_id}'";
			$result = $Db->query($query);
			header("Location: ?act=show" . $fwParams);
		}
		else {
			header("Location: ?act=new&errorMsg={$sendingStatus}&id=" . $_REQUEST['inner_id'] . $fwParams);
		}
		exit();
	}

	if (!empty($_REQUEST['stay'])) {
		header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
	} else {
		header("Location: ?act=show" . $fwParams);
	}

	exit();
}elseif($act=="del") {

	if ($_Proccess_Has_MultiLangs) {
		$query = "DELETE FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}' AND `lang_id`='{$module_lang_id}'";
		// delete language static file !!!
		@unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/push/' . get_item_dir($obj_id) . '/' . $languagesArr[$module_lang_id]['title'] . '/push-' . $obj_id . '.inc.php');
	} else {
		$query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
		// delete static file !!!
		@unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/push/' . get_item_dir($obj_id) . '/push-' . $obj_id . '.inc.php');
	}
	$result = $Db->query($query);


	if ($_Proccess_Has_MultiLangs) {
		// check if there are no other translations for item - if not, delete item
		$multiLangSql = "SELECT * FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}'";
		$multiLangRes = $Db->query($multiLangSql);
		if ($multiLangRes->num_rows == 0) {
			$multiLangSql = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
			$result = $Db->query($multiLangSql);
		}
	}

	if ($_Proccess_Has_Ordering_Action) {
		fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
	}
	if ($_Proccess_Has_MetaTags) {
		include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
	}
	module_updateStaticFiles();
	header("location:?act=show" . $fwParams);
	exit();
}else {
	$whereArr = array();
	if (($_Proccess_Has_GenricSearch) && ($_Proccess_Has_MultiLangs)) {
		genric_searchable_items($_ProcessID, $whereArr, 'Result');
	} else if ($_Proccess_Has_GenricSearch) {
		genric_searchable_items($_ProcessID, $whereArr);
		//	die('<hr /><pre>' . print_r($whereArr, true) . '</pre><hr />');
	}

	if ($_REQUEST['topcat_id'] > 0) {
		$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";
	}


	$where = (count($whereArr) > 0 ? " WHERE " . implode(' AND ', $whereArr) : "");
	$query = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if ($_Proccess_Has_MultiLangs) {


		$query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS  Main
							LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
								Main.`id`=Lang.`obj_id`
							)
						 WHERE  Lang.`lang_id`='{$module_lang_id}' {$where}";


		if ($_Proccess_Has_GenricSearch) {

			$query = str_replace($where, ' ', $query);
			$query = "SELECT * FROM ({$query}) AS Result {$where}";
		}
	}


	if (array_key_exists($_REQUEST['orderby'], $fieldsArr)) {
		$query .= " ORDER BY `{$_REQUEST['orderby']}`";
		if ($_REQUEST['ordertype']) {
			$query .= ' ' . $Db->make_escape($_REQUEST['ordertype']);
		}
	} elseif ($_Proccess_Has_Ordering_Action) {
		$query .= " ORDER BY order_num";
	}
	else {
		$query .= " ORDER BY `id` DESC";
	}
	if (isset($_REQUEST['csv']) && ($_REQUEST['csv'])) {
		query_to_csv($query, $_Proccess_Main_DB_Table . '_log_' . date('d_m_Y') . '.csv', true);
		exit();
	}

	$resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table,$_Proccess_Has_MultiLangs;
	/*
		if($_Proccess_Has_MultiLangs){
			$UpdateStatic = new pushLangsUpdateStaticFiles();
			$UpdateStatic->updateStatics();
		}else{
			$UpdateStatic = new pushUpdateStaticFiles();
			$UpdateStatic->updateStatics();
		}
	*/
}

function savePushRecord($push_id, $row, $message, $status){
	$Db = Database::getInstance();
	$send_ts = ($status == 1) ? time() : 0;
	$db_fields = array(
		"push_id" => $push_id,
		"message" => $message,
		"user_id" => $row['user_id'],
		"udid" => $row['udid'],
		"token" => $row['token'],
		"platform" => $row['platform'],
		"send_ts" => $send_ts,
		"last_update" => time()
	);

	foreach($db_fields AS $key => $value){
		$db_fields[$key] = $Db->make_escape($value);
	}

	$query = "INSERT INTO `tb_push__sent` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
	$res = $Db->query($query);
}

function sendCsvToPushQueue($code)
{
	$Db = Database::getInstance();
	$message = $Db->make_escape($_REQUEST['message']);
	$users = $Db->make_escape($_REQUEST['users']);
	$gender = intval($_REQUEST['gender']);
	$device_type = intval($_REQUEST['device_type']);
	$send_from_ts = $_REQUEST['send_from_ts'];
	$send_until_ts = ($send_from_ts > 0) ? ($send_from_ts + (60 * 60 * 6)) : (time() + (60 * 60 * 6));

	//JSON
	$json_input = stripslashes($_REQUEST['json']);
	//add msg_id to json
	$json = json_decode($json_input,true);
	$json['msg_id'] = $code;
	$json = json_encode($json);

	$where = "";
	if ($users != "") {
		$local_system = true;
		$where .= " AND user.`id` IN ({$users})";
	}
	if ($gender > 0) {
		$where .= " AND user.`gender` = {$gender}";
	}
	if ($device_type > 0) {
		$device_typeArr = array("", "iphone", "android");
		$where .= " AND device.`device_type` = '{$device_typeArr[$device_type]}'";
	}

	//with file
	if ($_FILES['csv_file']['tmp_name'] != '') {
		$local_system = false;
		$nameArr = explode('.', $_FILES['csv_file']['name']);
		$ext = strtolower(end($nameArr));

		if ($ext == "csv") {
			$path = $_SERVER['DOCUMENT_ROOT'] . "/salat2/_static/push";
			if(!file_exists($path)){
				mkdir($path . "/", 0777); //create push directory
				mkdir($path . "/csv/", 0777); //create csv directory
				mkdir($path . "/zip/", 0777); //create csv directory
			}

			$target_file = $_SERVER['DOCUMENT_ROOT'] . "/salat2/_static/push/csv/push-" . time() . ".csv";

			move_uploaded_file($_FILES['csv_file']['tmp_name'], $target_file);

			$handle = fopen($target_file, 'r');
			$line = 0;
			while(($data = fgetcsv($handle)) !== FALSE) {
				if($line == 0){
					$line++;
					continue;
				}

				$line++;
				$db_fields = array(
					"user_id" => $data[0],
					"message" => $data[1],
					"send_from_ts" => $data[2],
					"send_until_ts" => $data[3],
					"json" => $data[4]
				);

				foreach($db_fields AS $key => $value){
					$db_fields[$key] = $Db->make_escape($value);
				}

				$query = "INSERT INTO `tb_push__tmp` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
				$res = $Db->query($query);
			}

			$newQuery = "
				SELECT device.user_id, device.udid, device.device_type AS 'platform', device.token_device AS 'token', tmp.message AS 'message', tmp.send_from_ts, tmp.send_until_ts, tmp.json
					FROM `tb_push__tmp` AS tmp
						LEFT JOIN `tb_users` AS user
							ON tmp.user_id = user.id
						LEFT JOIN  `tb_push__device` AS device
							ON user.id = device.user_id
				WHERE device.`device_type` IS NOT NULL";
		}
		else {
			$error = "הקובץ שהועלה אינו תקין.";
			return $error;
		}
	} else {
		//get users information from the main DataBase
		$newQuery = "
			SELECT device.user_id, device.udid, device.device_type AS 'platform', device.token_device AS 'token', '{$message}' AS 'message', '{$send_from_ts}' AS 'send_from_ts', '{$send_until_ts}' AS 'send_until_ts', '{$json}' AS 'json', device.`source`
				FROM  `tb_users` AS user
					LEFT JOIN  `tb_push__device` AS device
						ON user.id = device.user_id
			WHERE device.`device_type` IS NOT NULL" . $where;
	}

	if ($local_system) {
		$result = $Db->query($newQuery);

		if ($result->num_rows > 0) {
			include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/module/pushManager.class.inc.php');
			$Push = new pushManager();
			while ($row = $Db->get_stream($result)) {
				$return = $Push->send_push($row['token'], $message, $row['platform'], json_decode($json, true), $row['source']);
				savePushRecord($_REQUEST['inner_id'], $row, $message, $return);
			}
		}
		else {
			$error = "לא נמצאו רשומות בסינון שהוגדר";
			$return = $error;
		}
	} else {
		//inManage-MPNS
		include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/pushClient.class.inc.php');

		$client_id = 1;
		$client_password = "rowMdqKntYF+kKE1122012GYYhO3220F7iwMNczXIc=";

		if($client_id && $client_password) {
			$PushClient = new PushClient($client_id, $client_password, $newQuery); //client_id, token, query
			$PushClient->sendPushToTesters($message);
			$return = $PushClient->sendPush($code);
		}
		else {
			$return = 'יש להזין פרטי שימוש ב-MPNS<br />Here: ' . __LINE__ . ' at ' . __FILE__ . '</pre><hr />';
		}
	}

	return $return;
}

include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<link rel="StyleSheet" href="../_public/faq.css" type="text/css">
	<link rel="StyleSheet" href="../_public/colorpicker.css" type="text/css">
	<link rel="stylesheet" href="/_media/css/plugins/jquery.autocomplete.css" type="text/css">
	<link rel="stylesheet" href="/salat2/_public/jquery-ui.css">


	<script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
	<script type="text/javascript" src="../_public/jquery-ui.js"></script>
	<script type="text/javascript" src="/_media/js/plugins/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="../_public/colorpicker.min.js"></script>
	<script type="text/javascript" src="../_public/media_select.js"></script>
	<script type="text/javascript" src="../htmleditor/ckeditor/ckeditor.js<?=($act != 'show') ? "?t=" . time() : "";?>"></script>
	<script type="text/javascript" src="../_public/datetimepicker.js"></script>
	<script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js"></script>

	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">
		function doDel(rowID,ordernum){
			if(confirm("<?=$_LANG['DEL_MESSAGE'];?>")){
				<? 	$lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id='.$module_lang_id:'';?>
				document.location.href = "?act=del<?=$lang_fw;?>&id="+rowID+"&order_num="+ordernum;
			}
		}

		function sendConfirm(){
			return confirm("האם לשלוח את ההודעה?");
		}

		$(function() {
			// color picker default and initialization
			$.each($('.colorSelection'), function(key,val){
				$(val).find('div').css('backgroundColor', '#'+$(val).find('input:hidden').val());
				$(val).ColorPicker({
					color: '#'+$(val).find('input:hidden').val(),
					onShow: function (colpkr) {
						$(colpkr).fadeIn(500);
						return false;
					},
					onHide: function (colpkr) {
						$(colpkr).fadeOut(500);
						return false;
					},
					onChange: function (hsb, hex, rgb) {
						$(val).find('div').css('backgroundColor', '#' + hex);
						$(val).find('input:hidden').val(hex);
					}
				});
			});

			/*  auto media load */
			var mediaSelects = $('.media-select');

			var mediaItemId,MediaExt,mediaCategoryId;
			mediaSelects.bind('change', function() {
				$('input#tmp-medium').val($(this).children(':selected').val());
				showImage();
			});

			$("#add-image").live('click',function(event){
				$("#media_category_sel").show();
			});


			$("#media_category_sel").live('change',function(event){
				var scope=$(this);

				$.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' ,'category': scope.val() ,'action' : 'getSelcetItems' }, function(response) {
					$("#media_items_sel").html(response);
					$("#media_items_sel").show();
				});

				$("#gallery_id").val($(this).val());
				$("#image_con").attr("src","");
				$("#image_con").attr("rel","");
				$("#image_con").removeClass("selected_item");
				$("#image_con").hide();
			});

			$("#media_items_sel").live('change',function(event){
				var splitStr = $(this).val().split('_');
				mediaCategoryId=splitStr[0];
				mediaItemId=splitStr[1];
				MediaExt=splitStr[2];

				if($(this).val()){
					$("#image_con").attr("src","/_media/media/" + mediaCategoryId + '/' + mediaItemId + '.' + MediaExt);
					$("#image_con").attr("rel",mediaItemId);
					$("#image_con").show();
				}else{
					$("#image_con").hide();
					$("#image_con").attr("src","");
					$("#image_con").attr("rel","");
				}
			});

			$("#image_con").live('click',function(event){

				$(this).toggleClass('selected_item');

				if( $(this).hasClass('selected_item')){

					$("#main_media").val($("#image_con").attr("rel"));
				}

			});
		});

		function countChar(val){
			var len = val.value.length;
			if (len >= 100) {
				val.value = val.value.substring(0, 100);
			} else {
				$('span.countChar').text(100 - len);
			}
		};

		function get_colors(){
			var Scope;
			$('.feature_color').live('keydown',function(){
				var flag=1;
				Scope=$(this);
				if(flag==1){
					flag++;
					$('.feature_color')
						.autocomplete('/salat2/_ajax/ajax.index.php', {
							'extraParams': {
								'file': 'auto_complete/item',
								'action' : 'get_colors',
								'lang_id' : '<?=$module_lang_id;?>'
							}
						}).result(function(event, data) {
							var color_id=data[1];
							$($(Scope).parent().next().find('.color_id_hidden')).val(color_id);
							$.post('/salat2/_ajax/ajax.index.php', {
									'color_id': color_id,
									'file': 'auto_complete/item',
									'action':'get_color_hex'
								},
								function(response,data1) {
									$($(Scope).parent().next().find('.color_hex_id')).css('background-color',response);
								}
							);
						});
				}
			});

		}

	</script>
	<style type="text/css">
		/* auto media load */
		.selected             { display: block; }
		.show   {display:block;}
		.off   {display:none;}
		#image_con   { height:40px;cursor:pointer;}
		.selected_item{border:1px solid red;}
		/* end of  auto media load */

		.colorSelection {
			position: relative;
			width: 36px;
			height: 36px;
			background: url(../_public/colorpicker_images/select.png);
		}

		.colorSelection > div {
			position: absolute;
			top: 3px;
			left: 3px;
			width: 30px;
			height: 30px;
			background: url(../_public/colorpicker_images/select.png) center;
		}
	</style>
</head>
<?php  echo $_salat_style;?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
<div class="maindiv">
	<br/>
	<? if($_Proccess_Has_GenricSearch){ ?>
		<br />
		<div id="search_frm">
			<form action="" method="get" style="display:inline; font-size:12px; margin:0 auto;">
				<input type="hidden" name="act" value="show" />
				<input type="hidden" name="dosearch" value="search" />

				<table cellpadding="3" cellspacing="0" align="center"><tr>

						<?
						foreach ($fieldsArr AS $key=>$fieldArr) {
							if($fieldArr['input']['searchable']==true){
								print "<td>".$fieldArr['title'] .":";
								print draw_genric_search($fieldArr,$key)."</td>";
							}
						}
						?>

						<td><input type="submit" value="חיפוש" class="buttons" /></td>
					</tr></table>
			</form>
		</div>
	<? } ?>
	<? if($_Proccess_Has_MultiLangs ){ ?>
		<?=draw_module_tabs();?>
	<?} ?>
	<?php  if($act=="show"){ ?>
		<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
			<tr class="dottTbl">
				<?php  $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
				<?php  if($_Proccess_Has_Ordering_Action){ ?>
					<td width="70">סדר</td>
				<?php  } ?>
				<td width="100">&nbsp;</td>
			</tr>
			<?php  for($count = $resultArr['result']->num_rows,$i=0;$row = $Db->get_stream($resultArr['result']);$i++){
				$c=($i%2==0)?'even':'odd'; ?>
				<tr class="normTxt <?=$c?>">
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
		<?php  if ($result->num_rows==0){ ?>
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
				<? if($_REQUEST['errorMsg'] <> ''){?>
					<tr>
						<td colspan="2" style="color:red; font-weight:bold; text-align:right;"><script>alert('<?=$_REQUEST['errorMsg']?>');</script></td>
					</tr>
				<?}?>
				<?php  fields_get_form_fields($fieldsArr, $row, ($obj_id==0?'add':'new'), false);
				if($_Proccess_Has_MetaTags){ ?>
					<tr>
					<td class="dottTblS" colspan="2">
						<?php
						$_META_FORM="form";
						$_META_TITLE="title";
						$_META_DESC="summary";
						$lang = "en";
						include_once($_project_server_path . $_salat_path . "_inc/metaform.inc.php");
						?>
					</td>
					</tr><?php  } ?>
				<tr>
					<td class="dottTblS" colspan="3">
						<input type="submit" name="send" value="שלח" class="buttons" onclick="if(!sendConfirm()){return false;} document.getElementById('loader').style.display='';this.style.display='none';" />
						<input type="submit" name="save" value="שמור" class="buttons" onclick="document.getElementById('loader').style.display='';this.style.display='none';" />
						<div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> מעבד נתונים, נא להמתין . . .</div>
					</td>
				</tr>
			</table>

		</form>
	<?php  } ?>
</div>
</body>
</html>