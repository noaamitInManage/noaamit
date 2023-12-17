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
//require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_salat_path . '_inc/db_syncArr.php'); //$db_informationArr
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_MetaTags = true;

$_Proccess_Has_MultiLangs = true;

$_Proccess_Has_GenricSearch = true;
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
$_Proccess_HC_RowsID_Arr_NOT_DELETABLE = array(59, 60, 61, 63, 64, 85);

/**
 * Array of forwarded parameters from $_REQUEST
 */
$_Proccess_FW_Params = array('lang_id', 'token');

$_yesNo_arr = array(
    '0' => 'לא',
    '1' => 'כן',
);

real_escape_request(); // for sql injection

$dir = $_project_server_path . $_media_path . str_replace('.php', '', basename(__FILE__)) . '/';

if ($_Proccess_Has_MultiLangs) {
    //include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/table_fields.inc.php');
}

$fwParams = array();
foreach ($_Proccess_FW_Params as $param) {
    $fwParams[] = $param . '=' . $_REQUEST[$param];
}

$fwParams = htmlspecialchars('&' . implode('&', $fwParams) . '&token=' . $_token); // for token security

define("_PAGING_NumOfItems", 25);    // number of rows in page
define("_PAGING_NumOfLinks", 5);    // number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template", '<a href="?pagenum={PAGENUM}' . $fwParams . '">{CONTENT}</a>');

$act = $_REQUEST['act'];
if ($act == '') $act = "show";

function connectedDB($db_informationArr)
{
    $db_connectionArr = array();
    foreach ($db_informationArr AS $server_name => $infoArr) {
        $db_connectionArr[$infoArr['server_name']] = mysqli_connect($infoArr['host'], $infoArr['user'], $infoArr['pass'], $infoArr['db']);
    }

    return $db_connectionArr;
}

function check_col_type($main_conn, $sec_conn, $table_name)
{
    $Db = Database::getInstance();

    $query = "SHOW COLUMNS FROM `{$table_name}`";
    $res = $Db->query($query, $main_conn);
    while ($line = $Db->get_stream($res)) {
        $query = "SHOW COLUMNS FROM  `{$table_name}` WHERE Field =  '{$line['Field']}'";
        $res1 = $Db->query($query, $sec_conn);
        if ($res1->num_rows > 0) {
            $field = $Db->get_stream($res1);
            if ($line['Type'] != $field['Type']) {
                return false;
            }
        }
    }

    return true;
}

