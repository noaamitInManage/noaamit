<?php


date_default_timezone_set('Asia/Jerusalem');
ini_set('MEMORY_LIMIT', '256MB');
set_time_limit(540);

$cp = 'qq978ARXjVyMdd';
if ($_REQUEST['cp'] != $cp) {
    die('...');
}
//mail('oleg@inmanage.net','mail - '.__FILE__,print_r(array('GEOIP CRON START OK!'),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
// includes
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
include_once($_project_server_path . "index.class.include.php");  // load class index
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
$Seo = new Seo();

$Queue = new QueueManager();
$result = $Queue->run();

die('<hr /><pre>' . print_r(array($result, '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />');