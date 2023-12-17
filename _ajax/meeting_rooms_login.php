<?php

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$answer = array("status" => 1, "err" => "", "msg" => "", "relocation" => "", "html" => "");

switch ($act) {

    case 'login':

        break;

    default:

        break;
}
exit(json_encode($answer));