?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <link rel="StyleSheet" href="../_public/colorpicker.css" type="text/css">
    <link rel="StyleSheet" href="../../_media/js/plugins/DataTables-1.10.1/media/css/jquery.dataTables.css"
          type="text/css">
    <script type="text/javascript"
            src="../htmleditor/ckeditor/ckeditor.js<?= ($act != 'show') ? "?t=" . time() : ""; ?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
    <script type="text/javascript" src="../_public/colorpicker.min.js"></script>
    <script type="text/javascript" src="../_public/media_select.js"></script>
    <script type="text/javascript"
            src="../../_media/js/plugins/DataTables-1.10.1/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript"> if (window.parent == window) location.href = '../frames.php?token=<?=$_token?>'; </script>
    <script type="text/javascript">
        function doDel(rowID, ordernum) {
            if (confirm("<?=$_LANG['DEL_MESSAGE'];?>")) {
                <?    $lang_fw = ($_Proccess_Has_MultiLangs) ? '&lang_id=' . $module_lang_id : '';?>
                document.location.href = "?act=del<?=$lang_fw;?>&id=" + rowID + "&order_num=" + ordernum + "&token=<?=$_token?>";
            }
        }

        $(function () {
            // color picker default and initialization
            $('#colorSelection > div').css('backgroundColor', '#' + $('#colorSelection > input:hidden').val());
            $('#colorSelection').ColorPicker({
                color: '#' + $('#colorSelection > input:hidden').val(),
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    $('#colorSelection > div').css('backgroundColor', '#' + hex);
                    $('#colorSelection > input:hidden').val(hex);
                }
            });

            $('body').on('click', '.show_columns', function () {
                $.post("/salat2/_ajax/ajax.index.php", '&file=db_sync&act=draw_table_col&table_name=' + $(this).attr('table_name'), function (result) {
                    if (result.html != "") {
                        $('.table_col').html(result.html);
                        goToByScroll("table_col");
                    }
                }, "json");
            });

            $('body').on('click', '.sync_db', function () {
                var flag = true;
                var html = '';
                var line_num = $(this).attr('line_num');
                var main_server_name = $(this).attr('main_server_name');
                var flag_one = 1;
                $.each($(this).parent().parent().find('.sync_table'), function (key, val) {
                    var table_name = $(this).attr('table_name');
                    var sec_server_name = $(this).attr('server_name');
                    $.post("/salat2/_ajax/ajax.index.php", '&file=db_sync&act=sync_table&main_server_name=' + main_server_name + '&sec_server_name=' + sec_server_name + '&table_name=' + table_name, function (result) {
                        if (result.msg == "OK") {
                        } else {
                            flag_one = 0;
                        }
                    }, "json");
                });

                $.each($(this).parent().parent().find('.create_table'), function (key, val) {
                    var table_name = $(this).attr('table_name');
                    var sec_server_name = $(this).attr('server_name');
                    $.post("/salat2/_ajax/ajax.index.php", '&file=db_sync&act=create_table&main_server_name=' + main_server_name + '&sec_server_name=' + sec_server_name + '&table_name=' + table_name, function (result) {
                        if (result.msg == "OK") {
                            html = result.html;
                        } else {
                            flag_one = 0;
                        }
                    }, "json");
                });
                if (flag_one) {
                    flag = true;
                } else {
                    flag = false;
                }

                if (flag) {
                    //If change table can take some sec to finish the process
                    var action = setTimeout(function () {
                        $('#search_frm').html('<p style="font-size: 20px; color: green; font-weight: bold;">הסנכרון התבצע בהצלחה!</p><br/><input type="button" class="reloadAgain" value="טען מחדש" />');
                    }, 2000);

                    //window.clearTimeout(action);
                } else {
                    alert("הסנכרון לא התבצע או התבצע חלקי!");
                }
            });


            $('body').on('click', '.reloadAgain', function () {
                location.reload();
            });

            $(document).ready(function () {
                $('table.display').dataTable({
                    "iDisplayLength": 250
                });
            });
        });

        function goToByScroll(id) {
            // Remove "link" from the ID
            id = id.replace("link", "");
            // Scroll
            $('html,body').animate({
                    scrollTop: $("#" + id).offset().top
                },
                'fast');
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
            border: 1px solid red;
        }

        /* end of  auto media load */

        #colorSelection {
            position: relative;
            width: 36px;
            height: 36px;
            background: url(../_public/colorpicker_images/select.png);
        }

        #colorSelection > div {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 30px;
            height: 30px;
            background: url(../_public/colorpicker_images/select.png) center;
        }

        .buttons {
            font-size: 18px !important;
        }

        #msg {
            display: none;
        }

    </style>
