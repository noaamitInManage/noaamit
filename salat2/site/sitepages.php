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
include_once($_project_server_path.$_includes_path.'site.array.inc.php'); // 15/04/2011
$_Proccess_Main_DB_Table = "tb_sitepages";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = true;

$_Proccess_Has_MultiLangs = true;

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
$_Proccess_FW_Params = array(
);

$_yesNo_arr = array("לא", "כן");

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$dir = $_project_server_path.$_media_path.str_replace('.php', '', basename(__FILE__)).'/';

if($_Proccess_Has_MultiLangs){
//	include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

/* Fetch all salatModules */
$salatModulesArr = array();
$salatModulesSql = "SELECT * FROM `tb_sys_processes` WHERE `id` IN (SELECT `processid` FROM `tb_sys_user_permissions` WHERE `sysuserid`={$_SESSION['salatUserID']})";
$salatModulesRes = $Db->query($salatModulesSql);
while($salatModule = $Db->get_stream($salatModulesRes)){
    $salatModulesArr[$salatModule['id']] = $salatModule['title'] . '( '.$salatModule['page'].' )';
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
    $fwParams[]=$param.'='.$_REQUEST[$param];
}
$fwParams = htmlspecialchars('&'.implode('&', $fwParams));

define("_PAGING_NumOfItems"			, 100);	// number of rows in page
define("_PAGING_NumOfLinks"			, 5);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"	, '<a href="?pagenum={PAGENUM}">{CONTENT}</a>');


// echo '<pre style="direction: ltr; text-align: left;">';
// 	print_r( $languagesArr );
// echo '</pre>';
// exit();


$act = $_REQUEST['act'];
if ($act=='') $act="show";
$obj_id = (int)$_REQUEST['id'];

if($act=='new'){
    if($obj_id){
        $query  	= "SELECT Module.* FROM {$_Proccess_Main_DB_Table} AS Module WHERE Module.id='{$obj_id}'";
        $result 	= $Db->query($query);
        $row    	= $Db->get_stream($result);
        $submit	   	= $_LANG['BTN_UPDATE'];

        // Check if this is an advanced seo module and fetch relevant details
        if ($row['is_advanced'] == 1){
            $query  	= "SELECT Module.*,Seo.*,SeoLang.da_file_name, SeoLang.url FROM {$_Proccess_Main_DB_Table} AS Module 
				LEFT JOIN `tb_seo` AS Seo ON Seo.module_id=Module.mdl_id 
				LEFT JOIN `tb_seo_lang` AS SeoLang ON SeoLang.obj_id=Module.mdl_id 
				WHERE Module.id='{$obj_id}' AND SeoLang.lang_id={$module_lang_id}";
            $result 	= $Db->query($query);
            $row    	= $Db->get_stream($result);
        }
    }else{
        $submit	  	= "שמור";
    }
    $submitStay = $submit . ' והשאר';
}elseif($act=='move'){
    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header ("Location: ?act=show".$fwParams);
    exit();
}elseif($act=='after'){
    // echo '<pre style="direction: ltr; text-align: left;">';		print_r( $_REQUEST );	echo '</pre>';		exit();
    if($obj_id){
        $query="UPDATE {$_Proccess_Main_DB_Table} SET ".fields_implode(',', $fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
        $result = $Db->query($query);
        $_REQUEST["inner_id"] = $_REQUEST["mdl_id"];
//		if($_Proccess_Has_MultiLangs){
//			td_Update($_Proccess_Main_DB_Table,$obj_id);
//		}
    }else{
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);
//		$newOrder = mysqli_result($result,0,0);
        $query	= "INSERT INTO {$_Proccess_Main_DB_Table}(".fields_implode(', ', $fieldsArr).") VALUES (".fields_implode(',', $fieldsArr, $_REQUEST).")";
        $result = $Db->query($query);
        $_REQUEST["inner_id"] = $_REQUEST["mdl_id"];
//		if($_Proccess_Has_MultiLangs){
//			td_Insert($_Proccess_Main_DB_Table,$obj_id);
//		}
        if($_Proccess_Has_Ordering_Action){
            setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $obj_id);
        }
    }

    if($_Proccess_Has_MetaTags){
        include_once($_project_server_path.$_salat_path.$_includes_path."metaupdate.inc.php");
        meta_UpdateTags();
    }

    if ($_REQUEST['is_advanced'] == 1){
        $seoFields = array(
            'url' 		=> $Db->make_escape($_REQUEST['url']),
            'module_id' 		=> intval($_REQUEST['mdl_id']),
            'file_name' 		=> $Db->make_escape($_REQUEST['file_name']),
            'arr_name' 			=> $Db->make_escape($_REQUEST['arr_name']),
            'priority'			=> floatval($_REQUEST['priority']),
            'seo_strict'		=> intval($_REQUEST['seo_strict']),
            'da_smart_dir'	=> $Db->make_escape($_REQUEST['da_smart_dir']),
            'da_arr_name'		=> $Db->make_escape($_REQUEST['da_arr_name'])
        );
        // save advanced SEO fields to database
        $query	= "INSERT INTO `tb_seo` (module_id,file_name,arr_name,priority,seo_strict,da_smart_dir,da_arr_name) VALUES 
			({$seoFields['module_id']},
			'{$seoFields['file_name']}',
			'{$seoFields['arr_name']}',
			{$seoFields['priority']},
			'{$seoFields['seo_strict']}',
			'{$seoFields['da_smart_dir']}',
			'{$seoFields['da_arr_name']}')
		 	ON DUPLICATE KEY UPDATE
			`module_id`={$seoFields['module_id']},
			`file_name`='{$seoFields['file_name']}',
			`arr_name`='{$seoFields['arr_name']}',
			`priority`={$seoFields['priority']},
			`seo_strict`={$seoFields['seo_strict']},
			`da_smart_dir`='{$seoFields['da_smart_dir']}',
			`da_arr_name`='{$seoFields['da_arr_name']}'";
        $result = $Db->query($query);
        $result->free();
        // save da_file_name as multiLang
        $url = $Db->make_escape($_REQUEST['url']);
        $da_file_name = $Db->make_escape($_REQUEST['da_file_name']);
        $query = "INSERT INTO `tb_seo_lang` (obj_id,lang_id,url,da_file_name) VALUES ({$seoFields['module_id']},{$module_lang_id},'{$url}','{$da_file_name}') ON DUPLICATE KEY UPDATE `da_file_name`='{$da_file_name}', `url`='{$url}'";
        $result = $Db->query($query);

        $seoFields['directAccess'] = array(
            'smart_dir' => $seoFields['da_smart_dir'],
            'file' => $da_file_name,
            'arr_name' => $seoFields['da_arr_name'],
        );
        unset($seoFields['da_smart_dir']);
        unset($seoFields['da_arr_name']);

        $smart_dir = smartDirctory('/_static/modules',$seoFields['module_id']);
        $langDir = $smart_dir.$languagesArr[$module_lang_id]['title'];
        if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$langDir)){
            @mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$langDir,0777);// crate a new dir
            chmod($_SERVER['DOCUMENT_ROOT'].'/'.$langDir,0777);// if the first line didnt Success
        }
        updateStaticFile($seoFields, $langDir.'/seo-'.$seoFields['module_id'].'.inc.php', 'moduleSEOArr');
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
    global $_Proccess_Main_DB_Table,$languagesArr, $Db;


    $query = "SELECT `id`, `mdl_id`, `mdl_name`, `is_static`, `salat_module`, `react_route_name`  FROM {$_Proccess_Main_DB_Table}";
    $result = $Db->query($query);
    $moduleNameArr = array();
    $react_modulesArr = array();
    $front_salat_modulesArr = array();
    while ($item = $Db->get_stream($result)){
        $moduleNameArr[$item['mdl_id']][$item['is_static']]=$item['mdl_name'];
        $front_salat_modulesArr[intval($item['mdl_id'])] = intval($item['salat_module']);
        $react_modulesArr[strtolower($item["react_route_name"])] = $item["mdl_id"];
    }
    updateStaticFile($moduleNameArr, '/_static/modules.inc.php', 'moduleNameArr');
    updateStaticFile($react_modulesArr, '/_static/react_modules.inc.php', 'react_modulesArr');
    updateStaticFile($front_salat_modulesArr, '/_static/front_salat_modules_link.inc.php', 'front_salat_modules_linkArr');

    updateStaticFile("SELECT `id`, `mdl_id`, `mdl_name`, `is_static` 
				FROM {$_Proccess_Main_DB_Table}
					WHERE `is_static` =1
	", '/_static/static_modules.inc.php', 'staticModuleNameArr','mdl_id',true);


    foreach ($languagesArr as $lang_id => $langData) {
        $seoArr = array();
        $seoSql = "SELECT SeoLang.url, Seo.module_id, Seo.file_name, Seo.arr_name, Seo.priority,Seo.seo_strict, Seo.da_smart_dir,SeoLang.da_file_name,Seo.da_arr_name,Module.mdl_name  
			FROM `tb_seo` AS Seo 
			LEFT JOIN `tb_sitepages` AS Module ON Module.mdl_id=Seo.module_id
			LEFT JOIN `tb_seo_lang` AS SeoLang ON Module.mdl_id=SeoLang.obj_id
			WHERE Module.is_static=0 AND Module.is_advanced=1 AND SeoLang.lang_id={$lang_id}";
        $seoRes = $Db->query($seoSql);
        while($item = $Db->get_stream($seoRes)){
            $url = $item['url'];
            if ($item['url'] == '') { $url = $item['mdl_name']; }
            $item['directAccess'] = array(
                'smart_dir' => $item['da_smart_dir'],
                'file' => $item['da_file_name'],
                'arr_name' => $item['da_arr_name'],
            );
            unset($item['url']);
            unset($item['da_smart_dir']);
            unset($item['da_file_name']);
            unset($item['da_arr_name']);

            $seoArr[$url] = $item;
        }
        if (!empty($seoArr)){
            updateStaticFile($seoArr, "/_static/seo.{$langData['title']}.inc.php", 'categoryArr','id',true,true);
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
    <script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
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

        $(function(){
            var hide_advanced = true;
            if ($('select[name="is_static"]').val() == 0){ // is not a static module
                if ($('input[name="is_advanced"]:checked').val() == 1){
                    hide_advanced = false;
                    $("input[name='meta_urlalias']").prop('disabled','disabled').closest('tr').hide();;
                }
            }else{
                $('input[name="is_advanced"]').prop('disabled','disabled').closest('tr').hide();
            }
            if (hide_advanced){
                $('*[data-adv="1"]').prop('disabled','disabled').closest('tr').hide();
            }
            $('select[name="is_static"]').on('change',function(){
                if ($(this).val() == 1){
                    $('input[name="is_advanced"]').prop('disabled','disabled').closest('tr').hide();
                    $('*[data-adv="1"]').prop('disabled','disabled').closest('tr').hide();
                    $("input[name='meta_urlalias']").prop('disabled','').closest('tr').show();;
                }else{
                    $('input[name="is_advanced"]').prop('disabled','').closest('tr').show();
                    if ($('input[name="is_advanced"]:checked').val() == 0){
                        $("input[name='meta_urlalias']").prop('disabled','').closest('tr').show();;
                        $('*[data-adv="1"]').prop('disabled','disabled').closest('tr').hide();
                    }else{
                        $('*[data-adv="1"]').prop('disabled','').closest('tr').show();
                    }
                }
            });
            $('input[name="is_advanced"]').on('change',function(){
                if ($(this).val() == 1){
                    $('*[data-adv="1"]').prop('disabled','').closest('tr').show();
                    $("input[name='meta_urlalias']").prop('disabled','disabled').closest('tr').hide();;
                }else{
                    $('*[data-adv="1"]').prop('disabled','disabled').closest('tr').hide();
                    $("input[name='meta_urlalias']").prop('disabled','').closest('tr').show();;
                }

            });
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
                <td width="50">&nbsp;</td>
            </tr>
            <?php  for($count = $resultArr['result']->num_rows,$i=0;$row = $Db->get_stream($resultArr['result']);$i++){ ?>
                <tr class="normTxt" >
                    <?php  $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
                    <?php  if($_Proccess_Has_Ordering_Action){ ?>
                        <td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']);?></td>
                    <?php  } ?>
                    <td class="dottTblS" style="width: 100px; ">
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
                    <td colspan="2">Add/ Edit</td>
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
                        $_REQUEST['id']=$row['mdl_id'];
                        include_once($_project_server_path.$_salat_path."_inc/metaform.inc.php");
                        ?>
                    </td>
                    </tr><?php  } ?>
                <tr>
                    <td class="dottTblS" colspan="3">
                        <input type="submit" name="send" value="<?php echo $submit;?>" onclick="return validateForm(this);" class="buttons" />
                        <input type="submit" name="stay" value="<?php echo $submitStay;?>" onclick="return validateForm(this);" class="buttons" />
                        <div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif" /> Processing . . .</div>
                    </td>
                </tr>
            </table>

        </form>
    <?php  } ?>
</div>
</body>
</html>
