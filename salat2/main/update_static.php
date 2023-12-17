<?
error_reporting(E_ALL);
ini_set('display_errors', '1');
/**
 * @author : Gal zalait
 * @version :1.1
 * galzalait@gmail.com
 */
include_once("../_inc/config.inc.php");
// check permissions
include($_project_server_path . $_salat_path . "_static/system-processes-min.inc.php"); // minProcessesArr

include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

$this_file = basename(__FILE__);
$this_dir = basename(dirname(__FILE__));
$_ProcessID = get_module_id(basename(__FILE__), $this_dir); // 05/10/11

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}

if ($langID == '') $langID = $_SESSION['salatLangID'];
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");
include_once($_project_server_path . $_salat_path . "_static/system-processes.inc.php"); // processesArr

$file = $_project_server_path.$_salat_path . '/_static/menus/modulesArr.'.$_SESSION["salatLangID"].'.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$_Proccess_Title = $all_modulesArr[$_ProcessID];

set_time_limit(0);
include_once($_SERVER['DOCUMENT_ROOT'] . '/_static/categoryGroup.inc.php');//$categoryGroupArr
include_once($_project_server_path . $_includes_path . "citywall.array.inc.php");
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . '_static/update_static.inc.php'); // $update_timeArr
include_once($_project_server_path . '_static/moduleUpdateStaticFiles.inc.php'); //$moduleUpdateStaticFilesArr

unset($_SESSION['pages']);
$act = $_REQUEST['act'];

$update_api_documentions = '//' . $_SERVER['HTTP_HOST'] . '/dev/build_doc.php';

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

if ($act == 'update') {
    $className = $_REQUEST['className'];
    $classHolder = new $className();
    $classHolder->updateAllStaticsFiles();
}
$moduleUpdate = new salatModuleUpdateStaticFiles();
if ($act == 'updateAll') {
    echo '<pre style="direction: ltr; text-align: left;">';
    print_r($moduleUpdate->class);
    echo '</pre>';
    foreach ($moduleUpdate->class AS $key => $className) {
        echo $className . ' => ';
        $trimmedClassName = str_replace('UpdateStaticFiles', 'LangsUpdateStaticFiles', $className);
        echo $trimmedClassName . '<br/>';
        if (in_array($trimmedClassName, $moduleUpdate->class)) {
            continue;
        }
        if ($className == 'salatModuleUpdateStaticFiles') {
            continue;
        }
        $tmp = $moduleUpdate->createInstance($className);
        if (strpos('LangsUpdateStaticFiles', $className)) { // if we are dealing with a multi_lang module
            foreach ($languagesArr As $module_lang_id => $value) {
                $tmp->updateAllStaticsFiles();
            }
        } else {
            $tmp->updateAllStaticsFiles();
        }


    }
}
include_once($_project_server_path . $_salat_path . $_includes_path . 'module_info.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html dir="<?= $_LANG['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <link rel="StyleSheet" href="../_public/faq.css" type="text/css">
    <?= $_salat_style; ?>
    <style type="text/css">
        .dottTb2 {
            background-color: orange;
            color: white;
            font-weight: bold;
        }

        .titleTxt {
            text-align: center;
            color: black;
        }

        .bbb {
            font-weight: bold;
        }

        .button.update_api {
            width: auto;
            cursor: pointer
        }

        .buttons {
            width: 60px;
            border: 3px solid white;
        }

        .buttons:hover {
            color: black;
            font-weight: bold;
        }

        .button {
            width: 80px;
            text-align: center;
        }

        .button:hover {
            color: white;
            font-weight: bold;
        }

        .orange {
            background-color: orange;
            color: black;
            border: 1px solid black;
            cursor: pointer;
        }

        .pad {
            padding: 2px;
        }
    </style>
    <script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>
</head>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="titleTxt"><?= $_Proccess_Title; ?></div>
<br/>
<input type="button" class="button red update_api"
       onclick="javascript: var z=window.open('<?= $update_api_documentions; ?>'); z.onbeforeunload = function(){window.location.reload();}"
       value="עדכון דוקמנטציית api"/>
<br/>
<br/>

<div class="button orange" onclick="javascript: location.href='?act=updateAll'">עדכן הכל</div>
<br/>
<table width="100%" border="0" cellpadding="3" cellspacing="1" style="empty-cells:show;" class="table-list">
    <tr class="dottTb1 dottTb2">
        <td width="30">ID</td>
        <td>שם המודול</td>
        <td>מספר קבצים</td>
        <td>פעולות</td>
        <td width="150px;">תאריך עדכון אחרון</td>
    </tr>
    <? foreach ($moduleUpdate->class AS $key => $className) {
        if ($className == 'salatModuleUpdateStaticFiles') {
            continue;
        }
        $tmp = $moduleUpdate->createInstance($className);
        $num = $tmp->getItemsNumber();
        ?>
        <tr class="normTxt">
            <td class="dottTblS"><?= $key; ?></td>
            <td class="dottTblS bbb"><?= ($tmp->name) ? $tmp->name : $className; ?></td>
            <td class="dottTblS"><?= $num ?></td>
            <td class="dottTblS">
                <input type="button" class="buttons pad" value=" עדכן אותי "
                       onclick="javascript: location.href='?act=update&className=<?= $className; ?>'"/> &nbsp;
            </td>
            <td class="dottTblS"> <?= date("j-m-Y", $moduleUpdateStaticFilesArr[$className]); ?></td>
        </tr>
    <? } ?>
</table>
</body>
</html>
