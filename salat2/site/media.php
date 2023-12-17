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

include_once($_project_server_path.$_includes_path."modules.array.inc.php");
require_once($_project_server_path.$_salat_path."modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

$_Proccess_Make_Excel = false;
 
$_Proccess_Main_DB_Table = "tb_media";

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
$_Proccess_FW_Params = array('category_id','orderby','ordertype'
);

$_yesNo_arr = array(
	'0' => 'No',
	'1' => 'Yes',
);
$mediaActionArr=array(
	1=>"Don't change",
	2=>'Make smaller',
	3=>'Make smaller but keep proportions',
);

$mediaCategory=array();
$query="SELECT * FROM `tb_media_category`";
$result=$Db->query($query);

while($row = $Db->get_stream($result)) {
	$mediaCategory[$row['id']]=$row['title'];
}

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
	if (isset($_REQUEST['show_all'])) {
		if ($param == 'lang_id') {
			$fwParams[] = $param . '=' . $_REQUEST[$param];
		}
	}else{
		$fwParams[]=$param.'='.$_REQUEST[$param];
	}

}

if(isset($_REQUEST['from_media_category']) && $_REQUEST['from_media_category']){
	$fwParams[] = 'from_media_category='.$_REQUEST['from_media_category'];
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
}elseif($act=='after'){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
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
		//$newOrder = mysqli_result($result,0,0);
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

		/** Images **/
		
	$imageFields=array('img');  /* all images fields needs to be in this array */
	foreach($imageFields as $field) {
		if($_FILES[$field . '_ext']['tmp_name']) {
			$ext = strtolower(end(explode('.', $_FILES[$field . '_ext']['name'])));
			if(in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
				$directory=$dir . $_REQUEST['category_id'];
				if(!file_exists($directory)){
					mkdir($directory.'/', 0777, true);
				}
				chmod($directory.'/',0777); //change permission of the directory
				//----------------------- delete old image before update the new one ------------------------
				$query = "SELECT `img_ext` FROM `{$_Proccess_Main_DB_Table}` WHERE id = '{$_REQUEST['inner_id']}'";
				$res = $Db->query($query);
				$line = $Db->get_stream($res);
				@unlink($dir . $_REQUEST['category_id'].'/'.$_REQUEST['inner_id'] .  '.' . $line['img_ext']);
				//--------------------------------------------------------------------------------------------
				move_uploaded_file($_FILES[$field . '_ext']['tmp_name'],
				$dir . $_REQUEST['category_id'].'/'.$_REQUEST['inner_id'] .  '.' . $ext);
				$query = "
				UPDATE
					{$_Proccess_Main_DB_Table}
					SET
						{$field}_ext = '{$ext}'
				WHERE
					id = '{$_REQUEST['inner_id']}'
				";
				$Db->query($query);
				$targetFile=$dir . $_REQUEST['category_id'].'/'.$_REQUEST['inner_id'] .  '.' . $ext;
				
				if(isset($_REQUEST['width']) && $_REQUEST['width'] && isset($_REQUEST['height']) && $_REQUEST['height']){
					list($image_new_width,$image_new_height)=array($_REQUEST['width'],$_REQUEST['height']);
					
					list($w, $h) = getimagesize($targetFile);	
				
				}
				if(isset($_REQUEST['_media_action_']) && ($_REQUEST['_media_action_'] > 1)){
					$image = new Imagick($targetFile);
					if(isset($_REQUEST['_media_action_']) && ($_REQUEST['_media_action_']==3)){
						if($h > $w){
							$image->ThumbnailImage($image_new_height,$image_new_width,true);
						} else{
							$image->cropThumbnailImage($image_new_width,$image_new_height);
						}
					}
					else if(isset($_REQUEST['_media_action_']) && ($_REQUEST['_media_action_']==2)){
						$image->resizeImage($image_new_width,$image_new_height,Imagick::FILTER_LANCZOS,1);
					}
					$image->writeImage($targetFile); 								
				}

                if(class_exists(Imagick)){
                    update_all_images_resolutions($targetFile);
                }
			}
		}
		if($_REQUEST[$field . '_ext_delete']) {
			@unlink($dir . $_REQUEST['category_id'].'/'.$_REQUEST['inner_id']  . '.' . $_REQUEST[$field . '_ext_delete']);
			$query = "
			UPDATE
				{$_Proccess_Main_DB_Table}
				SET
					{$field}_ext = ''
			WHERE
				id = '{$_REQUEST['inner_id']}'
			";
			$Db->query($query);
		}
	}
	/** Images **/
	

	if ($_REQUEST['category_id'] != $_REQUEST['orig_category_id'] && $_REQUEST['orig_category_id'] != ''){
		$origPath = $_SERVER['DOCUMENT_ROOT'] . '/_media/media/'.$_REQUEST['orig_category_id'] . '/' . $_REQUEST['inner_id'] .'.*';
		$newPath = $_SERVER['DOCUMENT_ROOT'] . '/_media/media/'.$_REQUEST['category_id'] . '/';
		exec('mv '.$origPath.' '.$newPath);
	}
	
	
	module_updateStaticFiles();
	$fwParams=str_replace('amp;','&',$fwParams);
	if(!empty($_REQUEST['stay'])) {
		header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
	} else {
		
		header ("Location: ?act=show".$fwParams);
	}
	exit();
}elseif($act=="del"){
	$query="SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$row=$Db->get_stream($Db->query($query));
	@unlink($_SERVER['DOCUMENT_ROOT'].'/_media/media/'.$row['category_id'].'/'.$row['id'].'.jpg');
	@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/media/'.get_item_dir($row['id']).'/media-'.$row['id'].'.inc.php');
	//delete fizik files of resolution
	$queryRes="SELECT Main.*,Resolution.title,Media.img_ext FROM `{$_Proccess_Main_DB_Table}_resolutions` as Main
					LEFT JOIN `tb_resolutions` as Resolution
						ON (Resolution.id=Main.resolution_id)
							LEFT JOIN {$_Proccess_Main_DB_Table} as Media
								ON (Main.resolution_media_id=Media.id)
									WHERE media_id={$obj_id}";
	$resResolution=$Db->query($queryRes);
	if($resResolution->num_rows>0){
		while($lineResolution=$Db->get_stream($resResolution)){
			@unlink($_SERVER['DOCUMENT_ROOT'].'/_media/media/'.$row['category_id'].'/'.$row['id'].'_'.$lineResolution['title'].'.'.$lineResolution['img_ext']);
		}
	}
	$query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
	$result = $Db->query($query);
	//delete from tb_media_resolutions
	$query="DELETE FROM {$_Proccess_Main_DB_Table}_resolutions WHERE media_id='{$obj_id}' OR resolution_media_id='{$obj_id}'";
	$result = $Db->query($query);

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

	//----- clear all search values -------
	if(isset($_REQUEST['show_all'])){
		$requestArr = $_Proccess_FW_Params;
		foreach($requestArr AS $key => $value){
			unset($_REQUEST[$value]);
		}
	}
	//-------------------------------------

	$whereArr = array();
	
	if($_Proccess_Has_GenricSearch){
	   	genric_searchable_items($_ProcessID,$whereArr);
	}
	
	if($_REQUEST['category_id']>0){$whereArr[] = "category_id={$_REQUEST['category_id']}";}
	
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
	$UpdateStatic = new mediaUpdateStaticFiles();
	$UpdateStatic->updateStatics();

}
function get_resolutions(){
    global $row;
	$Db = Database::getInstance();

    clearstatcache();
    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    //draw all resolsutions for the selected main picture
    $main_id=$row['id'];
    if(isset($main_id)&&$main_id){
        $box_html='';
        $tipArr=array(); //put the array inside of sizes-names of resolutions
        $queryResolutionM="SELECT Main.*,MediaRes.media_id,MediaRes.resolution_media_id
                            FROM `tb_resolutions` as Main
                                LEFT JOIN `tb_media_resolutions` as MediaRes
                                    ON(MediaRes.resolution_id=Main.id)
										WHERE MediaRes.media_id={$main_id}
											AND Main.separate=1 AND Main.active=1";
        $res_resolutionM=$Db->query($queryResolutionM);
        $rowArr=array();
        $row1=array();  //check without row (not overwrite global row)
        $row_save=array();
        if($res_resolutionM->num_rows){
            while($row_med = $Db->get_stream($res_resolutionM)){
                $rowArr[$row_med['id']]=$row_med['id'];
                $row_save[$row_med['id']]=$row_med;
            }
        }
        //LECT * FROM `tb_media` as Media LEFT JOIN `tb_media_category` as Galle
        $mediaGallQuery="SELECT * FROM `tb_media` as Media
                                LEFT JOIN `tb_media_category` as Gallery
                                   	 ON(Gallery.id=Media.category_id)
                                   	 	WHERE Media.id={$main_id} AND Gallery.mobile=1";
        $mediaGalRes=$Db->query($mediaGallQuery);
        if($mediaGalRes->num_rows>0){
            $queryResolution="SELECT * FROM `tb_resolutions` WHERE active=1 AND separate=1";
            $res_resolution=$Db->query($queryResolution);
            global $row;
            if($res_resolution->num_rows){
                while($row_res = $Db->get_stream($res_resolution)) {
                    //if resolution_id reg = res_id in media -> get picture, otherwise just buttons
                    $row1=$row_save[$row_res['id']]; //check without row (not overwrite global row)
                    $mediaid=(isset($row['id'])&&$row['id'])?'':$row1['media_id'];
                    $res_html=(in_array($row_res['id'],$rowArr))?draw_table_media('id',$row_res,$tipArr,false,$mediaid):draw_table_media('id',$row_res,$tipArr);
                    $box_html.=$res_html;
                }
                $answer['res_html']=$box_html;
            }
        }
        return $answer['res_html'];
    }else{
        return '';
    }

}

