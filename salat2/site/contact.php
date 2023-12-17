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

include($_project_server_path.$_includes_path."modules.array.inc.php");
include($_project_server_path.$_includes_path."citywall.array.inc.php");
include($_SERVER['DOCUMENT_ROOT'].'/_inc/class/moduleComment.class.inc.php');

require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

include($_project_server_path.'/_static/categoryFlat.inc.php'); //$categoryFlatArr
array_unshift($categoryFlatArr,'-- Choose --');

$_Proccess_Make_Excel = true;
 
$_Proccess_Main_DB_Table = "tb_contacts";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = false;

$_Proccess_Has_GenricSearch = true;

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
$_Proccess_FW_Params = array('full_name','user_id','email','is_done','orderby','ordertype'
);

$_yesNo_arr = array(
	'0' => 'לא',
	'1' => 'כן',
);

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

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
		$result 	= $Db->query($query);
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
}elseif($act=='after' || $act=='submit'){
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
//		$newOrder = mysqli_result($result,0,0);
		$query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
		$result = $Db->query($query);
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

	if($_Proccess_Has_GenricSearch && ($act!='submit')){
	   	genric_searchable_items($_ProcessID,$whereArr);
	}

	if($_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}
/*	if($_REQUEST['active']==0){$whereArr[] = "active=0";}
    else{$whereArr[] = "active=1";}*/
    /*if(!isset($_REQUEST['active'])){
        $whereArr[] = "active=1";
    }*/
	$where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
	$query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
	if(array_key_exists($_REQUEST['orderby'], $fieldsArr)){
		$query .= " ORDER BY `{$_REQUEST['orderby']}`";
		if($_REQUEST['ordertype']){
			$query .= ' '.$Db->make_escape($_REQUEST['ordertype']);
		}
	}elseif($_Proccess_Has_Ordering_Action){$query .= " ORDER BY order_num";}
	else{
		$query.=" ORDER BY `id` DESC";
	}
	if(isset($_REQUEST['csv']) && ($_REQUEST['csv'])){
		query_to_csv($query,$_Proccess_Main_DB_Table.'_log_'.date('d_m_Y').'.csv',true);
		exit();
	}
	$resultArr = getSqlPagingArr($query);
}


function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table;

}


function draw_contact_button($id){
   return '<input type="button" class="buttons contact_btn" value="צור קשר" id="user_contact" />';
}

function getUser($val){
   global $cityWallUsersArr;
   $class = ($val==1)? 'green' : 'blue';
   return  '<span class="'.$class.' bold">'.$cityWallUsersArr[$val].'</span>';
}

function getYesNo(){
   global $_yesNo_arr,$row,$_ProcessID;
   $val=ModuleComment::haveComment($_ProcessID,$row['id']);
   $class = ($val==1)? 'green' : 'red';
   return  '<span class="'.$class.' bold">'.$_yesNo_arr[$val].'</span>';
}

