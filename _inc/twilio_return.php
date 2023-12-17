<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
include_once($_project_server_path . "index.class.include.php");  // load class index
include($_project_server_path . $_includes_path . 'class/apiManager.class.inc.php');//apiManager
include($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/FacebookManager.class.inc.php'); // FacebookManager
include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/LinkedInManager.class.inc.php'); // LinkedInManager
include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/SalesForceManager.class.inc.php'); // SalesForceManager
include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/TwilioManager.class.inc.php'); // TwilioManager

$cp = 'qq978ARXjVyMdd';
if ($_REQUEST['cp'] != $cp) {
    die('...');
}

$ts = time();

$sid = isset($_REQUEST['SmsSid']) ? $_REQUEST['SmsSid'] : '';
$from = isset($_REQUEST['From']) ? $_REQUEST['From'] : '';
$to = isset($_REQUEST['To']) ? $_REQUEST['To'] : '';
$status = isset($_REQUEST['SmsStatus']) ? $_REQUEST['SmsStatus'] : '' ;

$TwilioManager = new TwilioManager();
$TwilioManager->update_message_status($sid, $status);
$TwilioManager->write_status_log($sid, $from, $to, $status, $ts);