function update_all_images_resolutions($targetFile){
	global $dir;
	$Db = Database::getInstance();
	$image = new Imagick($targetFile);
	$query = "SELECT * FROM `tb_media_resolutions` WHERE `resolution_media_id` = '{$_REQUEST['inner_id']}'";
    $res = $Db->query($query);
	if($res->num_rows){
		while($line = $Db->get_stream($res)){
			$query = "SELECT * FROM `tb_media` WHERE `id` = '{$line['media_id']}'"; //get main image
			$res1 = $Db->query($query);
			$line_media = $Db->get_stream($res1);
			$gallery_id = $line_media['category_id'];
			$main_ext = $line_media['img_ext'];
			$query = "SELECT `title` FROM `tb_resolutions` WHERE `id` = '{$line['resolution_id']}'";
			$res2 = $Db->query($query);
			$line_reso = $Db->get_stream($res2);
			$dir_reso = $dir . $gallery_id.'/'.$line['media_id'] . '_' .$line_reso['title'].  '.' . $main_ext; //image resolution path
			@unlink($dir_reso); // delete old image resolution file
			$image->setimageformat($main_ext);
			$image->writeimage($dir_reso); // add new image resolution file
		}
	}
}

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
	<script type="text/javascript" src="../_public/main.js"></script>
	<link rel="stylesheet" href="/_media/css/plugins/jquery.autocomplete.css" type="text/css">
	<script type="text/javascript" src="/_media/js/plugins/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js"></script>

	<script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
	<script type="text/javascript">

	function doAjaxDel(rowID,ordernum){
		if(confirm("<?=$_LANG['DEL_MESSAGE'];?>")){
			$.post('/salat2/_ajax/ajax.index.php',
				{'file':'media','action':'del','id':rowID},function(response){
					var rowItem = $("tr[data-rowid='"+rowID+"']");
					if (response.success){
						rowItem.remove();
					}
				},'json');
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
            var trrig1= 0,numUpload=0;

            $('.bar').closest('td').prev().attr('class', '').attr({'colspan':'2','align':'center'}).parent().attr('class', 'dottTbl').children().last().remove();
			      
			   $('#search_ajax')
					.autocomplete('/salat2/_ajax/ajax.index.php', {
						'extraParams': {
						'file': 'auto_complete/media_category',
						'action' : 'getItems'

						}
					}).result(function(event, data) {
						$.post('/salat2/_ajax/ajax.index.php', { 
							'title': data[0],
							'file': 'auto_complete/media_category',
						   'action':'getId' }, 
							function(response) {
		
								location.href = '?act=show&category_id=' + response;
							}
						);
					});

            //if choosen category, make save for this image
           /* $('.save_category_id').live('change',function(){
                $(document.getElementsByName('stay')[0]).trigger('click');
            });*/

            /*Media Start*/
            var mediaSelects = $('.media-select');

            var mediaItemId,MediaExt,mediaCategoryId,MediaRes;
            mediaSelects.bind('change', function() {
                $('input#tmp-medium').val($(this).children(':selected').val());
                showImage();
            });

            $(".add-image").live('click',function(event){
                var Scope=$(this);
                var flag=0;
                if(Scope.hasClass('pic_r')){
                    //get and show only gallery of THIS picture...
                    var img_close=$('.table-edit').find('.img_ext_media').next().find('a');
                    img_close=$(img_close).attr("href");
                    var hrefArr=img_close.split('/');
                    img_close=hrefArr.pop();  //169.jpg
                    img_close=img_close.split('.');
                    img_close=img_close[0]; //169
                    Scope.parent().find(".main_media").val(img_close);
                    Scope.parent().find(".main_media").addClass('done');
                    flag=1;
                }else{
                    flag=1;
                }
                if(flag){
                    $(this).parent().find('.media_category_sel').show();
                    $(this).parent().find('#uploadAlbum_Uploader').show();
                    $(this).parent().find('#uploadAlbum_Uploader').css('display','block');
                }

            });

            $(".remove-image").live('click',function(event){
                //if it is resolution, delete from tb_media_resolutions
                if($(this).prev().hasClass('pic_r')){
                    var box=$(this).closest('.media_items_sel_box');
                    var img_src=$($(this).parent().find('.image_con')).attr('src');
                    $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' , 'src':img_src , 'action' : 'deleteResolution'}, function(response) {
                       $(document.getElementsByName('stay')[0]).trigger('click');
                       /* $($(box).prev().prev().prev()).off("click",".image_con");
                        $($(box).prev().prev().prev()).trigger('click');
                        $($(box).prev().prev().prev()).addClass('selected_item');*/
                    });
                }else{
                    $.each($(this).parent().find('input[type="hidden"]'),function(key,val){
                        if(!$(val).hasClass('typeArr_size')){
                            $(val).val('');
                        }
                    });
                    trrig1=1;
                    $(this).parent().find('.media_category_sel').trigger('change');
                    var href=$($(this).parent().find('.cancel a')).attr('href');
                    //window.location.href = href;
                }
            });

            $(".media_category_sel").live('change',function(event){
                var Scope=$(this);
                Scope.parent().find (".media_items_sel_box").html("");
                var media_id="";
                var res="";
                var res_id=Scope.parent().attr('rel');
                var global_category_sel=Scope.closest('.media_items_sel_box').parent().find('.media_category_sel');
                //if it is resolution box add class resolution

                if($(Scope.prev().prev().prev().prev()).hasClass('pic_r')){
                    $(Scope).addClass('resolution');
                }
                if(Scope.prev().hasClass('done')){
                    media_id=Scope.prev().val();
                }else if($(global_category_sel).hasClass('resolution')&&(!media_id)){
                    media_id=$(global_category_sel).prev().val();
                }
                if($(Scope.prev().prev().prev().prev()).hasClass('pic_r')){
                    res=1;
                }
                if(!Scope.hasClass('resolution')){
                    $.each(Scope.parent().find(".media_category_sel .resolution"), function( index, value ) {
                        $(value).trigger('change');
                    });
                }

                if(!trrig1){
                    numUpload++;
                    $($(this).parent().find('.upload_div')).html('<br /><input type="button" gallery_id="0" class="buttons orange uploadAlbum" value="העלאה מרובה" id="uploadAlbum_'+numUpload+'" rel="1"  /> &nbsp;');
                    var href=$($(this).parent().find('.cancel a')).attr('href');
                    var valscope=Scope.val();
                    //setTimeout(function(){var k= 2,t=1; var s=0; s=k+t; },1000);
                    uploadImg(numUpload,Scope,valscope,res_id,media_id);
                }
                trrig1=0;
                //for resolution picture (draw just the selecting and image)
                if($(Scope.parent().find('.add-image')).hasClass('pic_r')){
                    $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' ,'category': Scope.val() ,'action' : 'getSelcetItemsOnly'}, function(response) {
                        if(response!==undefined&&response!=""){
                            Scope.parent().find (".media_items_sel").html(response);
                            Scope.parent().find (".media_items_sel").show();
                        }
                    });
                }else{
                    //for Main picture
                    var field_name=Scope.attr('field_name');
                    //var box=$(Scope).closest(".media_items_sel_box");
                    var box=$(Scope).parent().find(".media_items_sel_box");
                    var typeArr=$(box.next()).val();
                    $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' ,'field_name':field_name,'category': Scope.val(),'typeArr': $.parseJSON(typeArr) ,'action' : 'getSelcetItems'}, function(response) {
                        if(response!==undefined&&response!=""){
                            var obj = jQuery.parseJSON(response);
                            if(obj.main_html!==undefined&&obj.main_html!=""){
                                Scope.parent().find (".media_items_sel").html(obj.main_html);
                                Scope.parent().find (".media_items_sel").show();
                            }
                            if(obj.res_html!==undefined&&obj.res_html!=""){
                                Scope.parent().find (".media_items_sel_box").html(obj.res_html);
                                Scope.parent().find (".media_items_sel_box").show();
                            }
                        }
                    });
                }


                Scope.parent().find(".gallery_id").val(Scope.val());
                Scope.parent().find(".image_con").attr("src","");
                Scope.parent().find(".image_con").attr("rel","");
                Scope.parent().find(".image_con").removeClass("selected_item");
                Scope.parent().find(".image_con").hide();
            });

            $(".media_items_sel").live('change',function(event){
                var Scope=$(this);
                var splitStr = $(this).val().split('_');
                mediaCategoryId=splitStr[0];
                mediaItemId=splitStr[1];
                MediaExt=splitStr[2];
                MediaRes=splitStr[3];
                var resol_id=Scope.parent().attr('rel');
                var src_result="";
                var src=$(Scope.next().next()).attr('src');

                var main_media_src=$(Scope.prev().prev()).val();
                //new resolution picture
                if(main_media_src==''||!main_media_src){
                    main_media_src=$(Scope.closest('.media_items_sel_box').prev().prev().prev()).attr('src');
                    if(main_media_src!=''&&main_media_src){
                        main_media_src=main_media_src.split('/');
                        main_media_src=main_media_src[4].split('.');
                        main_media_src=main_media_src[0];
                    }
                }


                //this is change of resolution photo, make ajax update
                if(Scope.prev().hasClass('resolution')&&main_media_src!=''){
                    var box=$(this).closest('.media_items_sel_box');
                    var img_close=$('.table-edit').find('.img_ext_media').next().find('a');
                    var gallery_main=$(img_close).attr("href");
                    if(gallery_main){
                        gallery_main=gallery_main.split('/');
                        gallery_main=gallery_main[3];
                    }
                    $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' ,'main_gallery':gallery_main,'category': Scope.val() ,'action' : 'saveResolution','src':src,'resolution':resol_id,'main_pic':main_media_src}, function(response) {
                        $($(box).prev().prev().prev()).off("click",".image_con");
                        $($(box).prev().prev().prev()).trigger('click');
                        $($(box).prev().prev().prev()).addClass('selected_item');
                    });
                }

                if(Scope.next().next().attr('height')=="0"){
                    Scope.next().next().attr('height',"100");
                }

                if($(this).val()){
                    if(!MediaRes || MediaRes==undefined || MediaRes==""){
                        Scope.next().next().attr("src","/_media/media/" + mediaCategoryId + '/' + mediaItemId + '.' + MediaExt);
                    }else{
                        Scope.next().next().attr("src","/_media/media/" + mediaCategoryId + '/' + mediaItemId + '_'+MediaRes+'.' + MediaExt);
                    }
                    Scope.next().next().attr("rel",mediaItemId);
                    Scope.next().next().show();
                }else{
                    Scope.next().next().hide();
                    Scope.next().next().attr("src","");
                    Scope.next().next().attr("rel","");
                }
				$(document.getElementsByName('stay')[0]).trigger('click');
            });

            $(".image_con").live('click',function(event){
                var Scope=$(this);
                $(this).toggleClass('selected_item');
                if( $(this).hasClass('selected_item')){
                    var check=Scope.prev().prev().prev().prev();
                    if(!(Scope.prev().prev().prev().prev()).hasClass('done')){
                        $(Scope.prev().prev().prev().prev()).val(Scope.attr("rel"));
                    }
                }else{
                    if(!$(Scope.prev().prev().prev().prev()).hasClass('done')){  //main_media has class done..
                        $.each($(".pic_r"), function( index, value ) {
                            if($($(value).next().next().next().next()).attr('style')=="display: inline-block;"){
                                $(value).next().trigger('click');
                            }
                        });
                        // Scope.parent().find(".main_media").val(Scope.attr("rel"));
                    }
                }

                //if this is main picture, draw all the resolutions attached to it
                if(!$(Scope.prev().prev().prev().prev().prev()).hasClass('pic_r')){
                    var field_name=$(Scope.prev().prev().prev()).attr('field_name');
                    var box=$(Scope).parent().find(".media_items_sel_box");
                    var typeArr=$(box.next()).val();
                    var media_id=$(Scope).attr('rel');
                    if(!media_id||media_id==''){
                        media_id=$(Scope).attr('src');
                        media_id=media_id.split('/');
                        media_id=media_id[4].split('.');
                        media_id=media_id[0];
                    }
                    $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category','field_name':field_name,'typeArr': $.parseJSON(typeArr),'action' : 'drawResolutionBoxes','main_pic':media_id}, function(response) {
                        if(response!==undefined&&response!=""){
                            var obj = jQuery.parseJSON(response);
                            if(obj.res_html!==undefined&&obj.res_html!=""){
                                $(Scope.parent().find (".media_items_sel_box")).html(obj.res_html);
                            }else{
                                $.get('/salat2/_ajax/ajax.index.php', { 'file': 'auto_complete/media_category' ,'field_name':field_name,'category': $(Scope.prev().prev().prev()).val(),'typeArr': $.parseJSON(typeArr) ,'action' : 'getSelcetItems'}, function(response) {
                                    var obj = jQuery.parseJSON(response);
                                    Scope.parent().find (".media_items_sel_box").html(obj.res_html);
                                    Scope.parent().find (".media_items_sel_box").show();
                                });
                            }
                        }
                    });
                }
            });

            var upload_file_limit =35840000;
            function uploadImg(numUpload,Scope,gallery_id,resol_id,media_id){
                //   $(".uploadAlbum").each(function(a,item){
                $('#uploadAlbum_'+ numUpload).uploadify({
                    'uploader'  : '/resource/uploadify/uploadify.swf',
                    'script'    : '/resource/uploadify/media.upload.php',
                    'cancelImg' : '/resource/uploadify/cancel.png',
                    'folder'    : '/_media/temp/',
                    'hash'      : '',
                    'album_id'  : gallery_id,
                    'buttonImg' : '../_public/upload.png',
                    'width'	    : 120,
                    'height'    : 27,
                    'fileExt'		: '*.jpg; *.png; *.bmp;*.jpeg;*.gif;*.mov;*.avi;*.mp4;*.wmv;*.mpg',
                    'fileDesc'		: 'קבצי מדיה',
                    'wmode'		: 'transparent',
                    'scriptData':  {'album_id': gallery_id,'resolution_id':resol_id,'media_id':media_id},
                    'auto'      : true,
                    'multi'	    : true,
                    'hideButton'  : true,
                    'onSelect':function(event,ID,fileObj){
                        //add_loader();
                        //remove_loader();
                        if(fileObj.size > upload_file_limit) {
                            alert('גודל קובץ וידיאו מקסימלי  הינו :30 MB');
                            total_video=0;
                            remove_loader();
                            return false;
                        }
                        if(parseInt(gallery_id)==0){
                            alert('תבחר את הגלריה קודם!');
                            remove_loader();
                            return false;
                        }

                    },
                    'onComplete'  : function(event, ID, fileObj, response, data) {
                        trrig1=1;
                        Scope.trigger('change');
                        var responseObj=$.parseJSON( response );
                        if(responseObj.err){
                            alert(responseObj.err);
                            remove_loader();
                        }
                    }

                });
                // });
            }
            /* end of  auto media load */

            /* start choose gallery and show all her items */
            $(".gallery_media").live('change',function(event){
                var div_id=$(this).attr('id')+'_items';
                var Scope=$(this).parent();
                $.get('/salat2/_ajax/ajax.index.php', { 'file': 'getGalleryItems' ,'gal_id': $(this).val() }, function(response) {
                    //	Scope.parent().parent().find(".gallery_media_items").html(response);
                    $("#"+div_id).html(response);
                });
            });
            /* end of choose gallery and show all her items */


		});


	</script>
	<style type="text/css">
		.normTxt.hover td{	background:wheat;	}
		.excel{display:inline;cursor:pointer;width:auto;height:auto;float:left;}
		.redComment{color:red;font-size:10px;font-weight:bold;}
	</style>
