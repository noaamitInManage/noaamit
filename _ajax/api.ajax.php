<?php

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$responseArr = array("status" => 1, "err" => "", "msg" => "", "relocation" => "", "html" => "");

$platform = 'website';
$version = '1.0';

switch ($act) {

    case 'execute':
        $method_name = (isset($_REQUEST['method_name']) && $_REQUEST['method_name']) ? siteFunctions::safe_value($_REQUEST['method_name'], 'text') : '';

        $Api = new apiManager($platform, $version, $method_name);
        $answer = $Api->execute();
        exit($answer);

        break;

    default:

        break;
}
exit(json_encode($responseArr));