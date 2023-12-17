<?php

use Feed\FeedManager;
use Feed\Items\Event;

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
include_once($_project_server_path."index.class.include.php");  // load class index

$_Proccess_Main_DB_Table = "tb_events";

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
    '0' => 'No',
    '1' => 'Yes',
);

$sites_sql = "
    SELECT Main.`id`, Lang.`name` FROM `tb_sites` AS Main
      LEFT JOIN `tb_sites_lang` AS Lang ON (Lang.`obj_id` = Main.`id` AND Lang.`lang_id` = {$_SESSION['lang_id']})
    WHERE Main.`active` = 1
    ORDER BY `order_num`
";
$sites_result = $Db->query($sites_sql);
$sitesArr = array();
while ($siteArr = $Db->get_stream($sites_result)) {
    $sitesArr[$siteArr['id']] = $siteArr['name'];
}

$timezonesArr = siteFunctions::get_timezonesArr(true);

function get_site_timezone()
{
    global $Db;

    $site_id = $_REQUEST['site_id'];

    $sql = "
        SELECT `timezone` FROM `tb_sites` WHERE `id` = {$site_id}    
    ";
    $result = $Db->query($sql);
    $rowArr = $Db->get_stream($result);

    return $rowArr['timezone'];
}

function get_original_site_id()
{

}

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

