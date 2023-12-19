<?php
include_once("../_inc/config.inc.php");

$this_dir = basename(dirname(__FILE__));

$_ProcessID = get_module_id(basename(__FILE__), $this_dir); // 05/10/11


if ($langID == '') $langID = $_SESSION['salatLangID'];
include_once($_project_server_path . $_salat_path . "_static/langs/processes/" . $_ProcessID . "." . $langID . ".inc.php");
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");

$file = $_project_server_path.$_salat_path . '/_static/menus/modulesArr.'.$_SESSION["salatLangID"].'.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}

include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
require_once($_project_server_path . $_salat_path . $this_dir . "/modules_fields/" . str_replace('.php', '', basename(__FILE__)) . ".fields.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$store_properties = array(
    1 => 'is_koser',
    2 => 'has_shipping',
    4 => 'is_accessible',
);

$_Proccess_Main_DB_Table = "tb_stores";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

//order_num FIELD
$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = true;

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
$_Proccess_HC_RowsID_Arr_NOT_EDITABLE = array();

/**
 * Array of HardCoded Rows that system users can not DELETE
 */
$_Proccess_HC_RowsID_Arr_NOT_DELETABLE = array();

/**
 * Array of forwarded parameters from $_REQUEST
 */
$_Proccess_FW_Params = array('lang_id');

$_yesNo_arr = array(
    '0' => 'לא',
    '1' => 'כן',
);

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$dir = $_project_server_path . $_media_path . str_replace('.php', '', basename(__FILE__)) . '/';

if ($_Proccess_Has_MultiLangs) {
    //include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
    $fwParams[] = $param . '=' . $_REQUEST[$param];
}

$fwParams = htmlspecialchars('&' . implode('&', $fwParams));

define("_PAGING_NumOfItems", 25);    // number of rows in page
define("_PAGING_NumOfLinks", 5);    // number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template", '<a href="?pagenum={PAGENUM}' . $fwParams . '">{CONTENT}</a>');

$act = $_REQUEST['act'];
if ($act == '') $act = "show";
$obj_id = (int)$_REQUEST['id'];


