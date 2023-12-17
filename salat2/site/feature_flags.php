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
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$_Proccess_Main_DB_Table = "tb_feature_flags";

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
    '0' => 'No',
    '1' => 'Yes',
);

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
    //include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$featureFlagsGroups = array();
$selectGroups = "SELECT `id`, `title` FROM `tb_feature_flags_groups` ORDER BY `id`";
$result = $Db->query($selectGroups);
while ($row = $Db->get_stream($result)) {
    $featureFlagsGroups[$row['id']] = $row['title'];
}

function draw_active_toggles($id, $active) {
    $on = $active == 1 ? ' data-toggle-on="true"' : '';
    $html = '
        <div class="toggle toggle-dark" data-id="'. $id .'" data-toggle-width="80"'. $on .'></div>
    ';

    return $html;
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

        if($_Proccess_Has_MultiLangs){
            $query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
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
        $submit	  	= $_LANG['BTN_ADD'];
    }
    $submitStay = $submit . ' and stay in this page';
}elseif($act=='move'){
    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header ("Location: ?act=show".$fwParams);
    exit();
}elseif($act=='after'){
//die('<hr /><pre>' . print_r($_REQUEST, true) . '</pre><hr />');

    if($obj_id){  // ### UPDATE QUERY ###

        $_REQUEST['inner_id'] = $obj_id;
        if($_Proccess_Has_MultiLangs){
            multi_lang_update_query($_Proccess_Main_DB_Table,$_REQUEST['inner_id'],$fieldsArr,$module_lang_id);
        }else{
            $query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
            $result = $Db->query($query);
        }
    }else{
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);
       // $newOrder = mysqli_result($result,0,0);

        if($_Proccess_Has_MultiLangs){
            multi_lang_insert_query($_Proccess_Main_DB_Table,$fieldsArr,$module_lang_id);

        }else{
            $query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
            $result = $Db->query($query);
            $_REQUEST['inner_id'] = $Db->get_insert_id();
        }

        if($_Proccess_Has_Ordering_Action){
            setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
        }
    }

    if($_Proccess_Has_MetaTags){
        include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
        meta_UpdateTags();
    }

    module_updateStaticFiles();

    if(!empty($_REQUEST['stay'])) {
        header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
    } else {
        header ("Location: ?act=show".$fwParams);
    }
    exit();
}elseif($act=="del"){

    if($_Proccess_Has_MultiLangs){
        $query="DELETE FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}' AND `lang_id`='{$module_lang_id}'";
        // delete language static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/feature_flags/'.get_item_dir($obj_id).'/'.$languagesArr[$module_lang_id]['title'].'/feature_flag-'.$obj_id.'.inc.php');
    }else{
        $query="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        // delete static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/feature_flags/'.get_item_dir($obj_id).'/feature_flag-'.$obj_id.'.inc.php');
    }
    $result = $Db->query($query);


    if($_Proccess_Has_MultiLangs){
        // check if there are no other translations for item - if not, delete item
        $multiLangSql = "SELECT * FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}'";
        $multiLangRes = $Db->query($multiLangSql);
        if ($multiLangRes->num_rows == 0){
            $multiLangSql="DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
            $result = $Db->query($multiLangSql);
        }
    }

    if($_Proccess_Has_Ordering_Action){
        fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
    }
    if($_Proccess_Has_MetaTags){
        include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
    }
    module_updateStaticFiles();
    header("location:?act=show".$fwParams);
    exit();
}else{
    $whereArr = array();
    if( ($_Proccess_Has_GenricSearch) && ($_Proccess_Has_MultiLangs) ){
        genric_searchable_items($_ProcessID,$whereArr,'Result');
    }
    else if($_Proccess_Has_GenricSearch){
        genric_searchable_items($_ProcessID,$whereArr);
        //	die('<hr /><pre>' . print_r($whereArr, true) . '</pre><hr />');
    }

    if($_REQUEST['topcat_id']>0){$whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";}


    $where = (count($whereArr)>0 ? " WHERE ".implode(' AND ', $whereArr) : "");
    $query  = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";

    if($_Proccess_Has_MultiLangs){


        $query  	= "SELECT * FROM {$_Proccess_Main_DB_Table} AS  Main
							LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
								Main.`id`=Lang.`obj_id`
							)
						 WHERE  Lang.`lang_id`='{$module_lang_id}' {$where}";


        if($_Proccess_Has_GenricSearch){

            $query=str_replace($where,' ',$query);
            $query="SELECT * FROM ({$query}) AS Result {$where}";

        }
    }


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
    global $_Proccess_Main_DB_Table,$_Proccess_Has_MultiLangs;
    $UpdateStatic = new featureFlagsUpdateStaticFiles();
    $UpdateStatic->updateStatics();
}

include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <script type="text/javascript" src="../htmleditor/ckeditor/ckeditor.js<?=($act != 'show') ? "?t=" . time() : "";?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
    <script type="text/javascript" src="../_public/jquery-toggles/toggles.js"></script>
    <link rel="stylesheet" href="../_public/jquery-toggles/css/toggles.css" />
    <link rel="stylesheet" href="../_public/jquery-toggles/css/themes/toggles-dark.css" />
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

        $(function() {
            $('.toggle').toggles();

            $('.toggle').on('toggle', function(e, active) {
                var itemID = $(this).data('id');
                var activeState = active ? 1 : 0;

                $.post('/salat2/_ajax/ajax.index.php', {
                    file: 'feature_flags',
                    action: 'updateActiveState',
                    item_id: itemID,
                    active_state: activeState
                }, function(response) {
                    if (response.err != '') {
                        alert(response.err);
                    } else {
//                        $('.dv_select_category').html(response.html);
                    }
                }, 'json');
            });
        });

    </script>
    <style type="text/css">
        /* auto media load */
        .selected             { display: block; }
        .show   {display:block;}
        .off   {display:none;}
        #image_con   { height:40px;cursor:pointer;}
        .selected_item{border:1px solid red;}
        /* end of  auto media load */
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
 
                        <td><input type="submit" value="Search" class="buttons" /></td>
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
                        include_once($_project_server_path . $_salat_path . "_inc/metaform.inc.php");
                        ?>
                    </td>
                    </tr><?php  } ?>
                <tr>
                    <td class="dottTblS" colspan="3">
                        <input type="submit" name="send" value="<?php echo $submit;?>" class="buttons" onclick="document.getElementById('loader').style.display='';this.style.display='none';" />
                        <input type="submit" name="stay" value="<?php echo $submitStay;?>" class="buttons" onclick="document.getElementById('loader').style.display='';this.style.display='none';" />
                        <div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" />Processing, Please wait . . .</div>
                    </td>
                </tr>
            </table>

        </form>
    <?php  } ?>
</div>
</body>
</html>