</head>
<?php echo $_salat_style;?>
<body>
	<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>

<?
$extra_media_param = '';
if(isset($_REQUEST['from_media_category']) && $_REQUEST['from_media_category']){
 $extra_media_param = '&from_media_category='.$_REQUEST['from_media_category'];
}?>

<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>&show_all=true';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?><?echo $extra_media_param;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
<?if($_Proccess_Make_Excel){?>
   <div class="excel" onclick="get_excel_file('contact');">
   <img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="32" height="32" />
    <br />
      <div class="excel-text"><strong>דו"ח כללי</strong></div>
    </div>
<? } ?> 
<div class="maindiv">

<? if($_Proccess_Has_GenricSearch){ ?>
<br />
<div id="search_frm">
   <form action="" method="get" style="display:inline; font-size:12px; margin:0 auto;">
      	<input type="hidden" name="act" value="show" />
      	<input type="hidden" name="dosearch" value="search" />
      	<table cellpadding="3" cellspacing="0" align="center"><tr>
         <?
            foreach ($fieldsArr AS $key=>$fieldArr) {
               if(isset($fieldArr['input']['searchable']) && ($fieldArr['input']['searchable']==true)){
                     print "<td>".$fieldArr['title'] .":";
               	   print draw_genric_search($fieldArr,$key)."</td>"; 
               }
            }
         ?>      
         <td>
         <span> Search by category </span>
         <input type="text" id="search_ajax" style="width:200px;" /> </td>
         <td><input type="submit" value="Search" class="buttons" /></td>
         </tr></table>
   </form>
</div>
<? } ?>
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
		<tr  data-rowid="<?php echo $row['id']; ?>" class="normTxt" onmouseover="this.className='normTxt hover';" onmouseout="this.className='normTxt';">
			<?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
			<?php  if($_Proccess_Has_Ordering_Action){ ?>
			<td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
			<?php  } ?>
			<td class="dottTblS">
				<?php  if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
				<input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>" onclick="javascript: location.href='?act=new&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp;
				<?php  } if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)){ ?>
				<input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>" onclick="javascript:doAjaxDel(<?php echo $row['id'];?>, '<?php echo (int)$row['order_num'].$fwParams;?>');" /> &nbsp;
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
		<?if(isset($_REQUEST['from_media_category']) && $_REQUEST['from_media_category']){
			$row['category_id'] = $_REQUEST['category_id'];
		}?>
		<?php  fields_get_form_fields( (isset($obj_id) && ($obj_id==0)) ?array_merge($fieldsArr,$fieldsExtreaArr) : $fieldsArr, $row, ($obj_id==0?'add':'new'), false);
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
		<tr><td class="dottTblS redComment" colspan="3">Please pay attention to the image proportions</td></tr>
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
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/black_layer.inc.php'); ?>

</body>
</html>