// if ($obj_id) is true => edit. else => add.
if ($act == 'new') {
    if ($obj_id) {
        //get info about obj_id from the main db
        $query = "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";

        //get more info about obj_id from the lang db , based on lang_id
        if ($_Proccess_Has_MultiLangs) {
            $query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`='{$module_lang_id}'";
        }

        // fetch result from table
        $result = $Db->query($query);

        //row doesnt exist in lang table so fetch from default_lang_id (from project.inc.php)
        if ($result->num_rows == 0) {
            $query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`=" . default_lang_id;
            $result = $Db->query($query);
        }
        $row = $Db->get_stream($result);
        $submit = "עדכן";
    } else {
        $submit = "הוסף";
    }
    $submitStay = $submit . " {$_LANG['AND_STAY']}" ;
}
elseif ($act == 'move') {
    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header("Location: ?act=show" . $fwParams);
    exit();
}
elseif ($act == 'after') {

    if ($obj_id) {  // ### UPDATE QUERY ###

        $_REQUEST['inner_id'] = $obj_id;
        if ($_Proccess_Has_MultiLangs) {
            multi_lang_update_query($_Proccess_Main_DB_Table, $_REQUEST['inner_id'], $fieldsArr, $module_lang_id);
        } else {
            $query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
            $result = $Db->query($query);
        }
    }
    //insert with multi_lang_insert_query function - first to the main DB and then to _lang, according to $module_lang_id
    else {
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);

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
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/or_logs.txt', DateTime::createFromFormat('U.u',sprintf("%.6F", microtime(true)))->format("m-d-Y H:i:s.u")." : ". print_r(array(
            'update_dynamic_table' => 'in update_dynamic_table',
            'Here: ' . __LINE__ . ' at ' . __FILE__
        ), true) . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (isset($_REQUEST['dynamTable']['id']) && $_REQUEST['dynamTable']['id']) {

        update_dynamic_table($_REQUEST['inner_id']);
    }

    if ($_Proccess_Has_MetaTags) {
        include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
        meta_UpdateTags();
    }

    module_updateStaticFiles();

    if (!empty($_REQUEST['stay'])) {
        header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
    } else {
        header("Location: ?act=show" . $fwParams);
    }
    exit();
}
elseif ($act == "del") {

    if ($_Proccess_Has_MultiLangs) {
        $query = "DELETE FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}' AND `lang_id`='{$module_lang_id}'";
        // delete language static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/tmpl/' . get_item_dir($obj_id) . '/' . $languagesArr[$module_lang_id]['title'] . '/tmpl-' . $obj_id . '.inc.php');
    } else {
        $query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        // delete static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/tmpl/' . get_item_dir($obj_id) . '/tmpl-' . $obj_id . '.inc.php');
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
}
else {
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
    if (isset($_REQUEST['csv']) && ($_REQUEST['csv'])) {
        query_to_csv($query, $_Proccess_Main_DB_Table . '_log_' . date('d_m_Y') . '.csv', true);
        exit();
    }
    $resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles()
{
    global $_Proccess_Main_DB_Table, $_Proccess_Has_MultiLangs;

    if($_Proccess_Has_MultiLangs){
        $UpdateStatic = new storesLangsUpdateStaticFiles();
        $UpdateStatic->updateStatics();
    }else{
        $UpdateStatic = new storesLangsUpdateStaticFiles();
        $UpdateStatic->updateStatics();
    }

}

function draw_dynamic_table($table_link = '', $main_table = '', $field_id_name = '', $and_where = '')
{
    global $langID, $row;

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/or_logs.txt', DateTime::createFromFormat('U.u',sprintf("%.6F", microtime(true)))->format("m-d-Y H:i:s.u")." : ". print_r(array(
    'draw_dynamic_table' => 'start draw dynamic table',
    'Here: ' . __LINE__ . ' at ' . __FILE__
    ), true) . PHP_EOL, FILE_APPEND | LOCK_EX);

    $Db = Database::getInstance();

    $itemsDyHTML = '';
    if ($langID == '2') { // 2 = hebrew
        $dirStyle = 'rtl';
    } else {
        $dirStyle = 'ltr';
    }
    $main_table = "tb_stores";
    $table_link = "tb_stores_link";
    $and_where = "WHERE Main.id='{$row['id']}'";
    $field_id_name = 'store_id';
    $itemsDyHTML .= '<div class="dynamicTableItems">';
    $dynamSql = "SELECT Link.*
					FROM `{$table_link}` AS Link
						LEFT JOIN `{$main_table}` as Main ON(
							Link.`{$field_id_name}`=Main.id
						)
						 {$and_where}";

    $dynamRes = $Db->query($dynamSql);
    if ($dynamRes->num_rows > 0) {
        $count_index = 0;
        $cells = array();
        while ($item = $Db->get_stream($dynamRes)) {
            $order_num = ($item['order_num']) ? $item['order_num'] : $count_index + 1;

            //category title
            $cells[$count_index][0] = array(
                'type' => 'text',
                'value' => $item['category_title'],
                'name' => 'dynamTable[category_title][' . $count_index . ']',
                'direction' => $dirStyle
            );

            //obj_id of the category
            $cells[$count_index][1] = array(
                'type' => 'hidden',
                'value' => $item['category_id'],
                'name' => 'dynamTable[category_id][' . $count_index . ']',
            );

            $cells[$count_index][2] = array(
                'extra' => 'id="' . $count_index . '"',
                'class' => 'del_table',
            );
            $count_index++;
        }
    }
    // no categories in the store
    else{
        $count_index = 0;

        $cells[$count_index][0] = array(
            'type' => 'text',
            'value' => '',
            'name' => 'dynamTable[category_title][' . $count_index . ']',
            'direction' => $dirStyle
        );

        //obj_id of the category
        $cells[$count_index][1] = array(
            'type' => 'hidden',
            'value' => '',
            'name' => 'dynamTable[category_id][' . $count_index . ']',
        );

        $cells[$count_index][1] = array(
            'extra' => 'id="' . $count_index . '"',
            'class' => 'del_table',
        );
    }
    $itemsDyHTML .= make_dynamic_table($cells, array('קטגוריה'), true, true, true);
    $itemsDyHTML .= '</div>';
    return $itemsDyHTML;

}

