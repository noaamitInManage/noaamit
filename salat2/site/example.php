<?php
/**
 * Created by PhpStorm.
 * User: xenia
 * Date: 22/09/14
 * Time: 11:25
 */

include_once("../_inc/config.inc.php");

$this_dir = basename(dirname(__FILE__));

$_ProcessID = get_module_id(basename(__FILE__), $this_dir); // 05/10/11

if ($langID == '') $langID = $_SESSION['salatLangID'];
include_once($_project_server_path . $_salat_path . "_static/langs/processes/" . $_ProcessID . "." . $langID . ".inc.php");
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");

$file = $_project_server_path . $_salat_path . '/_static/menus/modulesArr.' . $_SESSION["salatLangID"] . '.inc.php';
$all_modulesArr = unserialize(file_get_contents($file));

$langSalat = ($_REQUEST['lang_id']) ? $languagesArr[$_REQUEST['lang_id']]['title'] : $languagesArr[$langID]['title'];
$query = "SELECT *  FROM tb_sys_user_permissions  WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}

include_once($_project_server_path . $_includes_path . "modules.array.inc.php");
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");
require_once($_project_server_path . $_salat_path . $this_dir . "/modules_fields/" . str_replace('.php', '', basename(__FILE__)) . ".fields.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011
$selectArr = array(
    '1' => 'this is select1',
    '2' => 'this is select2',
    '3' => 'this is select3',
    '4' => 'this is select4',
);
$some_bit_Arr = array(
    1 => 'text1',
    2 => 'text2',
    4 => 'text3',
    8 => 'text4',
    16 => 'text4',
);
$radioArr = array(
    10 => 'radddio16',
    20 => 'radddio31',
    30 => 'radddioe1',
    40 => 'radddio71',
    50 => 'radddio01',
    80 => 'radddio13',
);


$_Proccess_Main_DB_Table = "tb_salat_examples";

$_Proccess_Title = $all_modulesArr[$_ProcessID];

$_Proccess_Has_Ordering_Action = false;

$_Proccess_Has_Ordering_Action2 = true;

$_Proccess_Has_MetaTags = false;

$_Proccess_Has_MultiLangs = true;

$_Proccess_Has_GenricSearch = true;

$_Proccess_Has_IntervalSearch = true;

/**
 *  option to make the csv export from db
 */
$_Proccess_Has_Csv_Export = true;

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
$_Proccess_FW_Params = array('lang_id', 'from_date', 'to_date');

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
if ($_Proccess_Has_GenricSearch) {
    genric_search_add_fwParams($_Proccess_FW_Params);
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

//=====USER SALAT==========================================================
$query = "SELECT * FROM `tb_sys_users` AS Users";
$resultUser = $Db->query($query);
$usersArr = array();
if ($resultUser->num_rows > 0) {
    while ($userLine = $Db->get_stream($resultUser)) {
        $usersArr[$userLine['id']] = $userLine['fullname'];
    }
}


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
    $submitStay = $submit . " {$_LANG['AND_STAY']}";
} elseif ($act == 'move') {
    $table_name = $_Proccess_Main_DB_Table;
    if($_REQUEST['link_table'] == 'true'){
        $table_name = $_Proccess_Main_DB_Table.'_link';
    }

    reOrderRows($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['id']);
    module_updateStaticFiles();
    header("Location: ?act=show" . $fwParams);
    exit();
} elseif ($act == 'after') {

    if ($obj_id) {  // ### UPDATE QUERY ###
        $_REQUEST['inner_id'] = $obj_id;
        if ($_Proccess_Has_MultiLangs) {
            multi_lang_update_query($_Proccess_Main_DB_Table, $_REQUEST['inner_id'], $fieldsArr, $module_lang_id);
        } else {
            $query = "UPDATE {$_Proccess_Main_DB_Table} SET " . fields_implode(',', $fieldsArr, $_REQUEST, true) . " WHERE id='{$obj_id}'";
            $result = $Db->query($query);
        }
    } else {
        $query = "SELECT count(id) FROM {$_Proccess_Main_DB_Table}";
        $result = $Db->query($query);
        //$newOrder = mysqli_result($result,0,0);

        if ($_Proccess_Has_MultiLangs) {
            multi_lang_insert_query($_Proccess_Main_DB_Table, $fieldsArr, $module_lang_id);

        } else {
            $query = "INSERT INTO {$_Proccess_Main_DB_Table}(" . fields_implode(', ', $fieldsArr) . ") VALUES (" . fields_implode(',', $fieldsArr, $_REQUEST) . ")";
            $result = $Db->query($query);
            $_REQUEST['inner_id'] = $Db->get_insert_id();
        }
        //Update creation time
        $time = time();
        $Db->query("UPDATE {$_Proccess_Main_DB_Table} SET `created_ts`= '{$time}' WHERE id = '{$_REQUEST['inner_id']}'");

        if ($_Proccess_Has_Ordering_Action) {
            setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
        }
        if ($_Proccess_Has_Ordering_Action2) {
            setMaxShowOrder($_Proccess_Main_DB_Table, 'order_num', 'id', $_REQUEST['inner_id']);
        }
    }


    //================== update the fields from functions  =====================
    if (isset($_REQUEST['gallery_id']) && $_REQUEST['gallery_id']) {
        update_functions_db(array('gallery_id'));
    }

    if (isset($_REQUEST['dynamTable']['id']) && $_REQUEST['dynamTable']['id']) {
        update_dynamic_table($_REQUEST['inner_id']);
    }

// Netanel 6.6: for coordinates polygon
    // David 18.9.14 - Update: Add coordinates as mysql polygon field to tb_polygon table
    $innerId = $_REQUEST['inner_id'];
    if (isset($_REQUEST['coordinates'])) {
        $polygon = "'POLYGON((";
        $coordinatesArr = $_REQUEST['coordinates'];
        $Db->query("DELETE FROM `tb_polygon` WHERE item_id='{$innerId}'");
        $deleteQuery = $Db->query("DELETE FROM tb_polygon_coordinates WHERE item_id='{$innerId}'");
        foreach ($coordinatesArr as $polygonNumber => $valueArr) {
            $polygonNumber = $Db->make_escape($polygonNumber);
            for ($i = 0; $i < count($valueArr['lat']); $i++) {
                $lat = floatval($valueArr['lat'][$i]);
                $lng = floatval($valueArr['lng'][$i]);
                if ($i == 0) {
                    $first_lat = $lat;
                    $first_lng = $lng;
                }

                $polygon .= $lat . ' ' . $lng . ",";
                $query = $Db->query("INSERT INTO tb_polygon_coordinates(`item_id`,`polygon_number`,`lat`,`lng`) VALUES ('{$innerId}','{$polygonNumber}',{$lat},{$lng})");
            }

            $polygon .= $first_lat . " " . $first_lng . "))'"; //The first coordinates need to be also the end.
            $query = $Db->query("INSERT INTO `tb_polygon`(`item_id`, `polygon_number`, `polygon`) VALUES ('{$innerId}','{$polygonNumber}',PolygonFromText($polygon))");
            $polygon = "'POLYGON((";
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
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/examples/' . get_item_dir($obj_id) . '/' . $languagesArr[$module_lang_id]['title'] . '/item-' . $obj_id . '.inc.php');
    } else {
        $query = "DELETE FROM {$_Proccess_Main_DB_Table} WHERE id='{$obj_id}'";
        // delete static file !!!
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/examples/' . get_item_dir($obj_id) . '/example-' . $obj_id . '.inc.php');
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
    if ($_Proccess_Has_Ordering_Action2) {
        fixOrderNum($_Proccess_Main_DB_Table, $_REQUEST['order_num'], "order_num");
    }
    if ($_Proccess_Has_MetaTags) {
        include_once($_project_server_path . $_salat_path . $_includes_path . "metaupdate.inc.php");
    }
    module_updateStaticFiles();
    header("location:?act=show" . $fwParams);
    exit();
} else { //act=show...
    $whereArr = array();
    if (($_Proccess_Has_GenricSearch) && ($_Proccess_Has_MultiLangs)) {
        genric_searchable_items($_ProcessID, $whereArr, 'Result');
    } else if ($_Proccess_Has_GenricSearch) {
        genric_searchable_items($_ProcessID, $whereArr);
    }

    if ($_REQUEST['topcat_id'] > 0) {
        $whereArr[] = "topcat_id={$_REQUEST['topcat_id']}";
    }

    if ($_Proccess_Has_IntervalSearch) {
        if (isset($_REQUEST['from_date']) && ($_REQUEST['from_date'] > 0)) {
            $_REQUEST['from_date'] = $Db->make_escape($_REQUEST['from_date']);
            $from_ts = strtotime($_REQUEST['from_date']);
            $whereArr[] = "date >={$from_ts}";
        }
        if (isset($_REQUEST['to_date']) && ($_REQUEST['to_date'] > 0)) {
            $_REQUEST['to_date'] = $Db->make_escape($_REQUEST['to_date']);
            $to_ts = strtotime($_REQUEST['to_date']) + 86400; //all day long..
            $whereArr[] = "date <={$to_ts}";
        }
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
    } elseif ($_Proccess_Has_Ordering_Action2) {
        $query .= " ORDER BY order_num ASC";
    } else {
        $query .= " ORDER BY id DESC";
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

    /*if($_Proccess_Has_MultiLangs){
        $UpdateStatic = new exampleLangUpdateStaticFiles();
        $UpdateStatic->updateStatics();
    }else{
            $UpdateStatic = new exampleUpdateStaticFiles();
            $UpdateStatic->updateStatics();
        }*/

}

function update_dynamic_table($id)
{
    global $row;

    $Db = Database::getInstance();

    $id = ($id) ? $id : $row['id'];
    $table = 'tb_salat_examples_link';
    foreach ($_REQUEST['dynamTable']['id'] as $num => $itemArr) {
        if(in_array($_SERVER['REMOTE_ADDR'],array('62.219.212.139','81.218.173.175','207.232.22.164'))) {
            die('<hr /><pre>' . print_r(array($_REQUEST['dynamTable']['id']), true).' - Here: ' . __LINE__ . ' at ' . __FILE__ . ' '.time().'</pre><hr />');
        }
        $update_fl = 0;
        $dyn_id = $_REQUEST['dynamTable']['id'][$num];
        if ($dyn_id) {
            $query = "SELECT id FROM `{$table}` WHERE id={$dyn_id}";
            $col_result = $Db->query($query);
            if ($col_result->num_rows > 0) {
                $update_fl = 1;
            }
        }
        if ($update_fl) {//update
            $db_fields = array(
                'title' => $_REQUEST['dynamTable']['title'][$num],
                'num' => $_REQUEST['dynamTable']['num'][$num],
                'hidden_val' => $_REQUEST['dynamTable']['hidden_val'][$num],
                'color' => $_REQUEST['dynamTable']['color'][$num],
                'textarea' => $_REQUEST['dynamTable']['textarea'][$num],
                'date' => $_REQUEST['dynamTable']['date'][$num],
                'active' => (isset($_REQUEST['dynamTable']['active'][$num]) && $_REQUEST['dynamTable']['active'][$num] == "on") ? 1 : 0,
                'last_update' => time(),
            );
            $updateArr = array();
            foreach ($db_fields as $k => $v) {
                $v = $Db->make_escape($v);
                $updateArr[] = "`$k` = '{$v}' ";
            }
            $query = "UPDATE `{$table}` SET  " . implode(',', $updateArr) . " WHERE `id`={$dyn_id}";
            $Db->query($query);
        } else {//insert
            $db_fields = array(
                'example_id' => $id,
                'title' => $_REQUEST['dynamTable']['title'][$num],
                'num' => $_REQUEST['dynamTable']['num'][$num],
                'hidden_val' => $_REQUEST['dynamTable']['hidden_val'][$num],
                'color' => $_REQUEST['dynamTable']['color'][$num],
                'textarea' => $_REQUEST['dynamTable']['textarea'][$num],
                'date' => $_REQUEST['dynamTable']['date'][$num],
                'active' => (isset($_REQUEST['dynamTable']['active'][$num]) && $_REQUEST['dynamTable']['active'][$num] == "on") ? 1 : 0,
                'last_update' => time(),
            );
            foreach ($db_fields AS $key => $value) {
                $db_fields[$key] = $Db->make_escape($value);
            }
            $query = "INSERT INTO `{$table}` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
            $Db->query($query);
        }
    }
}

function get_user_update()
{
    global $usersArr, $row;
    $value = $usersArr[$row['user_salat_id']];
    return <<<html
	<label>{$value}</label>
	<input type="hidden" name="user_salat_id" value="{$row['user_salat_id']}" />
html;
}

function update_functions_db($fieldsArr, $lang = '')
{
    global $_Proccess_Main_DB_Table;
    $Db = Database::getInstance();

    $id = ($lang) ? 'obj_id' : 'id';
    $db_fields = array();
    foreach ($fieldsArr as $field_name) {
        if ($_REQUEST[$field_name]) {
            $db_fields[$field_name] = $_REQUEST[$field_name];
        }
    }
    $updateArr = array();
    foreach ($db_fields as $k => $v) {
        $v = $Db->make_escape($v);
        $updateArr[] = "`$k` = '{$v}' ";
    }
    $query = "UPDATE `{$_Proccess_Main_DB_Table}{$lang}` SET  " . implode(',', $updateArr) . " WHERE `{$id}`='{$_REQUEST['inner_id']}'";
    $Db->query($query);
}

function draw_dynamic_table($table_link = '', $main_table = '', $field_id_name = '', $and_where = '')
{
    global $langID, $row;

    $Db = Database::getInstance();

    $itemsDyHTML = '';
    if ($langID == '2') { // 2 = hebrew
        $dirStyle = 'rtl';
    } else {
        $dirStyle = 'ltr';
    }
    $main_table = "tb_salat_examples";
    $table_link = "tb_salat_examples_link";
    $and_where = "WHERE Main.id='{$row['id']}'";
    $field_id_name = 'example_id';
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
            $color_html = "<div style='background-color: red; height: 20px;border:2px solid dimgrey;
                                    width: 40px; color:white; position:relative; margin:0 auto;'> yeaap </div>";
            $cells[$count_index][0] = array(
                'type' => 'text',
                'value' => $item['title'],
                'name' => 'dynamTable[title][' . $count_index . ']',
                'direction' => $dirStyle
            );
            $cells[$count_index][1] = array(
                'type' => 'text',
                'value' => $item['num'],
                'name' => 'dynamTable[num][' . $count_index . ']',
                'extra' => 'class="numOnly"',
                'direction' => 'ltr',
            );
            $cells[$count_index][2] = array(
                'type' => 'label',
                'value' => $item['text'],
                'name' => 'dynamTable[text][' . $count_index . ']',
                'extra' => '',
                'direction' => 'ltr',
            );
            $cells[$count_index][3] = array(
                'type' => 'textarea',
                'value' => $item['textarea'],
                'name' => 'dynamTable[textarea][' . $count_index . ']',
                'cols' => '7',
                'rows' => '4',
                'direction' => $dirStyle,
            );
            $cells[$count_index][4] = array(
                'type' => 'date',
                'value' => $item['date'],
                'name' => 'dynamTable[date][' . $count_index . ']',
                'extra_html' => 'style="width:150px;"',
            );
            $cells[$count_index][5] = array(
                'type' => 'html',
                'value' => $color_html,
            );
            $cells[$count_index][6] = array(
                'type' => 'hidden',
                'value' => 'some_val',
                'name' => 'dynamTable[hidden_val][' . $count_index . ']',
            );
            $color = ($item['color']) ? $item['color'] : 'ffffff';
            $cells[$count_index][7] = array(
                'type' => 'function',
                'array' => 'drawColorPicker("' . $color . '","colorSelection","dynamTable[color][' . $count_index . ']")',
            );
            $cells[$count_index][8] = array(
                'type' => 'active',
                'value' => $item['active'],
                'name' => 'dynamTable[active][' . $count_index . ']',
                'extra_after' => '<input type="hidden" name="dynamTable[id][' . $count_index . ']" value="' . $item['id'] . '"/>',
            );
            $cells[$count_index][9] = array(
                'type' => 'order',
                'value' => $order_num,
                'name' => 'dynamTable[order_num][' . $count_index . ']',
                'extra' => 'link_table=true',
            );
            $cells[$count_index][10] = array(
                'extra' => 'id="' . $item['id'] . '"',
                'class' => 'del_table colors_f',
            );
            $count_index++;
        }
    }else{
        $count_index = 0;
        $color_html = "<div style='background-color: red; height: 20px;border:2px solid dimgrey;
                                    width: 40px; color:white; position:relative; margin:0 auto;'> yeaap </div>";
        $cells[$count_index][0] = array(
            'type' => 'text',
            'value' => '',
            'name' => 'dynamTable[title][' . $count_index . ']',
            'direction' => $dirStyle
        );
        $cells[$count_index][1] = array(
            'type' => 'text',
            'value' => '',
            'name' => 'dynamTable[num][' . $count_index . ']',
            'extra' => 'class="numOnly"',
            'direction' => 'ltr',
        );
        $cells[$count_index][2] = array(
            'type' => 'label',
            'value' => '',
            'name' => 'dynamTable[text][' . $count_index . ']',
            'extra' => '',
            'direction' => 'ltr',
        );
        $cells[$count_index][3] = array(
            'type' => 'textarea',
            'value' => '',
            'name' => 'dynamTable[textarea][' . $count_index . ']',
            'cols' => '7',
            'rows' => '4',
            'direction' => $dirStyle,
        );
        $cells[$count_index][4] = array(
            'type' => 'date',
            'value' => '',
            'name' => 'dynamTable[date][' . $count_index . ']',
            'extra_html' => 'style="width:150px;"',
        );
        $cells[$count_index][5] = array(
            'type' => 'html',
            'value' => $color_html,
        );
        $cells[$count_index][6] = array(
            'type' => 'hidden',
            'value' => 'some_val',
            'name' => 'dynamTable[hidden_val][' . $count_index . ']',
        );
        $cells[$count_index][7] = array(
            'type' => 'function',
            'array' => 'drawColorPicker("ffffff","colorSelection","dynamTable[color][' . $count_index . ']")',
        );
        $cells[$count_index][8] = array(
            'type' => 'active',
            'value' => '',
            'name' => 'dynamTable[active][' . $count_index . ']',
            'extra_after' => '<input type="hidden" name="dynamTable[id][' . $count_index . ']" value="' . $count_index . '"/>'
        );
        $cells[$count_index][9] = array(
            'type' => 'order',
            'value' => '1',
            'name' => 'dynamTable[order_num][' . $count_index . ']',
        );
        $cells[$count_index][10] = array(
            'extra' => 'id="' . $count_index . '"',
            'class' => 'del_table colors_f',
        );
    }
    $itemsDyHTML .= make_dynamic_table($cells, array('כותרת', 'מספר', 'טקסט ללא עריכה', 'textarea', 'תאריך', 'html', 'צבע', 'פעיל', 'סידור'), true, true, true);
    $itemsDyHTML .= '</div>';
    return $itemsDyHTML;

}

function get_type($type = '')
{
    global $item_TypeArr, $row;
    if ($type == 'lead') {
        $val = (isset($row['product_type']) && $row['product_type']) ? $row['product_type'] : 2;
        $html = <<<html
			<label>{$item_TypeArr[$val]}</label>
			<input type="hidden" value="{$val}" name="product_type"/>
html;
    } else {
        $val = (isset($row['product_type']) && $row['product_type']) ? $row['product_type'] : 1;
        $html = <<<html
			<label>{$item_TypeArr[$val]}</label>
			<input type="hidden" value="{$val}" name="product_type"/>
html;
    }
    return $html;
}

function some_function()
{
    return <<<HTML
		<div style="color:red; background:green; width:250px;height:250px;font-weight: bold;font-size: 37px;text-align: center;line-height: 90px;"> <br/> YYeeaapppp </div>
HTML;

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
        }


        $(function () {

            var $editable = $('.salat-editable');
            $editable.editable();

            $editable.on('save', function(e, params) {
                var field = $(this).data('field');
                var newValue = params.newValue;
            });

            $('.numOnly,#numOnly').live('keypress', function (event) {
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9) return;
                if (event.which < 48 || event.which > 57) {
                    event.preventDefault();
                } // prevent if not number/dot
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


            // color picker default and initialization
            $.each($('.colorSelection'), function (key, val) {
                $(val).find('div').css('backgroundColor', '#' + $(val).find('input:hidden').val());
                $(val).ColorPicker({
                    color: '#' + $(val).find('input:hidden').val(),
                    onShow: function (colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        $(colpkr).fadeOut(500);
                        return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                        $(val).find('div').css('backgroundColor', '#' + hex);
                        $(val).find('input:hidden').val(hex);
                    }
                });
            });

            //Orders
            $('.order').on('click', function () {
                var order_num = $($(this).parent().find('.input_order')).val();
                var params = '<?=$fwParams;?>';
                $.post("/salat2/_ajax/ajax.index.php", '&file=example_service&act=new_order&id=' + $(this).attr('example_id') + '&order_num=' + order_num, function (result) {
                    if (result.msg == 'OK') {
                        document.location.href = "?act=show" + params;
                    }
                }, "json");
            });

            // delete DYNAMIC items and features
            $('.delRowGenTable').live('click', function () {
                var item_id = '<?=$obj_id;?>';
                if ($(this).hasClass('del_table')) {
                    $.get('/salat2/_ajax/ajax.index.php', {
                        'file': 'example_service',
                        'id': $(this).attr('id'),
                        'act': 'delete_rows',
                        'item_id': item_id
                    }, function (response) {

                    });
                }
            });


            //features
            $('.color_hex_id').on('click', function () {
                alert('זה רק לתצוגה, כדי להוסיף צבע תרשום בכותרת שם של צבע ותבחר מאופציות');
            });

            get_colors();


            //change to TEXT or LABEL field at stock...
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


            // UPLOAD NEW GALLERY
            /* start choose gallery and show all her items */
            $(".gallery_new_media").live('change', function (event) {
                var div_id = $(this).attr('id') + '_items';
                var Scope = $(this).parent();
                $.get('/salat2/_ajax/ajax.index.php', {
                    'file': 'getGalleryItems',
                    'gal_id': $(this).val(),
                    'id_name': div_id,
                    'field_name': $(this).attr('name')
                }, function (response) {
                    $("#" + div_id).html(response);
                });
            });
            /* end of choose gallery and show all her items */

            $(".uploadGallery").each(function (a, item) {
                $('#uploadGallery_' + $(item).attr("rel")).uploadify({
                    'uploader': '/resource/uploadify/uploadify.swf',
                    'script': '/resource/uploadify/gallery.upload.php',
                    'cancelImg': '/resource/uploadify/cancel.png',
                    'folder': '/_media/temp/',
                    'hash': '',
                    'item_id': '<?=$obj_id;?>',
                    'buttonImg': '../_public/upload.png',
                    'width': 120,
                    'height': 27,
                    'fileExt': '*.jpg; *.png; *.bmp;*.jpeg;*.gif;*.mov;*.avi;*.mp4;*.wmv;*.mpg',
                    'fileDesc': 'קבצי מדיה',
                    'wmode': 'transparent',
                    'scriptData': {'item_id': '<?=$obj_id;?>', 'table': 'tb_salat_examples_lang', 'name': 'example'},
                    'auto': true,
                    'multi': true,
                    'hideButton': true,
                    'onSelect': function (event, ID, fileObj) {
                        //add_loader();
                        //remove_loader();
                        if (fileObj.size > 35840000) {
                            alert('גודל קובץ וידיאו מקסימלי  הינו :30 MB');
                            total_video = 0;
                            remove_loader();
                            return false;
                        }
                    },
                    'onComplete': function (event, ID, fileObj, response, data) {
                        var responseObj = $.parseJSON(response);
                        if (responseObj.err) {
                            alert(responseObj.err);
                            remove_loader();
                        }
                        $.post("/salat2/_ajax/ajax.index.php", '&file=media&action=reload_gallery_select&gallery_id=' + responseObj.album_id, function (result) {
                            if (result.html != "") {
                                $(item).parent().find('select').html(result.html);
                                $($(item).parent().find('select')).trigger("change");
                            }
                        }, "json");
                    }
                });
            });

        });


        function get_colors() {
            var Scope;
            $('.feature_color').live('keydown', function () {
                var flag = 1;
                Scope = $(this);
                if (flag == 1) {
                    flag++;
                    $('.feature_color')
                        .autocomplete('/salat2/_ajax/ajax.index.php', {
                            'extraParams': {
                                'file': 'auto_complete/item',
                                'action': 'get_colors',
                                'lang_id': '<?=$module_lang_id;?>'
                            }
                        }).result(function (event, data) {
                        var color_id = data[1];
                        $($(Scope).parent().next().find('.color_id_hidden')).val(color_id);
                        $.post('/salat2/_ajax/ajax.index.php', {
                                'color_id': color_id,
                                'file': 'auto_complete/item',
                                'action': 'get_color_hex'
                            },
                            function (response, data1) {
                                $($(Scope).parent().next().find('.color_hex_id')).css('background-color', response);
                            }
                        );
                    });
                }
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
<input type="button" class="buttons" onclick="javascript: location.href='?act=show<?php /*echo $fwParams;*/ ?>';"
       value="הצג הכל"/>
<input type="button" class="buttons new" onclick="javascript: location.href='?act=new<?php echo $fwParams; ?>';"
       value="הוסף חדש"/>

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
                        <td><span>כותרת</span>
                            <input type="text" id="search_ajax" style="width:180px;direction: ltr"/>
                        </td>

                        <?
                        foreach ($fieldsArr AS $key => $fieldArr) {
                            if ($fieldArr['input']['searchable'] == true) {
                                print "<td>" . $fieldArr['title'] . ":";
                                print draw_genric_search($fieldArr, $key) . "</td>";
                            }
                        }
                        ?>
                        <? if ($_Proccess_Has_IntervalSearch) { ?>
                            מתאריך:
                            <input type="text" size="15" value="<?= $_REQUEST['from_date']; ?>" id="from_date"
                                   name="from_date"/>
                            <img onclick="javascript: NewCal('from_date', 'ddmmyyyy', false, 24);"
                                 src="../_public/datetimepicker.gif" class="pointer" style="vertical-align: middle;"/>
                            &nbsp;&nbsp;
                            עד תאריך:
                            <input type="text" size="15" value="<?= $_REQUEST['to_date']; ?>" id="to_date"
                                   name="to_date"/>
                            <img onclick="javascript: NewCal('to_date', 'ddmmyyyy', false, 24);"
                                 src="../_public/datetimepicker.gif" class="pointer" style="vertical-align: middle;"/>
                            &nbsp;&nbsp;
                        <? } ?>
                        <td><input type="submit" value="חיפוש" class="buttons"/></td>
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
                    <td width="70">סדר</td>
                <?php } ?>
                <td width="100">סידור ידני</td>
                <td width="100">צבע</td>
                <td width="100">&nbsp;</td>
            </tr>
            <?php for ($count = $resultArr['result']->num_rows, $i = 0; $row = $Db->get_stream($resultArr['result']); $i++) {
                $c = ($i % 2 == 0) ? 'even' : 'odd'; ?>
                <tr class="normTxt <?= $c ?>">
                    <?php $columns_count = fields_get_show_rows_fields($fieldsArr, $row, false); ?>
                    <?php if ($_Proccess_Has_Ordering_Action2) { ?>
                        <td class="dottTblS">
                            <input type=text class="input_order" style="width: 55px;" name="order_num"
                                   value="<?= $row['order_num']; ?>"/>
                            <input type="button" class="buttons order"
                                   example_id="<?php echo $row['id'] . $fwParams; ?>" value="עדכן"/> &nbsp;
                        </td>
                    <? } ?>
                    <td class="dottTblS">
                        <?php
                        $query = "SELECT color FROM `{$_Proccess_Main_DB_Table}` WHERE id = " . $row['id'];
                        $result = $Db->query($query);
                        $rowColor = $Db->get_stream($result);
                        $hexColor = "#" . $rowColor['color'];
                        echo("<div style='background-color: $hexColor; height: 20px;
                                    width: 20px;
                                    position:relative;
                                    float:right;
                                    padding: 5px;
                                    margin:0 0 4px 30px;'></div>");
                        ?>
                    </td>
                    <?php if ($_Proccess_Has_Ordering_Action) { ?>
                        <td class="dottTblS"><?php echo outputOrderingArrows($count, $i, 'id', $row['id'], $row['order_num']); ?></td>
                    <?php } ?>

                    <td class="dottTblS">
                        <?php if (!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)) { ?>
                            <input type="button" class="buttons" value="<?= $_LANG['BTN_EDIT']; ?>"
                                   onclick="javascript: location.href='?act=new&id=<?php echo $row['id'] . $fwParams; ?>';"/> &nbsp;
                        <?php }
                        if (!in_array($row['id'], $_Proccess_HC_RowsID_Arr_NOT_DELETABLE)) { ?>
                            <input type="button" class="buttons red" value="<?= $_LANG['BTN_DEL']; ?>"
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

                    <?= $_LANG['EXPORT_TO_CSV']; ?>
                </a>
            </div>
        <? } ?>
    <?php } elseif ($act == "new") { ?>
        <form name="form" action="" class="form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="act" value="after"/>
            <input type="hidden" name="upsaleid" value="" class="subcategory_upsale"/>
            <input type="hidden" name="product_type" value="1"/>
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="table-edit">
                <tr class="dottTbl">
                    <td colspan="2">הוספה / עריכה</td>
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
                        <input type="submit" name="send" value="<?php echo $submit; ?>" class="buttons"/>
                        <input type="submit" name="stay" value="<?php echo $submitStay; ?>" class="buttons stay"/>
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