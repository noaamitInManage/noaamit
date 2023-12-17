<?php

if (!in_array($_SERVER['REMOTE_ADDR'], array('207.232.22.164', '62.219.212.139', '81.218.173.175', "37.142.40.96"))) {
    die('אין לך הרשאות למודול הזה');
}

include_once("../_inc/config.inc.php");

$this_dir = basename(dirname(__FILE__));

$_ProcessID = get_module_id(basename(__FILE__), $this_dir); // 05/10/11


if ($langID == '') $langID = $_SESSION['salatLangID'];
include_once($_project_server_path . $_salat_path . "_static/langs/processes/" . $_ProcessID . "." . $langID . ".inc.php");
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query) or die ("error checking user permissions");
if ($Db->get_num_rows($result) == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}

include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$_Proccess_Main_DB_Table = "tb_get_methods";

$_Proccess_Title = "get methods";

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

$_Proccess_Has_MediaLink = false;

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

$apisArr = array(
    'apiManager',
);

$current_api = $_REQUEST['api_name'] ?: $apisArr[0];

$GET_methods = [
    'apiManager' => siteFunctions::get_GET_methodsArr(),
];


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

        $result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
        if ($Db->get_num_rows($result) == 0) {
            $query = "SELECT * FROM {$_Proccess_Main_DB_Table} AS Main
								LEFT JOIN `{$_Proccess_Main_DB_Table}_lang` AS Lang ON  (
									Main.`id`=Lang.`obj_id`
								)
							 WHERE Main.id='{$obj_id}' AND Lang.`lang_id`=" . default_lang_id;
            $result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
        }
        $row = $Db->get_stream($result);
        $submit = "עדכן";
    } else {
        $submit = "הוסף";
    }
    $submitStay = $submit . ' והישאר בעמוד זה';
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
            $query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
            $result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
        }
    } else {
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);
        $newOrder = $Db->mysqli_result($result, 0, 0);

        if ($_Proccess_Has_MultiLangs) {
            multi_lang_insert_query($_Proccess_Main_DB_Table, $fieldsArr, $module_lang_id);

        } else {
            $query = "INSERT INTO {$_Proccess_Main_DB_Table}(" . fields_implode(', ', $fieldsArr) . ") VALUES (" . fields_implode(',', $fieldsArr, $_REQUEST) . ")";
            $result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
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
        //@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/tmpl/'.get_item_dir($obj_id).'/'.$languagesArr[$module_lang_id]['title'].'/tmpl-'.$obj_id.'.inc.php');
    } else {
        $query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        // delete static file !!!
        //@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/tmpl/'.get_item_dir($obj_id).'/tmpl-'.$obj_id.'.inc.php');
    }
    $result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);


    if ($_Proccess_Has_MultiLangs) {
        // check if there are no other translations for item - if not, delete item
        $multiLangSql = "SELECT * FROM `{$_Proccess_Main_DB_Table}_lang` WHERE `obj_id`='{$obj_id}'";
        $multiLangRes = $Db->query($multiLangSql);
        if ($Db->get_num_rows($multiLangRes) == 0) {
            $multiLangSql = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
            $result = $Db->query($multiLangSql) or db_showError(__FILE__, __LINE__, $multiLangSql);
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
    } else {
        $query .= " ORDER BY `id` DESC";
    }
    if (isset($_REQUEST['csv']) && ($_REQUEST['csv'])) {
        query_to_csv($query, $_Proccess_Main_DB_Table . '_log_' . date('d_m_Y') . '.csv', true);
        exit();
    }
    $resultArr = getSqlPagingArr($query);
}

/**-----------------------------------------------------------------------------------------------------------------**/