function update_dynamic_table($id)
{
    global $row;

    $Db = Database::getInstance();
    $id = ($id) ? $id : $row['id'];
    $table = 'tb_stores_link';
    foreach ($_REQUEST['dynamTable']['id'] as $num => $itemArr) {
        $update_fl = 0;
        $dyn_id = $_REQUEST['dynamTable']['id'][$num];

        if ($dyn_id) {
            $query = "SELECT id FROM `{$table}` WHERE id={$dyn_id}";
            $col_result = $Db->query($query);
            if ($col_result->num_rows > 0) {
                $update_fl = 1;
            }
        }
//        if ($update_fl) {//update
//            $db_fields = array(
//                'title' => $_REQUEST['dynamTable']['title'][$num],
//            );
//            $updateArr = array();
//            foreach ($db_fields as $k => $v) {
//                $v = $Db->make_escape($v);
//                $updateArr[] = "`$k` = '{$v}' ";
//            }
//            $query = "UPDATE `{$table}` SET  " . implode(',', $updateArr) . " WHERE `id`={$dyn_id}";
//            $Db->query($query);
//        }
        else {//insert
            $db_fields = array(
                'store_id' => $id,
                'category_title' => $_REQUEST['dynamTable']['category_title'][$num],
            );
            foreach ($db_fields AS $key => $value) {
                $db_fields[$key] = $Db->make_escape($value);
            }

            $query = "SELECT FROM obj_id from tb_categories_langs WHERE title = {$db_fields['category_title']} ";
            $res = $Db->query($query);

            if ($res->num_rows == 1) {
                $item = $Db->get_stream($query);
                $db_fields['category_id'] = $item['obj_id'];
            } else {
                die("<pre>" .print_r(array($db_fields['category_id'] , 'Here: ' . __LINE__ . ' at ' . __FILE__) ,true) ."</pre>");
            }

            $query = "INSERT INTO `{$table}` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
            $Db->query($query);
        }
    }
}

