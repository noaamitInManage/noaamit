<?php

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$answer = array("status" => 1, "err" => "", "msg" => "", "relocation" => "", "html" => "");

switch ($act) {

    case 'abcde':
        exit(json_encode($answer));
        break;

    default:

        break;
}