include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_info.inc.php');
header('Content-Type: text/html; charset=UTF-8');         
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
	<script type="text/javascript" src="../_public/main.js"></script>
	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">
	function doDel(rowID,ordernum){
		if(confirm("<?=$_LANG['DEL_MESSAGE'];?>")){
			document.location.href = "?act=del&id="+rowID+"&order_num="+ordernum;
		}
	}

	function get_excel_file(report){
        $("#black-layer").show();
			$.post("/salat2/_ajax/ajax.index.php",'&file=excel_web_service&report_name=' + report,function(result){
				if(result.err!="") {
					alert(result.err);

				} else {
				   if(result.html){
				      $("#black-layer").append(result.html);
				   }
				}
					   $("#black-layer").hide();
			},"json");
	}
	
		function get_excel_file_item(report,id){
        $("#black-layer").show();
			$.post("/salat2/_ajax/ajax.index.php",'&file=excel_web_service&tp=' + id,function(result){
				if(result.err!="") {
					alert(result.err);

				} else {
				   if(result.html){
				      $("#black-layer").append(result.html);
				   }
				}
					   $("#black-layer").hide();
			},"json");
	}
	
	
	

	$(function() {

        menu_header();

	   $("#user_contact").live('click',function(event){
	      			$.get("/salat2/_ajax/ajax.index.php",'&file=user_contact&act=show&email=' + $("#email").html() ,function(result){
	      				if(result.err!="") {
	      					alert(result.err);
	      				} else {
	      					$("#black-layer").show();
	      					$("#black-layer").html(result.html);
	      				}
	      				
	      			},"json");
	      			
	   });
	   
	   $(".del_note").live('click',function(event){
	     	  $.get("/salat2/_ajax/ajax.index.php",'&file=user_contact&act=delComment&comment_id='+ $(this).attr("rel") +'&mdl_id=<?=$_ProcessID?>'+'&obj_id=<?=$obj_id;?>' ,function(result){
	      		   $("#notes").html(result.html);	
	      	 },"json");
	      	   //  $(this).parent().parent().remove();
	   });

	   $("#sendMailSubmit").live('click',function(event){
        if($("#note_text").val()!=""){
           $.post("/salat2/_ajax/ajax.index.php",'content="' + $("#note_text").val() + '"&file=user_contact&act=addComment&obj_id=<?=$obj_id;?>&mdl_id=<?=$_ProcessID;?>&user_id=<?=$_SESSION['salatUserID'];?>',function(result){
                 $("#notes").html(result.html);
           },"json");
        }else{
            alert('אנא הזן תוכן');
            $("#note_text").css('border',"1px solid red");
        }
      });
	   
	  $("#close_btn").live('click',function(event){
	     // CKEDITOR.instances['contact_text'].destroy();
	     CKEDITOR.instances['contact_text'].destroy();
	     $("#black-layer").html('');
	     $("#black-layer").hide();
	  });

	});
	
	function sendcontact(){

        $.post("/salat2/_ajax/ajax.index.php",'content=' + CKEDITOR.instances['contact_text'].getData()+ '&file=user_contact&act=send&email='+ $("#email").html()+ '&obj_id=<?=$obj_id;?>&user_id=<?=$_SESSION['salatUserID'];?>',function(result){
            if(result.err!="") {
                alert(result.err);
            }
            else{
                $("#close_btn").trigger('click');
                $("#notes").html(result.html);
            }
        },"json");
	   			
	}
	</script>
	<style type="text/css">
	.normTxt.hover td{	background:wheat;	}
	.excel{display:inline;cursor:pointer;width:auto;height:auto;float:left;}
	.green{color:green;}
	.blue{color:blue;}
	.bold{font-weight:bold;}
	.contact_btn{	     width:200px;	     padding:3px;	     border:2px solid black !important;		}
	.msg_txt{display:inline;max-height:200px;overflow:auto;	}
	#note_form {background-color: #EEE;border-bottom: 1px solid #DDD;padding: 2px;}
   #frm_wrapper{width:700px; margin: 0 auto; position:relative; background-color:white; top:40px;padding:25px;border:1px solid black;}
   #close_btn{cursor:pointer;position:absolute; right: 15px;top:10px; background-color:#D3D3D3;font-weight:bold;color:white;font-size:16px; width:20px;height:16px;padding:5px;border:1px solid black; }
   #close_btn span{font-size:22px; margin-right:4px;line-height:30px; position:absolute; top: -4px;}
   #ck_wrapper{margin:0 auto;margin-right:50px;}
   #title_contact{font-size:16px; font-weight:bold;color:#0573C7;width:700px;text-decoration:underline;text-align:center;}
   #title_contact span{font-size:16px; font-weight:bold;color:#0573C7;text-decoration:underline;}
   #buttons_holder{width:700px; text-align:center;}
   #black_box{width:320px;margin-top:0/*-384*/;position: relative;}
   .notes_title{	background-color:#0573C7; line-height:20px; color:white; font-weight:bold;   }

   .div_notes {display:block;}
	</style>
</head>
<?php echo $_salat_style;?>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<!--<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />-->
<?if($_Proccess_Make_Excel){?>
   <div class="excel" onclick="get_excel_file('contact');">
   <img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="32" height="32" />
    <br />
      <div class="excel-text"><strong>דו"ח כללי</strong></div>
    </div>
<? } ?> 
<div class="maindiv">

<? if($_Proccess_Has_GenricSearch && ($act!='submit')){ ?>
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
<br/>

<?php  if($act=="show"){ ?>
	<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;float:right;" class="table-list">
		<tr class="dottTbl">
			<?php  $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td width="70">סדר</td>
			<?php  } ?>
			<td width="100">&nbsp;</td>
		</tr>
		<?php  for($count = $resultArr['result']->num_rows,$i=0;$row = $resultArr['result']->get_stream();$i++){ ?>
		<tr class="normTxt" >
			<?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
			<?php  } ?>
			<td class="dottTblS">
				<?php  if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
<!--				<input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>" onclick="javascript: location.href='?act=new&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp;-->
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
		</table>
		

		
		<?php  if ($$result->num_rows==0){ ?>
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
	
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="">
   	<tr>
      	<td width="80%" style="position: relative; vertical-align: top;top: 0;">
      	

            	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit" style="float:right;" id="window_table">
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
      	</td>
      	<td width="20%" style="position: relative; vertical-align: top;top: 0;">
      	
      	<div id="black_box">
      	
      	<h2 style="background-color:#0573C7;color:white;font-weight:bold;"><img title="הסתר" alt="הסתר" class="del-ico" src="../images/delete.png" rel="42" style="cursor:pointer;" onclick="javascript:getElementById('black_box').style.display='none'; getElementById('window_table').style.width='100%'; return false;">
         			        <span style="text-align:center;">היסטוריה התכתבות</span></h2> 
      	
      	         		       <div id="note_form">
         		                  <form id="submitNoteForm" action="" method="post">
         		                     <input type="hidden" name="obj_id" value="<?=$obj_id?>" />
         		                     <input type="hidden" id="note_lts" name="lts" value="0" />
         		                     <input type="hidden" name="userid" value="<?= $_SESSION['salatUserID'] ;?>" />
         		                     <input type="hidden" name="fullname" value="<?=$_SESSION['salatUserFName']  ;?>" />
         		                     <input type="hidden" name="file" value="policyNotes" />
         		                     <input type="hidden" name="act" value="submit" />
         									<strong>הוספת הערה:</strong> <br />
         									<textarea id="note_text" name="note" cols="30" rows="5"></textarea>
         									<br />
         									<div class="white-line">
         										<div class="user-timestamp">
         											<?=$_SESSION['salatUserFName'].' - '.date("d/m/Y [H:i]");?>
         										</div>
         										<input type="button" id="sendMailSubmit" value="שמור" class="buttons"  /> <br /> <br />
         										<div class="clear"></div>
         									</div>
         								</form>
            								<div id="notes">
                              		<?=ModuleComment::drawAllcomments($obj_id);?>
                                    </div>
         							</div>

      	</div>
   	</td> </tr>
	</table>
		<?php// include($_project_server_path.'salat2/_inc/' . 'ckbox.inc.php'); ?>
	</form>
<?php  } ?>
</div>
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/black_layer.inc.php'); ?>
</body>
</html>