if ($act == 'new') {
    if ($obj_id) {
        $query = "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";

        if ($_Proccess_Has_MultiLangs) {
            $query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`='{$module_lang_id}'";
        }

        $result = $Db->query($query);
        if ($result->num_rows == 0) {
            $query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`=" . default_lang_id;
            $result = $Db->query($query);
        }
        $row = $Db->get_stream($result);
        $submit = $_LANG['BTN_UPDATE'];
    } else {
        $submit = $_LANG['BTN_ADD'];
    }
    $submitStay = $submit . " {$_LANG['AND_STAY']}" ;
} elseif ($act == 'move') {
    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header("Location: ?act=show" . $fwParams);
    exit();
} elseif ($act == 'after') {
//die('<hr /><pre>' . print_r($_REQUEST, true) . '</pre><hr />');

    if ($obj_id) {  // ### UPDATE QUERY ###

        $_REQUEST['inner_id'] = $obj_id;
        if ($_Proccess_Has_MultiLangs) {
            multi_lang_update_query($_Proccess_Main_DB_Table, $_REQUEST['inner_id'], $fieldsArr, $module_lang_id);
        } else {
            if ($_REQUEST['site_id']) {
                $fi_sql = "
                    SELECT
                      Activity.`foreign_id`, Activity.`object`, Event.`site_id`
                    FROM `tb_events` AS Event
                      LEFT JOIN `tb_feed__activities` AS Activity ON Activity.`foreign_id` = Event.`activity_foreign_id`
                    WHERE Event.`id` = {$obj_id}
                ";
                $fi_res = $Db->query($fi_sql);
                $fi_rowArr = $Db->get_stream($fi_res);
                $activity_foreign_id = $fi_rowArr['foreign_id'];
                $site_id = $fi_rowArr['site_id'];
                $object = $fi_rowArr['object'];

                if ($site_id != $_REQUEST['site_id']) {
                    $Event = new Event($activity_foreign_id);
                    $FeedManager = FeedManager::getInstance();
                    $FeedManager->delete_from_feed('site', $site_id, $activity_foreign_id, true);

                    $Event->add_copy('site', $_REQUEST['site_id'])->publish();
                }
            }

            $query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
            $result = $Db->query($query);
        }
    } else {
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);

        if ($_Proccess_Has_MultiLangs) {
            multi_lang_insert_query($_Proccess_Main_DB_Table, $fieldsArr, $module_lang_id);

        } else {
            error_reporting(E_ERROR);
            ini_set('display_errors','1');
            $query = "INSERT INTO {$_Proccess_Main_DB_Table}(" . fields_implode(', ', $fieldsArr) . ") VALUES (" . fields_implode(',', $fieldsArr, $_REQUEST) . ")";
            $result = $Db->query($query);
            $_REQUEST['inner_id'] = $Db->get_insert_id();

            if ($_REQUEST['site_id']) {
                $Event = new Event();
                $Event->set_event_id($_REQUEST['inner_id'])
                    ->set_publish_ts(time())
                    ->add_copy('site', $_REQUEST['site_id'])
                    ->publish();
            }
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

    if (!empty($_REQUEST['stay'])) {
        header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
    } else {
        header("Location: ?act=show" . $fwParams);
    }
    exit();
} elseif ($act == "del") {

    if ($_Proccess_Has_MultiLangs) {
        $query = "DELETE FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}' AND `lang_id`='{$module_lang_id}'";
        // delete language static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/tmpl/' . get_item_dir($obj_id) . '/' . $languagesArr[$module_lang_id]['title'] . '/tmpl-' . $obj_id . '.inc.php');
    } else {
        $query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        // delete static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/tmpl/' . get_item_dir($obj_id) . '/tmpl-' . $obj_id . '.inc.php');
    }

    $fi_sql = "
        SELECT
          Activity.`foreign_id`, Activity.`object`, Event.`site_id`
        FROM `tb_events` AS Event
          LEFT JOIN `tb_feed__activities` AS Activity ON Activity.`foreign_id` = Event.`activity_foreign_id`
        WHERE Event.`id` = {$obj_id}
    ";
    $fi_res = $Db->query($fi_sql);
    $fi_rowArr = $Db->get_stream($fi_res);
    $activity_foreign_id = $fi_rowArr['foreign_id'];
    $site_id = $fi_rowArr['site_id'];
    $object = $fi_rowArr['object'];

    $Event = new Event($activity_foreign_id);
    $Event->delete();

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
} else {
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

    $whereArr[] = "bizzabo_id=0";


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
    $UpdateStatic = new eventsUpdateStaticFiles();
    $UpdateStatic->updateStatics();
}

include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <script type="text/javascript"
            src="../htmleditor/ckeditor/ckeditor.js<?= ($act != 'show') ? "?t=" . time() : ""; ?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
    <script type="text/javascript" src="../_public/media_select.js"></script>
    <script type="text/javascript" src="/resource/uploadify/jquery.uploadify.v2.1.4.min.js"></script>

    <script type="text/javascript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script type="text/javascript">
        function doDel(rowID, ordernum) {
            if (confirm("Are you sure you want to delete this item?")) {
                <?    $lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id=' . $module_lang_id : '';?>
                document.location.href = "?act=del<?=$lang_fw;?>&id=" + rowID + "&order_num=" + ordernum;
            }
        }

        $(function () {
            $("body").on('click', '#addFloor', function () {
                var site_id = <?=(int)$obj_id?>;
                var floor = $(this).closest('tr').find("input[name='floorArr[floor]']").val();
                var floor_order_num = $(this).closest('tr').find("input[name='floorArr[order_num]']").val();

                var floorArr = {site_id: site_id, floor: floor, order_num: floor_order_num};
                if (floor != '' && site_id > 0) {
                    $.post('/salat2/_ajax/ajax.index.php', {
                        file: 'sites',
                        action: 'add_floor',
                        site_id: site_id,
                        floorArr: floorArr
                    }, function (response) {
                        if (response.err != '') {
                            alert(response.err);
                        } else {
                            $("#floors_order").html('');
                            $("#floors_order").html(response.html);
                            alert(response.msg);
                        }
                    }, 'json');
                }
                else {
                    alert("יש למלא את כל פרטי הקומה");
                }
                return false;
            });

            $("body").on('click', '.updateFloor', function () {
                var site_id = <?=(int)$obj_id?>;
                var floor_id = $(this).closest('tr').data('floor-id');
                var floor = $(this).closest('tr').find("input[name='floorArr[floor]']").val();

                var floorArr = {site_id: site_id, floor_id: floor_id, floor: floor};

                $.post('/salat2/_ajax/ajax.index.php', {
                        'file': 'sites',
                        'action': 'update_floor',
                        'site_id': site_id,
                        'floor_id': floor_id,
                        'floorArr': floorArr
                    },
                    function (response) {
                        if (response.err != '') {
                            alert(response.err);
                        } else {
                            $("#floors_order").html('');
                            $("#floors_order").html(response.html);
                            alert(response.msg);
                        }
                    }, 'json');
            });

            $("body").on('click', '.deleteFloor', function () {
                if (confirm("Are you sure you want to delete this floor?")) {
                    var site_id = <?=(int)$obj_id?>;
                    var floor_id = $(this).closest('tr').data('floor-id');

                    $.post('/salat2/_ajax/ajax.index.php', {
                            'file': 'sites',
                            'action': 'delete_floor',
                            'site_id': site_id,
                            'floor_id': floor_id
                        },
                        function (response) {
                            if (response.err != '') {
                                alert(response.err);
                            } else {
                                console.log(response.html);
                                $("#floors_order").html('');
                                $("#floors_order").html(response.html);
                            }
                        }, 'json');
                }
            });

            $("body").on('click', 'img.order', function () {
                var site_id = <?=(int)$obj_id?>;
                var item_id = $(this).closest('tr').data('item-index');
                var floor_id = $(this).closest('tr').data('floor-id');
                var direction = $(this).hasClass('down') ? 'down' : 'up';
                $.post('/salat2/_ajax/ajax.index.php', {
                        'file': 'sites',
                        'action': 'order_floors',
                        'site_id': site_id,
                        'direction': direction,
                        'item_index': item_id,
                        'floor_id': floor_id
                    },
                    function (response) {
                        if (response.err != '') {
                            alert(response.err);
                        } else {
                            console.log(response.html);
                            $("#floors_order").html('');
                            $("#floors_order").html(response.html);
                        }
                    }, 'json');
            });
        });

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
            border: 1px solid red;
        }

        /* end of  auto media load */
    </style>