include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/bootstrap.min.css" type="text/css">
    <link rel="StyleSheet" href="../_public/bootstrap-rtl.min.css" type="text/css">
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <link rel="stylesheet" href="../_public/x-editable/bootstrap3-editable/css/bootstrap-editable.css" type="text/css"/>
    <link rel="StyleSheet" href="../_public/colorpicker.css" type="text/css">
    <link rel="stylesheet" href="/_media/css/plugins/jquery.autocomplete.css" type="text/css">
    <link rel="stylesheet" href="../_public/select2/css/select2.css" type="text/css">
    <link rel="stylesheet" href="/salat2/_public/jquery-ui.css">

    <link rel="stylesheet" media="screen,print" href="/resource/uploadify/uploadify.css" type="text/css"/>

    <script type="text/javascript" src="../_public/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../_public/jquery-ui.js"></script>
    <script type="text/javascript" src="/_media/js/plugins/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="../_public/colorpicker.min.js"></script>
    <script type="text/javascript" src="../_public/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="../_public/bootstrap.min.js"></script>
    <script type="text/javascript"
            src="../_public/x-editable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script type="text/javascript" src="../_public/media_select.js"></script>
    <script type="text/javascript"
            src="../htmleditor/ckeditor/ckeditor.js<?= ($act != 'show') ? "?t=" . time() : ""; ?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
    <script type="text/javascript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script type="text/javascript">
        function doDel(rowID, ordernum) {
            if (confirm("<?=$_LANG['DEL_MESSAGE'];?>")) {
                <?    $lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id=' . $module_lang_id : '';?>
                document.location.href = "?act=del<?=$lang_fw;?>&id=" + rowID + "&order_num=" + ordernum;
            }

            $(function () {

                var $editable = $('.salat-editable');
                $editable.editable();

                $editable.on('save', function(e, params) {
                    var field = $(this).data('field');
                    var newValue = params.newValue;
                });

                $('.addRowGenTable').live('click', function () {
                    var genTable;
                    if ($(this).closest('tr').parent().hasClass('genricTable')) {
                        genTable = $(this).closest('tr').parent();
                    } else {
                        genTable = $(this).closest('tr').parent().parent();
                    }
                    var btn = $(this);
                    var tr_row = $(this).closest('tr').prev();
                    var dest_clone = $(tr_row).clone();
                    var new_index = parseInt($(genTable).attr('last_index')) + 1;
                    $(genTable).attr('last_index', new_index); //update the index
                    //go through each td in NEW row and update the Name to new index
                    $.each($(dest_clone).find('td').children(), function (key, val) {
                        if ($(val).attr('type') == 'text' || $(val).attr('type') == 'hidden' || $(val).is("select")) {
                            $(val).val(''); //empty value of clone..
                        }

                        if ($(val).attr('type') == 'checkbox' || $(val).attr('type') == 'radio') {
                            $(val).prop('checked', false);
                        }

                        var old_name = $(val).attr('name');
                        var new_name = '';
                        if (old_name !== undefined && old_name != '') {
                            var pos = 0;
                            pos = old_name.indexOf("[");
                            if (pos > -1) {
                                var res = old_name.substr(0, (old_name.length - 2));
                                $(val).attr('name', res + new_index + ']');
                                new_name = res + new_index + ']';
                            }
                        }

                        if ($(val).attr('type') == "hidden") {
                            $(val).val('');
                        }
                        //update the name inside onlick for date(time)picker only
                        if ($(val).next().attr('onclick') != undefined && $(val).next().attr('onclick') != '') {
                            var newOnclick = $(val).next().attr('onclick');
                            newOnclick = newOnclick.replace("FIELD_" + old_name, "FIELD_" + new_name);
                            $(val).attr('id', "FIELD_" + new_name);
                            $(val).next().attr('onclick', newOnclick);
                        }
                    });
                    $($(this).closest('tr')).before($(dest_clone));
                });
                $('.delRowGenTable').live('click', function () {
                    $(this).closest('tr').remove();
                });

                $('.delRowGenTable').live('click', function () {
                    var item_id = '<?=$obj_id;?>';

                    if ($(this).hasClass('del_table')) {
                        $.get('/salat2/_ajax/ajax.index.php', {
                            'file': 'stores_service',
                            'id': $(this).attr('id'),
                            'act': 'delete_rows',
                            'item_id': item_id
                        }, function (response) {

                        });
                    }
                });

                $('.addRowGenTable').live('click', function () {
                    var table = $(this).closest('.genricTable');
                    var last_index = $(table).attr('last_index');
                    var last_label = document.getElementsByName('dynamCollected[total_count][' + last_index + ']')[0];
                    var val_lab = $(last_label).prev().html();
                    if (!val_lab || val_lab == undefined) {
                        val_lab = $(last_label).val();
                    }
                    var class_lab = $(last_label).attr('class');
                    $(last_label).parent().html('<input type="text" value="' + val_lab + '" name="dynamCollected[total_count_set][' + last_index + ']" class="' + class_lab + '" style="direction:ltr;width:104px;" />');
                });


            });
        }

    </script>
    <style type="text/css">
        /* auto media load */
        .selected {
            display: block;
        }

        .show {
            display: block;
        }

        .off {
            display: none;
        }

        #image_con {
            height: 40px;
            cursor: pointer;
        }

        .selected_item {
            border: 1px solid red !important;
        }

        /* end of  auto media load */
        .show_Xdays {
            float: left;
            margin-left: 1100px;
            margin-top: -29px;
        }

        .err {
            color: red;
        }

        .genricTable {
            width: 800px !important;
        }

        .genricTable th, .genricTable td {
            white-space: nowrap;
        }

        .grey_table th {
            background: grey !important;
        }

        .gallery_media {
            /*line-height: 0px;*/
        }

        tr .dottTblS:first-child {
            width: 100px;
        }

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
<?php echo $_salat_style; ?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?php echo $_Proccess_Title; ?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams; ?>';"
       value="הצג הכל"/>
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams; ?>';"
       value="הוסף חדש"/>