</head>
<?php echo $_salat_style; ?>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?php echo $_Proccess_Title; ?></div>
<div class="maindiv">
    <br/>
    <div id="search_frm" style="text-align:center; width:100%;">
        <div style="width:70%; margin: 0 auto;">
            <h3 align="right"><b><u>Legend</u></b></h3>
            <label style="margin-right: 15px; float: right;">Good</label>
            <div style="width: 10px; height: 10px; background-color: lightgreen;"></div>
            <label style="margin-right: -30px; margin-top: 10px; float: right;">Column missing / Table Missing</label>
            <div style="width: 10px; height: 10px; margin-top: 10px; background-color: #f08080;"></div>
            <label style="float: right; margin-top:10px; margin-right: -172px;">Colums type diffrent</label>
            <div style="width: 10px; margin-top:10px; height: 10px; background-color: #EDEF7D;"></div>
        </div>
        <br>
        <hr width="70%"/>
        <div style="width:70%; margin: 0 auto; padding-top: 30px;">
            <? $db_connectionsArr = connectedDB($db_informationArr); ?>
            <? $row_num = 1; ?>
            <table id="" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Num</th>
                    <th>Tables Name</th>
                    <? foreach ($db_connectionsArr AS $server_name => $conn) { ?>
                        <th><?= $server_name ?></th>
                    <? } ?>
                    <th>Action</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Num</th>
                    <th>Tables Name</th>
                    <? foreach ($db_connectionsArr AS $server_name => $conn) { ?>
                        <th><?= $server_name ?></th>
                    <? } ?>
                    <th>Action</th>
                </tr>
                </tfoot>
                <tbody>
                <?
                $main_server = reset($db_connectionsArr);
                $res = $Db->query("show tables", $main_server);
                $line_num = 1;
                while ($line = $Db->get_stream_array($res)) {
                    ?>
                    <tr align="center">
                        <td><?= $row_num; ?></td>
                        <? $row_num++; ?>
                        <td><?= $line[0] ?></td>
                        <?
                        $index = 1;
                        $show_sync_bt = true;
                        ?>
                        <? foreach ($db_connectionsArr AS $server_name => $conn) {
                            ?>
                            <?
                            $num_of_col = 0;
                            $query = "SHOW TABLES LIKE '{$line[0]}'";
                            $res_is_exist = $Db->query($query, $conn);
                            if($res_is_exist->num_rows > 0){
                                $query = "SHOW COLUMNS FROM `{$line[0]}`";
                                $res1 = $Db->query($query, $conn);
                                ?>
                                <? $num_of_col = $res1 ? $res1->num_rows : 0; ?>
                            <?}?>

                            <? if ($index == 1) {
                                ?>
                                <td>
                                    <?= $num_of_col; ?>
                                    <input type="button" value="show columns" table_name="<?= $line[0] ?>"
                                           class="show_columns">
                                </td>
                                <?
                                $main_table_num_col = $res1->num_rows;
                                $main_server_name = $server_name;
                                $main_conn = $conn;
                                ?>
                            <? } else {
                                if ($num_of_col == 0 || $num_of_col != $main_table_num_col) {
                                    ?>
                                    <td style="background-color: #f08080;">
                                        <?= $num_of_col; ?>
                                    </td>
                                <? } elseif (!check_col_type($main_conn, $conn, $line[0])) {
                                    ?>
                                    <td style="background-color: #EDEF7D;">
                                        <?= $num_of_col; ?>
                                    </td>
                                <? } else {
                                    ?>
                                    <td style="background-color: lightgreen;">
                                        <?= $num_of_col; ?>
                                    </td>
                                <? } ?>
                            <? } ?>
                            <? if ($num_of_col == 0) {
                                ?>
                                <input type="hidden" class="create_table" server_name="<?= $server_name ?>"
                                       table_name="<?= $line[0] ?>"/>
                                <? $show_sync_bt = false; ?>
                            <? } elseif ($main_table_num_col != $num_of_col || !check_col_type($main_conn, $conn, $line[0])) {
                                ?>
                                <? $show_sync_bt = false; ?>
                                <input type="hidden" class="sync_table" server_name="<?= $server_name ?>"
                                       table_name="<?= $line[0] ?>"/>
                            <? } ?>
                            <? $index++; ?>
                        <? } ?>
                        <td>
                            <? if (!$show_sync_bt) {
                                ?>
                                <input type="button" value="sync" main_server_name="<?= $main_server_name; ?>"
                                       line_num="<?= $line_num ?>" id="sync_db_<?= $line_num; ?>" class="sync_db"/>
                            <? } ?>
                        </td>
                        <? $line_num++; ?>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
        <div style="width:70%; margin: 0 auto; margin-top: 50px;" id="table_col" class="table_col">
        </div>
    </div>
</div>
</body>
</html>
