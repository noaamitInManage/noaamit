<?
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");

$responseArr = array("err" => '', "status" => 0, 'html' => '');
$err = '';
$msg = '';
$html = '';
$status = false;
$act = $_REQUEST['act'] ? $_REQUEST['act'] : $_REQUEST['action'];

switch ($act) {

    case 'add_room_limited_company':

        if (isset($_REQUEST['company_id']) && $_REQUEST['company_id'] && isset($_REQUEST['room_id']) && $_REQUEST['room_id']) {

            $query = "SELECT *
                        FROM `tb_meeting_rooms__limited_to_companies`
                            WHERE `company_id` = {$_REQUEST['company_id']} AND `meeting_room_id` = {$_REQUEST['room_id']}";
            $res = $Db->query($query);
            if (!$res->num_rows) {
                $db_fields = array(
                    'company_id' => $_REQUEST['company_id'],
                    'meeting_room_id' => $_REQUEST['room_id'],
                    'last_update' => time(),
                );
                $Db->insert('tb_meeting_rooms__limited_to_companies', $db_fields);
                $html = draw_meeting_room_limited_to_companies($_REQUEST['room_id']);
                $status = 1;
            } else {
                $status = 0;
                $err = 'The meeting room is already limited to this company';
            }
        }

        break;


        case 'delete_room_limited_company':

        if (isset($_REQUEST['company_id']) && $_REQUEST['company_id'] && isset($_REQUEST['room_id']) && $_REQUEST['room_id']) {
//            $query = "DELETE FROM `tb_meeting_rooms__limited_to_companies` WHERE `company_id` = {} AND `room_id` = {}";
//            $Db->query($query);
            $Db->delete('tb_meeting_rooms__limited_to_companies', [
                ['company_id', $_REQUEST['company_id']],
                ['meeting_room_id', $_REQUEST['room_id']]
            ]);
            $html = draw_meeting_room_limited_to_companies($_REQUEST['room_id']);
            $status = 1;
        }

        break;
}
$responseArr['html'] = $html;
$responseArr['err'] = $err;
$responseArr['msg'] = $msg;
$responseArr['status'] = $status;
exit(json_encode($responseArr));
?>