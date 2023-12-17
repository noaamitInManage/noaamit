<?php
$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$answer = array("err" => "", "msg" => "", "relocation" => "", "reload" => "", "html" => "");
include($_project_server_path . $_salat_path . '_inc/db_syncArr.php'); //$db_informationArr

switch ($act) {
    case 'draw_table_col':
        $html = '<table id="" border="2" class="display" cellspacing="0" width="100%" dir="ltr;">';
        $query = "SHOW COLUMNS FROM `{$_REQUEST['table_name']}`";
        $res = $Db->query($query);
        $html .= <<<html
                    <tr style="background-color: #4cafee;">
                        <th>Field Name</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Key</th>
                        <th>Default</th>
                        <th>Extra</th>
                    </tr>
html;

        while ($line = $Db->get_stream($res)) {
            $html .= <<<html
                    <tr>
                        <td style="text-align: left;">{$line['Field']}</td>
                        <td style="text-align: left;">{$line['Type']}</td>
                        <td style="text-align: left;">{$line['Null']}</td>
                        <td style="text-align: left;">{$line['Key']}</td>
                        <td style="text-align: left;">{$line['Default']}</td>
                        <td style="text-align: left;">{$line['Extra']}</td>
                    </tr>

html;
        }

        $html .= '</table>';
        $answer['html'] = $html;
        break;
    case 'sync_table':
        global $db_informationArr;
        $table_name = $_REQUEST['table_name'];
        $sec_server_name = $_REQUEST['sec_server_name'];
        $main_server_name = $_REQUEST['main_server_name'];
        //==========create connections to DB==============
        $main_conn_info = reset($db_informationArr);
        $main_conn = mysqli_connect($main_conn_info["host"], $main_conn_info["user"], $main_conn_info["pass"], $main_conn_info["db"]);
        foreach ($db_informationArr AS $key => $connArr) {
            if (in_array($sec_server_name, $connArr)) {
                $sec_conn_info = $connArr;
                break;
            }
        }

        $sec_conn = mysqli_connect($sec_conn_info["host"], $sec_conn_info["user"], $sec_conn_info["pass"], $sec_conn_info["db"]);
        //================================================

        $query = "SHOW COLUMNS FROM `{$table_name}`";
        $res = $Db->query($query, $main_conn);
        while ($line = $Db->get_stream($res)) {
            $query = "SHOW COLUMNS FROM  `{$table_name}` WHERE Field =  '{$line['Field']}'";
            $res1 = $Db->query($query, $sec_conn);
            if ($res1->num_rows > 0) {
                $field = $Db->get_stream($res1);
                if ($line['Type'] != $field['Type']) {
                    $null = $line['Null'] == "NO" ? "NOT NULL" : "NULL";
                    $query = "ALTER TABLE  `{$table_name}` CHANGE  `{$field['Field']}` `{$line['Field']}` {$line['Type']} {$null} {$line['Extra']}";
                    $Db->query($query, $sec_conn);
                    $answer['msg'] = "OK";
                }
            } else {
                $null = $line['Null'] == "NO" ? "NOT NULL" : "NULL";
                $query = "ALTER TABLE  `{$table_name}` ADD `{$line['Field']}` {$line['Type']} {$null} {$line['Extra']}";
                $Db->query($query, $sec_conn);
                $answer['msg'] = "OK";
            }
        }

        break;
    case 'create_table':
        $table_name = $_REQUEST['table_name'];
        $sec_server_name = $_REQUEST['sec_server_name'];
        $main_server_name = $_REQUEST['main_server_name'];

        //==========create connections to DB==============
        $main_conn_info = reset($db_informationArr);
        $main_conn = mysqli_connect($main_conn_info["host"], $main_conn_info["user"], $main_conn_info["pass"], $main_conn_info["db"]);
        foreach ($db_informationArr AS $key => $connArr) {
            if (in_array($sec_server_name, $connArr)) {
                $sec_conn_info = $connArr;
                break;
            }
        }
        $sec_conn = mysqli_connect($sec_conn_info["host"], $sec_conn_info["user"], $sec_conn_info["pass"], $sec_conn_info["db"]);
        //================================================

        $query = "SHOW CREATE TABLE `{$table_name}`";
        $res = $Db->query($query, $main_conn);
        $line = $Db->get_stream($res);
        $Db->query($line['Create Table'], $sec_conn);
        $answer['msg'] = "OK";
        $html = <<<html
            <p style="font-size: 20px; color: green; font-weight: bold;">הסנכרון התבצע בהצלחה!</p>
            <br/>
            <input type="button" class="reloadAgain" value="טען מחדש" />
html;
        $answer['html'] = $html;
        break;

    default:

        break;

}
exit(json_encode($answer));