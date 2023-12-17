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

include($_project_server_path . $_includes_path . "modules.array.inc.php");
include($_project_server_path . $_includes_path . "site.array.inc.php");
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
require_once($_project_server_path . $_salat_path . $this_dir . "/modules_fields/" . str_replace('.php', '', basename(__FILE__)) . ".fields.inc.php");

include_once($_SERVER['DOCUMENT_ROOT'] . '/_static/sub-sites.inc.php'); // $subSitesArr

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
$_Proccess_Make_Excel = false;

$_Proccess_Main_DB_Table = "tb_sub_sites";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = false;

$_Proccess_Has_GenricSearch = false;

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
$_Proccess_FW_Params = array();

$_yesNo_arr = array(
    '0' => 'לא',
    '1' => 'כן',
);


if ($_Proccess_Make_Excel) {
    /* load fiels */
    include($_project_server_path.'salat2/resources/php-excel/1.8.1/PHPExcel.php');
    include($_project_server_path . "salat2/cs/excelReport.class.inc.php");//excelReport
}

$dir = $_project_server_path . $_media_path . str_replace('.php', '', basename(__FILE__)) . '/';

if ($_Proccess_Has_MultiLangs) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/table_fields.inc.php');
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

if ($act == 'new' && 0) {
    if ($obj_id) {
        $query = "SELECT * FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        $result = $Db->query($query);
        $row = $Db->get_stream($result);
        $submit = "עדכן";
    } else {
        $submit = "הוסף";
    }
    $submitStay = $submit . ' and stay in this page';
} elseif ($act == 'move') {
    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header("Location: ?act=show" . $fwParams);
    exit();
} elseif ($act == 'sync-files') {

    syncSubSite($obj_id);

    header("Location: ?act=show" . $fwParams);
    exit();
} elseif ($act == 'sync-db') {

    if ($obj_id > 0) {
        $subSite = $subSitesArr[$obj_id];
        $objSyncDBStructure = new SyncDBStructure(array(
            'host' => 'localhost',
            'user' => 'root',
            'password' => 'inmanage',
            'name' => 'c14_goproject',
        ), array(
            'host' => 'localhost',
            'user' => $subSite['db_username'],
            'password' => $subSite['db_password'],
            'name' => $subSite['db_name'],
        ));
        $objSyncDBStructure->Sync();
    } else {
        foreach ($subSitesArr as $subSite) {
            $objSyncDBStructure = new SyncDBStructure(array(
                'host' => 'localhost',
                'user' => 'root',
                'password' => 'inmanage',
                'name' => 'c14_goproject',
            ), array(
                'host' => 'localhost',
                'user' => $subSite['db_username'],
                'password' => $subSite['db_password'],
                'name' => $subSite['db_name'],
            ));
            $objSyncDBStructure->Sync();
            unset($objSyncDBStructure);
        }
    }

    header("Location: ?act=show" . $fwParams);
    exit();
} elseif ($act == 'after' && 0) {
    if ($obj_id) {
        $query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
        $result = $Db->query($query);
        $_REQUEST['inner_id'] = $obj_id;
        if ($_Proccess_Has_MultiLangs) {
            td_Update($_Proccess_Main_DB_Table, $_REQUEST['inner_id']);
        }
    } else {
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);

        $query = "INSERT INTO {$_Proccess_Main_DB_Table}(" . fields_implode(', ', $fieldsArr) . ") VALUES (" . fields_implode(',', $fieldsArr, $_REQUEST) . ")";
        $result = $Db->query($query);
        $_REQUEST['inner_id'] = $Db->get_insert_id();
        if ($_Proccess_Has_MultiLangs) {
            td_Insert($_Proccess_Main_DB_Table, $_REQUEST['inner_id']);
        }
        if ($_Proccess_Has_Ordering_Action) {
            setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
        }
    }

    if ($_Proccess_Has_MetaTags) {
        include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
    }

    module_updateStaticFiles();

    if (!empty($_REQUEST['stay'])) {
        header("Location: ?act=new&id=" . $_REQUEST['inner_id'] . $fwParams);
    } else {
        header("Location: ?act=show" . $fwParams);
    }
    exit();
} elseif ($act == "del") {
    $query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
    $result = $Db->query($query);
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

    if ($_Proccess_Has_GenricSearch) {
        genric_searchable_items($_ProcessID, $whereArr);
    }

    if ($_REQUEST['topcat_id'] > 0) {
        $whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";
    }

    $where = (count($whereArr) > 0 ? " WHERE " . implode(' AND ', $whereArr) : "");
    $query = "SELECT * FROM {$_Proccess_Main_DB_Table} {$where}";
    if (array_key_exists($_REQUEST['orderby'], $fieldsArr)) {
        $query .= " ORDER BY `{$_REQUEST['orderby']}`";
        if ($_REQUEST['ordertype']) {
            $query .= ' ' . $Db->make_escape($_REQUEST['ordertype']);
        }
    } elseif ($_Proccess_Has_Ordering_Action) {
        $query .= " ORDER BY order_num";
    }
    $resultArr = getSqlPagingArr($query);
}