<div class="maindiv">

    <? if ($_Proccess_Has_MultiLangs) { ?>
        <?= draw_module_tabs(); ?>
    <? } ?>
    <?php if ($act == "show") { ?>
        <table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
            <tr class="dottTbl">
                <?php $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
                <?php if ($_Proccess_Has_Ordering_Action) { ?>
                    <td width="70">סדר</td>
                <?php } ?>
                <td width="100">&nbsp;</td>
            </tr>
            <?php for ($count = $resultArr['result']->num_rows, $i = 0; $row = $Db->get_stream($resultArr['result']); $i++) {
                $c = ($i % 2 == 0) ? 'even' : 'odd'; ?>
                <tr class="normTxt <?= $c ?>">
                    <?php $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
                    <?php if ($_Proccess_Has_Ordering_Action) { ?>
                        <td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']); ?></td>
                    <?php } ?>
                    <td class="dottTblS">
                        <?php if (!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)) { ?>
                            <input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>"
                                   onclick="javascript: location.href='?act=new&id=<?php echo $row['id'] . $fwParams; ?>';"/> &nbsp;
                        <?php }
                        if (!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)) { ?>
                            <input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>"
                                   onclick="javascript:doDel(<?php echo $row['id']; ?>, '<?php echo (int)$row['order_num'] . $fwParams; ?>');"/> &nbsp;
                        <?php } ?>
                    </td>
                </tr>
            <?php }
            if ($resultArr['paging']) { ?>
                <tr class="normTxt">
                    <td class="dottTblS"
                        colspan="<?php echo $columns_count + 1 + ($_Proccess_Has_Ordering_Action ? 1 : 0); ?>"
                        align="center"><?php echo $resultArr['paging']; ?></td>
                </tr>
            <?php } ?>
        </table><br/>
        <?php if ($result->num_rows == 0) { ?>
            אין נתונים
        <?php } else if ($_Proccess_Has_Csv_Export) { ?>
            <div class="csv" style="direction: rtl;">
                <a href="?act=show<?= $fwParams; ?>&csv=1">
                    <img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="16" height="16">

                    <?=$_LANG['EXPORT_TO_CSV'];?>
                </a>
            </div>
        <? } ?>
    <?php }
    elseif ($act == "new") { ?>
        <form name="form" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="act" value="after"/>
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
                <tr class="dottTbl">
                    <td colspan="2"><?=$_LANG['TXT_ADD&EDIT'];?></td>
                </tr>
                <?php fields_get_form_fields($fieldsArr, $row, ($obj_id == 0 ? 'add' : 'new'), false);
                if ($_Proccess_Has_MetaTags) { ?>
                    <tr>
                    <td class="dottTblS" colspan="2">
                        <?php
                        $_META_FORM = "form";
                        $_META_TITLE = "title";
                        $_META_DESC = "summary";
                        $lang = "en";
                        include_once($_project_server_path . $_salat_path . "_inc/metaform.inc.php");
                        ?>
                    </td>
                    </tr><?php } ?>
                <tr>
                    <td class="dottTblS" colspan="3">
                        <input type="submit" name="send" value="<?php echo $submit; ?>" class="buttons"
                               onclick="document.getElementById('loader').style.display='';this.style.display='none';"/>
                        <input type="submit" name="stay" value="<?php echo $submitStay; ?>" class="buttons"
                               onclick="document.getElementById('loader').style.display='';this.style.display='none';"/>
                        <div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif"/> מעבד נתונים,
                            נא להמתין . . .
                        </div>
                    </td>
                </tr>
            </table>

        </form>
    <?php } ?>
</div>
</body>
</html>
