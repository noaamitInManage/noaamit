<?php
set_time_limit(0);
include_once("../_inc/config.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

$act=($_REQUEST['action']) ? $_REQUEST['action'] : 'show' ;
$err='';
$html='';
$status = false;

switch ($act){
    case "updateActiveState":
        $item_id = $_REQUEST['item_id'] ? $Db->make_escape($_REQUEST['item_id']) : '';
        $active_state = $_REQUEST['active_state'] ? $Db->make_escape($_REQUEST['active_state']) : '';
        $time = time();

        $query = "
            UPDATE `tb_feature_flags` SET `active` = '{$active_state}', `last_update` = '{$time}'
            WHERE `id` = '{$item_id}'
        ";
        $result = $Db->query($query);

        $UpdateStatic = new featureFlagsUpdateStaticFiles();
        $UpdateStatic->updateStatics();

        $status = true;

        break;

    default:
        break;
}

echo json_encode(array("err"=>$err,"status"=>$status,'html'=>$html));


?>