function module_updateStaticFiles()
{
    global $_Proccess_Main_DB_Table;

    /*
        $UpdateStatic = new contentUpdateStaticFiles();
        $UpdateStatic->updateStatics();*/
}

function syncSubSite($subSiteID)
{
    global $subSitesArr;

    $zipFilePath = zipFiles();

    if (key_exists($subSiteID, $subSitesArr)) {
        $domainName = $subSitesArr[$subSiteID]['domain'];
        $remotePath = "http://www.{$domainName}/sync/upload.php";

        $response = uploadZipFile($zipFilePath, $remotePath);
        echo '<pre>';
        print_r($zipFilePath);
        echo '</pre>';
        echo '<pre>[';
        print_r($response);
        echo ']</pre>';
        exit();
    } else {
        foreach ($subSitesArr as $subSite) {
            $domainName = $subSite['domain'];
            $remotePath = "http://www.{$domainName}/sync/upload.php";
            $response = uploadZipFile($zipFilePath, $remotePath);
        }
    }
}

function uploadZipFile($zipFilePath, $remotePath)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $remotePath);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'zipfile' => '@' . $zipFilePath,
    ));
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function zipFiles()
{
    set_time_limit(3600000);

    // Realtive to
    $relativeTo = "/var/www/clients/client0/web17/web/";

    // This array tells the script which files to backup
    $filesToBackupFromRoot = array();

    // Usage: Dir => array(Search Pattern, Recursive)
    $dirsToBackupFromRoot = array(
        '_inc' => array("*.php,*.js,*.html,*.css", true),
        '_static' => array("*.php,*.js,*.html,*.css", true),
        '_modules' => array("*.php,*.js,*.html,*.css", true),
    );

    // All of the dirs / files to be excluded
    $dirsToExclude = array();

    // All of the dirs / files to not be excluded
    $dirsToExcludeOverride = array();

    // Basic configuration
    $backupName = date("Y-m-d_H-i-s") . '.zip';
    $backupDir = $relativeTo . "_static/sync";

    // Execute backup
    $serverBackup = new serverBackup($backupDir, $backupName);
    $serverBackup->performBackupFromArrRoot($filesToBackupFromRoot, $dirsToBackupFromRoot, $dirsToExclude, $dirsToExcludeOverride, $relativeTo);

    chmod($backupDir . '/' . $backupName, 0777);

    return $_SERVER['DOCUMENT_ROOT'] . '/_static/sync/' . $backupName;
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
    <script type="text/javascript" src="../_public/jquery1.6.4.min.js"></script>
    <script type="text/javascript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script type="text/javascript">
        function doDel(rowID, ordernum) {
            if (confirm("<?=$_LANG['DEL_MESSAGE'];?>")) {
                document.location.href = "?act=del&id=" + rowID + "&order_num=" + ordernum;
            }
        }

        function doSyncFiles(rowID) {
            if (confirm("האם לסנכרן קבצים לדומיין זה?")) {
                document.location.href = "?act=sync-files&id=" + rowID;
            }
        }

        function doSyncDB(rowID) {
            if (confirm("האם לסנכרן מבנה DB לדומיין זה?")) {
                document.location.href = "?act=sync-db&id=" + rowID;
            }
        }

        function get_excel_file(report) {
            $("#black-layer").show();
            $.post("/salat2/_ajax/ajax.index.php", '&file=excel_web_service&report_name=' + report, function (result) {
                if (result.err != "") {
                    alert(result.err);

                } else {
                    if (result.html) {
                        $("#black-layer").append(result.html);
                    }
                }
                $("#black-layer").hide();
            }, "json");
        }

        function get_excel_file_item(report, id) {
            $("#black-layer").show();
            $.post("/salat2/_ajax/ajax.index.php", '&file=excel_web_service&tp=' + id, function (result) {
                if (result.err != "") {
                    alert(result.err);

                } else {
                    if (result.html) {
                        $("#black-layer").append(result.html);
                    }
                }
                $("#black-layer").hide();
            }, "json");
        }
    </script>
    <style type="text/css">
        .normTxt.hover td {
            background: wheat;
        }

        .excel {
            display: inline;
            cursor: pointer;
            width: auto;
            height: auto;
            float: left;
        }
    </style>
</head>
<?php echo $_salat_style; ?>
<body>    <? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?php echo $_Proccess_Title; ?></div>
<? /* 
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_SHOW_ALL'];?>" />
<input type="button" class="buttons" onclick="javascript: location.href='?act=new<?php echo $fwParams;?>';" value="<?=$_LANG['BTN_ADD_NEW'];?>" />
*/ ?>
<input type="button" class="buttons" onclick="javascript: location.href='?act=sync-db<?php echo $fwParams; ?>';"
       value="סנכרון מבנה DB" style="color: orange;"/>
&nbsp;&nbsp;
<input type="button" class="buttons" onclick="javascript: location.href='?act=sync-files<?php echo $fwParams; ?>';"
       value="סנכרון קבצים" style="color: green;"/>
<? if ($_Proccess_Make_Excel) { ?>
    <div class="excel" onclick="get_excel_file('contact');">
        <img src="../_public/Excel-icon.png" alt="אקסל" title="אקסל" width="32" height="32"/>
        <br/>
        <div class="excel-text"><strong>דו"ח כללי</strong></div>
    </div>
<? } ?>
<div class="maindiv">

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
    <br/>

    <?php if ($act == "show") { ?>
        <table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
            <tr class="dottTbl">
                <?php $columns_count = fields_get_show_heads_fields($fieldsArr, false); ?>
                <?php if ($_Proccess_Has_Ordering_Action) { ?>
                    <td width="70">סדר</td>
                <?php } ?>
                <td width="150">&nbsp;</td>
            </tr>
            <?php for ($count = $resultArr['result']->num_rows, $i = 0; $row = $Db->get_stream($resultArr['result']); $i++) { ?>
                <tr class="normTxt" onmouseover="this.className='normTxt hover';"
                    onmouseout="this.className='normTxt';">
                    <?php $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
                    <?php if ($_Proccess_Has_Ordering_Action) { ?>
                        <td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']); ?></td>
                    <?php } ?>
                    <td class="dottTblS">
                        <?php if (0 && !in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)) { ?>
                            <input type="button" class="buttons" value="<?=$_LANG['BTN_EDIT'];?>"
                                   onclick="javascript: location.href='?act=new&id=<?php echo $row['id'] . $fwParams; ?>';"/> &nbsp;
                        <?php }
                        if (0 && !in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)) { ?>
                            <input type="button" class="buttons red" value="<?=$_LANG['BTN_DEL'];?>"
                                   onclick="javascript:doDel(<?php echo $row['id']; ?>, '<?php echo (int)$row['order_num'] . $fwParams; ?>');"/> &nbsp;
                        <?php } ?>
                        <input type="button" class="buttons" style="color: green;" value="סנכרן קבצים"
                               onclick="javascript:doSyncFiles('<?php echo $row['id']; ?>');"/> &nbsp;
                        <input type="button" class="buttons" style="color: orange;" value="סנכרן DB"
                               onclick="javascript:doSyncDB('<?php echo $row['id']; ?>');"/> &nbsp;
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
        <?php } ?>
    <?php } elseif ($act == "new") { ?>
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
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/black_layer.inc.php'); ?>

</body>
</html>