function module_updateStaticFiles()
{
    global $_Proccess_Main_DB_Table, $_Proccess_Has_MultiLangs;

    if ($_Proccess_Has_MultiLangs) {
    } else {
        $UpdateStatic = new getMethodsUpdateStaticFiles();
        $UpdateStatic->updateStatics();
    }

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
    <script type="text/javascript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script type="text/javascript">
        function doDel(rowID, ordernum) {
            if (confirm("האם למחוק רשומה?")) {
                <?    $lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id=' . $module_lang_id : '';?>
                document.location.href = "?act=del<?=$lang_fw;?>&id=" + rowID + "&order_num=" + ordernum;
            }
        }

        const api_name = '<?= $current_api ?>';

        $(function () {

            /*  auto media load */
            var mediaSelects = $('.media-select');

            var mediaItemId, MediaExt, mediaCategoryId;
            mediaSelects.bind('change', function () {
                $('input#tmp-medium').val($(this).children(':selected').val());
                showImage();
            });

            $(".add-image").live('click', function (event) {
                $(this).parent().find('.media_category_sel').show();
            });

            $("[name=method]").live('click', function (event) {
                var method_name = $(this).val();
                $.get('/salat2/_ajax/ajax.index.php', {
                    'file': 'get_methods',
                    'action': 'check_or_uncheck_method',
                    'method_name': method_name,
                    'api_name': api_name,
                }, function (response) {
                    alert('נוסף בהצלחה');
                });
            });


            $(".media_category_sel").live('change', function (event) {
                var Scope = $(this);

                $.get('/salat2/_ajax/ajax.index.php', {
                    'file': 'auto_complete/media_category',
                    'category': Scope.val(),
                    'action': 'getSelcetItems'
                }, function (response) {
                    Scope.parent().find(".media_items_sel").html(response);
                    Scope.parent().find(".media_items_sel").show();
                });

                Scope.parent().find(".gallery_id").val(Scope.val());
                Scope.parent().find(".image_con").attr("src", "");
                Scope.parent().find(".image_con").attr("rel", "");
                Scope.parent().find(".image_con").removeClass("selected_item");
                Scope.parent().find(".image_con").hide();
            });

            $(".media_items_sel").live('change', function (event) {
                var Scope = $(this);
                var splitStr = $(this).val().split('_');
                mediaCategoryId = splitStr[0];
                mediaItemId = splitStr[1];
                MediaExt = splitStr[2];

                if ($(this).val()) {
                    Scope.parent().find(".image_con").attr("src", "/_media/media/" + mediaCategoryId + '/' + mediaItemId + '.' + MediaExt);
                    Scope.parent().find(".image_con").attr("rel", mediaItemId);
                    Scope.parent().find(".image_con").show();
                } else {
                    Scope.parent().find(".image_con").hide();
                    Scope.parent().find(".image_con").attr("src", "");
                    Scope.parent().find(".image_con").attr("rel", "");
                }
            });

            $(".image_con").live('click', function (event) {
                var Scope = $(this);
                $(this).toggleClass('selected_item');
                if ($(this).hasClass('selected_item')) {
                    Scope.parent().find(".main_media").val(Scope.attr("rel"));
                }

            });

            /* end of  auto media load */
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

        .apis-buttons {
            background: linear-gradient(rgb(76, 119, 168) 0%, rgb(38, 96, 162) 100%) !important;
            padding: 15px 45px;
            font-size: 22px;
            text-decoration: none;
            color: white;
            border: 1px solid rgba(0,0,0,0.21);
            border-bottom: 4px solid rgba(0,0,0,0.21);
            border-radius: 4px;
            text-shadow: 0 1px 0 rgb(0 0 0 / 15%);
            margin: 5px;
            cursor: pointer;
        }

        .api-button-selected {
            background: linear-gradient(rgb(58, 89, 124) 0%, rgb(28, 71, 119) 100%) !important;
        }

        /* end of  auto media load */
    </style>
</head>
<?php echo $_salat_style; ?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?php echo $_Proccess_Title; ?></div>
<div class="maindiv">
    <br/>
    <?php
    foreach ($apisArr as $api_name) { ?>
        <input type="button" class="apis-buttons <?=($api_name == $current_api) ? 'api-button-selected' : ''?>" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>&api_name=<?=$api_name?>';" value="<?=$api_name?>" />
    <?php } ?>
    <?php
    $api_methods = get_class_methods($current_api);
    if (!empty($api_methods)) { ?>
        <table width="30%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
        <tr class="dottTbl">
            <td>אינדקס במערך</td>
            <td>מתודה</td>
            <td>פעיל</td>
        </tr>

        <?php foreach ($api_methods as $index => $value) {
            $c = ($index % 2 == 0) ? 'even' : 'odd'; ?>
            <tr class="normTxt <?= $c ?>">
                <td class="dottTblS">
                    <?= $index ?>
                </td>
                <td class="dottTblS" style="direction: ltr; text-align: center">
                    <?= $value ?>
                </td>
                <td class="dottTblS">
                    <input type="checkbox" name="method" value="<?= $value ?>" <?= (in_array($value, $GET_methods[$current_api])) ? 'checked' : '' ?>/>
                </td>
            </tr>
        <?php } ?>
            </table>
            <br/>
    <?php } else { ?>
        אין נתונים
    <?php } ?>
</div>
</body>
</html>
