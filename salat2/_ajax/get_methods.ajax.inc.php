<?php

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$method_name = $_REQUEST['method_name'];
$api_name = $_REQUEST['api_name'];
$answer = array("err" => "", "msg" => "", "status" => "", "html" => "");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
$Db = Database::getInstance();
$ts = time();
switch ($act) {
    case "check_or_uncheck_method":
        $query = "
                    INSERT INTO `tb_get_methods` (`name`, `active`, `api_name`, `last_update`) VALUES ('{$method_name}', 1, '{$api_name}', {$ts})
                    ON DUPLICATE KEY UPDATE `active` = IF(`active` = 0, 1, 0), `last_update` = {$ts}
                ";
        $Db->query($query);
        $UpdateStatic = new getMethodsUpdateStaticFiles();
        $UpdateStatic->updateStatics();
        break;
}

echo json_encode($answer);