</head>
<?php echo $_salat_style; ?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?php echo $_Proccess_Title; ?></div>
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams; ?>';"
       value="<?=$_LANG['BTN_SHOW_ALL'];?>"/>
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams; ?>';"
       value="<?=$_LANG['BTN_ADD_NEW'];?>"/>
<div class="maindiv">
    <br/>
    <? if ($_Proccess_Has_GenricSearch) { ?>
        <br/>
        <div id="search_frm">
            <form action="" method="get" style="display:inline; font-size:12px; margin:0 auto;">
                <input type="hidden" name="act" value="show"/>
                <input type="hidden" name="dosearch" value="search"/>

                <table cellpadding="3" cellspacing="0" align="center">
                    <tr>

                        <?
                        foreach ($fieldsArr AS $key => $fieldArr) {
                            if ($fieldArr['input']['searchable'] == true) {
                                print "<td>" . $fieldArr['title'] . ":";
                                print draw_genric_search($fieldArr, $key) . "</td>";
                            }
                        }
                        ?>

                        <td><input type="submit" value="Search" class="buttons"/></td>
                    </tr>
                </table>
            </form>
        </div>
    <? } ?>
    <? if ($_Proccess_Has_MultiLangs) { ?>
        <?= draw_module_tabs(); ?>
    <? } ?>
    <?php if ($act == "show") { ?>
        <table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
            <tr class="dottTbl">
                <?php $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
                <?php if ($_Proccess_Has_Ordering_Action) { ?>
                    <td width="70">Order</td>
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
            No data
        <?php } else if ($_Proccess_Has_Csv_Export) { ?>
            <div class="csv" style="direction: rtl;">
                <a href="?act=show<?= $fwParams; ?>&csv=1">
                    <img src="../_public/Excel-icon.png" alt="Excel" title="Excel" width="16" height="16">

                    Export to CSV
                </a>
            </div>
        <? } ?>
    <?php } elseif ($act == "new") { ?>
        <form name="form" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="act" value="after"/>
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
                <tr class="dottTbl">
                    <?=$_LANG['TXT_ADD&EDIT'];?>
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
                        <div id="loader" style="display:none;"><img src="/salat2/images/ajax-loader.gif"/> Processing,
                            Please wait . . .
                        </div>
                    </td>
                </tr>
            </table>

        </form>
    <?php } ?>
</div>
</body>
</html>
