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
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
//require_once($_project_server_path.$_salat_path.$this_dir."/modules_fields/".str_replace('.php', '', basename(__FILE__)).".fields.inc.php");
include($_project_server_path . $_salat_path . $_includes_path . 'moduleUpdateStaticFiles.php'); // 15/04/2011
include($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

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

?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <link rel="StyleSheet" href="../_public/colorpicker.css" type="text/css">
    <script type="text/javascript"
            src="../htmleditor/ckeditor/ckeditor.js<?= ($act != 'show') ? "?t=" . time() : ""; ?>"></script>
    <script type="text/javascript" src="../_public/datetimepicker.js"></script>
    <script type="text/javascript" src="../_public/jquery1.8.min.js"></script>
    <script type="text/javascript" src="../_public/colorpicker.min.js"></script>
    <script type="text/javascript" src="../_public/media_select.js"></script>
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


            /*  auto media load */
            var mediaSelects = $('.media-select');

            var mediaItemId, MediaExt, mediaCategoryId;
            mediaSelects.bind('change', function () {
                $('input#tmp-medium').val($(this).children(':selected').val());
                showImage();
            });

            $("#add-image").live('click', function (event) {
                $("#media_category_sel").show();
            });


            $("#media_category_sel").live('change', function (event) {
                var scope = $(this);

                $.get('/salat2/_ajax/ajax.index.php', {
                    'token': '<?=$_token?>',
                    'file': 'auto_complete/media_category',
                    'category': scope.val(),
                    'action': 'getSelcetItems'
                }, function (response) {
                    $("#media_items_sel").html(response);
                    $("#media_items_sel").show();
                });

                $("#gallery_id").val($(this).val());
                $("#image_con").attr("src", "");
                $("#image_con").attr("rel", "");
                $("#image_con").removeClass("selected_item");
                $("#image_con").hide();
            });

            $("#media_items_sel").live('change', function (event) {
                var splitStr = $(this).val().split('_');
                mediaCategoryId = splitStr[0];
                mediaItemId = splitStr[1];
                MediaExt = splitStr[2];

                if ($(this).val()) {
                    $("#image_con").attr("src", "/_media/media/" + mediaCategoryId + '/' + mediaItemId + '.' + MediaExt);
                    $("#image_con").attr("rel", mediaItemId);
                    $("#image_con").show();
                } else {
                    $("#image_con").hide();
                    $("#image_con").attr("src", "");
                    $("#image_con").attr("rel", "");
                }
            });

            $("#image_con").live('click', function (event) {

                $(this).toggleClass('selected_item');

                if ($(this).hasClass('selected_item')) {

                    $("#main_media").val($("#image_con").attr("rel"));
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
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php echo $fwParams; ?>';"
       value="הצג הכל"/>
<div class="maindiv">
    <br/>
    <div id="search_frm" style="text-align:center; width:100%;">
        <div style="width:40%; margin: 0 auto;">
            <div class="buttons" id="sync" onclick="window.open('/_crons/rsyncPhp/syncer.php'); $('#msg').show(); ">בצע
                סנכרון אתרים
            </div>
            <div id="msg">הסנכרון בוצע בהצלחה</div>
        </div>
    </div>
</div>
</body>
</html>
