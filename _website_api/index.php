<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 24/06/13
 * Time: 13:44
 * @desc : generic site api
 */

session_start();
$qArr=explode('/',$_SERVER['REQUEST_URI']);
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
include_once($_project_server_path . "index.class.include.php");  // load class index
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
$Browser = new Browser();
/**
 * $platform =  iphone | android
 * $version =  1.0 | 1.1 ...
 * $method_name =  getContent | setBranch
 */
/*if(in_array($_SERVER['REMOTE_ADDR'],configManager::$familiar_ipsArr)) {
	$Push =  new pushManager();
	$Push->send_push('d0d491bf9a30931c9cb1f3b0ba43770ac6b47e220888005915c999fed78b29d9','בדיקה','iphone');
}*/
$tokenManager = tokenManager::getInstance();
$Aes = new AES256Bit();
$method_name = $Aes->decrypt($_REQUEST['action']);
//  && $tokenManager->verify_token($_REQUEST['token_api'])

if($method_name) {

    $Api = new websiteApiManager();
    $answer = $Api->execute($method_name);

    exit($answer);
} else {
    exit(json_encode(array("err"=>"unknown method","status"=>"","message"=>"מתודה לא קיימת")));
}