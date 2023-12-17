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

$_Proccess_Main_DB_Table = "tb_media_category";

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
$_Proccess_FW_Params = array('orderby','ordertype'
);

$_yesNo_arr = array(
    '0' => 'No',
    '1' => 'Yes',
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
       // $newOrder = mysqli_result($result,0,0);
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

    if(isset($_REQUEST['from_ajax'])){
        $answer=array("err"=>"","msg"=>"","relocation"=>"","html"=>"");
        $answer['html'] = $_REQUEST['inner_id'];
        echo json_encode($answer);
        exit();
    }

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

    if($_Proccess_Has_GenricSearch){
        genric_searchable_items($_ProcessID,$whereArr);
    }

    if($_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}

    $where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
    $query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
    if(array_key_exists($_REQUEST['orderby'], $fieldsArr)){
        $query .= " ORDER BY `{$_REQUEST['orderby']}`";
        if($_REQUEST['ordertype']){
            $query .= ' '.$Db->make_escape($_REQUEST['ordertype']);
        }
    }elseif($_Proccess_Has_Ordering_Action){$query .= " ORDER BY order_num";}
    else
    {
        $query .= " ORDER BY id DESC";
    }

	if(isset($_REQUEST['csv']) && ($_REQUEST['csv'])){
		query_to_csv($query,$_Proccess_Main_DB_Table.'_log_'.date('d_m_Y').'.csv',true);
		exit();
	}
    $resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles() {
    global $_Proccess_Main_DB_Table;
    $UpdateStatic = new mediaCategoryParagraphUpdateStaticFiles();
    $UpdateStatic->updateStatics();
}

include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <link rel="stylesheet" media="screen,print" href="/resource/uploadify/uploadify.css" type="text/css" />

    <script type="text/javascript" src="../htmleditor/ckeditor/ckeditor.js<?=($act != 'show') ? "?t=" . time() : "";?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>

    <script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js?t=<?=filemtime($_SERVER["DOCUMENT_ROOT"] ."/resource/uploadify/jquery.uploadify.v2.1.4.min.js")?>"></script>


    <script type="text/javascript"> if (window.parent==window) location.href = '../frames.php'; </script>
    <script type="text/javascript">

        function doDel(rowID,ordernum){
            if(confirm("Are you sure you want to delete this record?		\n		כל הפריטים המשוייכים לקטגוריה זו יאבדו את שיוכם		")){
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

        <?$upload_file_limit =35840000; //1024 * 1000 * 35?>
        $(function() {

            $(".uploadAlbum").each(function(a,item){
                $('#uploadAlbum_'+ $(item).attr("rel")).uploadifive({
                    //'uploader'  : '/resource/uploadify/uploadify.swf',
                    //'script'    : '/resource/uploadify/media.upload.php',
                    'fileObjName'      : 'Filedata',
                    'uploadScript'    : '/resource/uploadify/media.upload.php',
                    'cancelImg' : '/resource/uploadify/cancel.png',
                    'folder'    : '/_media/temp/<?=$user_hash;?>',
                    'buttonText'  : 'בחר קובץ',
                    'removeCompleted' : true,
                    'hash'      : '<?=$user_hash;?>',
                    'album_id'      : $(item).attr("rel"),
                    'buttonImg' : '../_public/upload.png',
                    'width'	    : 120,
                    'height'    : 27,
                    //'fileType'		: ['*.jpg;', '*.png;', '*.bmp;', '*.jpeg;', '*.gif;', '*.mov;', '*.avi;', '*.mp4;', '*.wmv;', '*.mpg'],
                    'fileType'		: ['image/*', 'video/vnd.sealedmedia.softseal-mov', 'application/vnd.avistar+xml', 'video/mp4' ],
                    'fileDesc'		: 'קבצי מדיה',
                    'wmode'		: 'transparent',
                    'formData': {'album_id':$(item).attr("rel")},
                    'auto'      : true,
                    'multi'	    : true,
                    'hideButton'  : false,
                    'onUploadFile' : function(fileObj) {
                        if(fileObj.size > <?=$upload_file_limit;?>) {
                            alert('גודל קובץ וידיאו מקסימלי  הינו :30 MB');
                            total_video=0;
                            remove_loader();
                            return false;
                        }
                    },
                    'onUploadComplete'  : function( response, data) {
                        var responseObj=$.parseJSON( data );
                        if(responseObj.err){
                            alert(responseObj.err);
                            remove_loader();
                        }
                    }
                });

            });
        });


    </script>
    <style type="text/css">
        .normTxt.hover td{	background:wheat;	}
        .excel{display:inline;cursor:pointer;width:auto;height:auto;float:left;}
        dottTblS object{float:left; margin-bottom:-10px !important;}
    </style>
</head>
<?php echo $_salat_style;?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
<div class="titleTxt"><?php echo $_Proccess_Title;?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
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
                            if($fieldArr['input']['searchable']==true){
                                print "<td>".$fieldArr['title'] .":";
                                print draw_genric_search($fieldArr,$key)."</td>";
                            }
                        }
                        ?>
                        <td></td>
                        <td><input type="submit" value="חיפוש" class="buttons" /></td>
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
                <td width="150">&nbsp;</td>
            </tr>
            <?php  for($count = $resultArr['result']->num_rows,$i=0;$row = $Db->get_stream($resultArr['result']);$i++){ ?>
                <tr class="normTxt">
                    <?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
                    <?php  if($_Proccess_Has_Ordering_Action){ ?>
                        <td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
                    <?php  } ?>
                    <td class="dottTblS" width="260">
                        <?php  if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)){ ?>
                            <input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>" onclick="javascript: location.href='?act=new&id=<?php echo $row['id'].$fwParams;?>';" /> &nbsp;
                        <?php  } if(!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)){ ?>
                            <input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>" onclick="javascript:doDel(<?php echo $row['id'];?>, '<?php echo (int)$row['order_num'].$fwParams;?>');" /> &nbsp;
                        <?php  } ?>
                        <input type="button" class="buttons" value="Items" onclick="javascript: location.href='media.php?from_media_category=1&category_id=<?php echo $row['id'];?>';" /> &nbsp;
                        <input type="button" class="buttons orange uploadAlbum" value="Multi Upload" id="uploadAlbum_<?=$row['id'];?>" rel="<?=$row['id'];?>"  /> &nbsp;
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
<td colspan="2"><?=$_LANG['TXT_ADD&EDIT'];?></td>
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
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/black_layer.inc.php'); ?>

</body>
</html>