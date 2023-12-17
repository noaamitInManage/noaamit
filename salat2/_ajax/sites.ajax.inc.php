<?php
$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$site_id = intval($_REQUEST['site_id']);
$answer = array("err" => "", "msg" => "", "status" => "", "html" => "");
$ts = time();

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
$Db = Database::getInstance();
$UpdateStatic = new sitesLangsUpdateStaticFiles();

switch ($act) {
    case "add_floor":
        $floorArr = $_REQUEST['floorArr'];
        $db_fieldsArr = array(
            'site_id' => $floorArr['site_id'],
            'floor' => $floorArr['floor'],
            'order_num' => $floorArr['order_num'],
            'last_update' => $ts,
        );
        $Db->insert('tb_sites__floors_order', $db_fieldsArr);
        $status = true;
        $UpdateStatic->updateStatics($site_id);
        $html = draw_site_floors_order_field($floorArr['site_id'], true);
        $answer = array("err" => "", "msg" => "Floor added successfuly", "status" => $status, "html" => $html);
        break;
    case "order_floors":
        $floor_id = intval($_REQUEST['floor_id']);
        $item_index = intval($_REQUEST['item_index']);
        $direction = $_REQUEST['direction'];
        $other_index = '';
        if ($direction == 'down') {
            $other_index = $item_index + 1;
        } else {
            $other_index = $item_index - 1;
        }
        $sql = "
			UPDATE `tb_sites__floors_order` SET `order_num` =
			CASE `order_num`
				WHEN {$item_index} THEN {$other_index}
				WHEN {$other_index} THEN {$item_index}
				ELSE `order_num`
			END
		WHERE `id` = {$floor_id} AND `site_id` = {$site_id}";
        $res = $Db->query($sql);
        if (!$res) {
            $err = "DB Error";
            break;
        }
        $status = true;

        $UpdateStatic->updateStatics($site_id);
        $html = draw_site_floors_order_field($site_id, true);
        $answer = array("err" => "", "msg" => "", "status" => $status, "html" => $html);
        break;
    case "update_floor":
        $floor_id = intval($_REQUEST['floor_id']);
        $floorArr = $_REQUEST['floorArr'];
        if (!$floorArr['floor']) {
            $answer = array("err" => "You must fill the floor field", "msg" => "", "status" => 0);
            break;
        }
        $db_fieldsArr = array(
            'floor' => $floorArr['floor'],
            'last_update' => $ts,
        );
        $Db->update('tb_sites__floors_order', $db_fieldsArr, 'id', $floor_id);
        $status = true;
        $UpdateStatic->updateStatics($site_id);
        $html = draw_site_floors_order_field($site_id, true);
        $answer = array("err" => "", "msg" => "The floor was updated successfuly", "status" => $status, "html" => $html);
        break;

    case "delete_floor":
        $floor_id = intval($_REQUEST['floor_id']);
        $Db->delete('tb_sites__floors_order', 'id', $floor_id);
        $status = true;
        $UpdateStatic->updateStatics($site_id);
        $html = draw_site_floors_order_field($site_id, true);
        $answer = array("err" => "", "msg" => "", "status" => $status, "html" => $html);
        break;
}

echo json_encode($answer);

?>