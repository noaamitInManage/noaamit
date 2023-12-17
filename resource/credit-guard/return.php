<?php
/**
 * Created by PhpStorm.
 * User: Gal Zalait
 * Date: 6/5/14
 * Time: 12:18 PM
 * 
 */
session_start();
set_time_limit(0);
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."mobile_html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
include_once($_project_server_path.'/salat2/'.$_includes_path."metaindex.php"); // load meta functions
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions   TODO: [CHANGE THE 'site' TO SITE NAME]
include_once($_project_server_path."index.class.include.php");  // load class index

// split between the status and the hash params
if(strpos($_REQUEST['status'], 'hash')) {
    list($_REQUEST['status'], $_REQUEST['hash']) = explode("&", $_REQUEST['status']);
    $_REQUEST['hash'] = str_replace("hash=", "", $_REQUEST['hash']);
}

// In case the user lost it's session during the payment process, restore it from the db
if($_REQUEST['userData7'] || $_REQUEST['hash']) {
    $field_to_load = $_REQUEST['userData7'] ?: urldecode($_REQUEST['hash']);
    siteFunctions::load_order_session_from_db($field_to_load);
}

$status =(isset($_REQUEST['status']) && ($_REQUEST['status'])) ? strtolower(trim($_REQUEST['status'])) : '';
die('<hr /><pre>' . print_r(array(errorManager::get_cg_error(abs($_REQUEST['ErrorCode']))), true) . '</pre><hr />');
$payment_id = 1; //credit card
switch($status){
	case 'success':

		break;

	case 'error':
	default:
		exit("location: ".'/resource/payment/cg/failure/?json='.json_encode(errorManager::get_cg_error(abs($_REQUEST['ErrorCode']))));
		break;
}

?>