<?php

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
//09.12.2018 | Itay >>  Load Balancer support
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&  $_SERVER['HTTP_X_FORWARDED_FOR']){
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$Db = Database::getInstance();

?>