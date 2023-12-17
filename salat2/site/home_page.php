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
$result = $Db->query($query) or die ("error checking user permissions");
if (($result->num_rows)==0){
	print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
	exit();
}

include_once($_project_server_path.$_includes_path."modules.array.inc.php");
include_once($_project_server_path.$_includes_path."citywall.array.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
//require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

$_Proccess_Make_Excel = false;
 
$_Proccess_Main_DB_Table = "";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = false;

$_Proccess_Has_GenricSearch = true;

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
$_Proccess_FW_Params = array('status','orderby','ordertype'
);

$_yesNo_arr = array(
	'0' => 'לא',
	'1' => 'כן',
);

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();


$_REQUEST['status'] = ($_REQUEST['status']) ? intval($_REQUEST['status']) :1 ;
if($_Proccess_Make_Excel){
   /* load fiels */
	include($_project_server_path.'salat2/resources/php-excel/1.8.1/PHPExcel.php');
include($_project_server_path."salat2/cs/excelReport.class.inc.php");//excelReport
}

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
	include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
	$fwParams[]=$param.'='.$_REQUEST[$param];
}
$fwParams = htmlspecialchars('&'.implode('&', $fwParams));

define("_PAGING_NumOfItems"			, 25);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}">{CONTENT}</a>');

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
		$submit	  	= $_LANG['BTN_ADD'];
	}
	$submitStay = $submit . " {$_LANG['AND_STAY']}" ;
}elseif($act=='move'){
	reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
	module_updateStaticFiles();
	header ("Location: ?act=show".$fwParams);
	exit();
}elseif($act=='after'){
	if($obj_id){
		$query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
		$result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
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
}elseif($act=="del"){
	$query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
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
	
	if($_Proccess_Has_GenricSearch){
	   	genric_searchable_items($_ProcessID,$whereArr);
	}
	
	if($_REQUEST['status']==2){$whereArr[] = "`status`={$_REQUEST['status']}";}
	else{
	   $whereArr[] = "`status`=1";
	}
	
	$where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
	$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if(array_key_exists($_REQUEST['orderby'], $fieldsArr)){
		$query .= " ORDER BY `{$_REQUEST['orderby']}`";
		if($_REQUEST['ordertype']){
			$query .= ' '.$Db->make_escape($_REQUEST['ordertype']);
		}
	}else{
	   if($_Proccess_Has_Ordering_Action){
	        $query .= " ORDER BY order_num";
	   }else{
	        $query .= " ORDER BY `last_name` DESC ";
	   }
	}
	$resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table;
/*  	$UpdateStatic = new usersUpdateStaticFiles();   
	$UpdateStatic->updateStatics(); */
}

/* home page items */


$query=" SELECT id,title,main_media FROM `tb_category` WHERE  `parent_id`=0 ORDER BY `home_page_order_num`";
$result=$Db->query($query);
include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<link rel="StyleSheet" href="../_public/main.css" type="text/css">
	<link rel="StyleSheet" href="../_public/faq.css" type="text/css">
	<script type="text/javascript" src="../htmleditor/ckeditor/ckeditor.js<?=($act != 'show') ? "?t=" . time() : "";?>"></script>
	<script type="text/javascript" src="../_public/datetimepicker.js"></script>
	<script type="text/javascript" src="../_public/jquery1.6.4.min.js"></script>
	<script type="text/javascript" src="/_media/js/plugins/jquery.dragsort-0.4.3.min.js"></script>
	<link rel="stylesheet" href="/_media/css/plugins/jquery.autocomplete.css" type="text/css">
	<script type="text/javascript" src="/_media/js/plugins/jquery.autocomplete.js"></script>
	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">
	
	$(function() {
	   $("#homepageItems").dragsort({ dragSelector: "div", dragBetween: true, dragEnd: saveOrder, placeHolderTemplate: $(this).html() });
	//   $("ul").dragsort({ dragSelector: "li", dragEnd: function() { }, dragBetween: true, placeHolderTemplate: "" });<br/>

	
	
	
	
   	$("#updateStatic").live('click',function(event){
   	   			$.post("/salat2/_ajax/ajax.index.php",'&file=home_page_category_order&act=static',function(result){
   	   				alert("עודכן בהצלחה!");
   	   			},"json");
   	   
   	});
	});	
	
		
		function saveOrder() {
			var data = $("#homepageItems li").map(function() { return $(this).children().html(); }).get();
			$("input[name=homepageItems_values]").val(data.join("|"));
		};
	
		
		
		
		
		
		
		
		
		
	
	</script>
	<style type="text/css">
		.normTxt.hover td{	background:wheat;	}
      #wrapper{width:960px; margin:0 auto; text-align:center;}
      	#gallery{width:1024px; overflow:auto;text-align:center;}
	  	.placeHolder div { background-color:white !important; border:dashed 1px gray !important; }
		h1 { font-size:16pt; }
		h2 { font-size:13pt; }
		ul {  list-style-type: none; margin:0px; padding:0px; }
		li { float:left; padding:5px;  display: list-item;}
		li div { background-color:#FFF; text-align:center; padding-top:10px; }
		.placeHolder div { background-color:white !important; }
		
	</style>
</head>
<?php echo $_salat_style;?>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>

<?if($_Proccess_Make_Excel){?>
   <div class="excel" onclick="get_excel_file('users');">
   <img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="32" height="32" />
    <br />
      <div class="excel-text"><strong>דו"ח כללי</strong></div>
    </div>
<? } ?> 
<div class="maindiv">


<br/>

<?php  if($act=="show"){ ?>
<div id="wrapper">

	<!--	<input name="homepageItems_values" type="hidden" value=""/>
	
	   <div id="homepageItems">
	      <ul>
	      <?// while($row = mysql_fetch_assoc($result)) { ?>
	      	<li><div><img src="/_media/media/<?=$row['main_media'];?>.png" alt="<?=$row['title'];?>" title="<?=$row['title'];?>" /><br /><span><?=$row['title'];?></span></div></li>
	      <?// }?>
	      </ul>
	   </div>-->
	
	    <div>
	
		    <br/>
	        
	        <ul id="gallery">
				<?php
				      while($row = mysql_fetch_assoc($result)) { 
	         			echo "<li itemID='" . $row['id']  . "'>";
	   					    echo "<div><img src=\"/_media/media/{$row['main_media']}.png\" alt=\"{$row['title']}\" title=\"{$row['title']}\" /><br /><span>{$row['title']}</span></div>";
	   					echo "</li>";
				      }
				?>
			</ul>
			
			<script type="text/javascript">
			    $("#gallery").dragsort({ dragSelector: "div", dragEnd: saveOrder, placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
	
			    function saveOrder() {
					var data = $("#gallery li").map(function() { return $(this).attr("itemID"); }).get();
			        $.post("/salat2/_ajax/ajax.index.php", { "ids[]": data,"file":'home_page_category_order' ,"act":"update"});
			    };
		    </script>
	        
	        <div style="clear:both;"></div>
	    </div>

</div>
<?php  } ?>


<div id="information" class="bold red">
גרור את הקטגוריות למקומם הרצוי

</div>
<input type="button" class="buttons bold pointer" id="updateStatic" value="סיימתי!" />
</div>
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/black_layer.inc.php'); ?>

</body>
</html>