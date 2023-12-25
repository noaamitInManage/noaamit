<?php

$cp = '12345678';
if ($_REQUEST['cp'] != $cp) {
    die('No Permission');
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
//include_once($_project_server_path . $_salat_path . '_inc/moduleUpdateStaticFiles.php'); //load update static class
include_once($_project_server_path . "index.class.include.php");  // load class index
//include_once($_project_server_path . $_includes_path . 'class/apiManager.class.inc.php');//apiManager
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
//include_once($_project_server_path . "_crons/cronsManager.class.inc.php"); // cronsManager

$cron_name = "open_close_stores";

$Cron = new cronsManager($cron_name);
if ($Cron->is_running(1)) {
    echo "<pre>IN PROGRESS</pre>";
    exit();
}

$Cron->start_proccess();

$table = 'tb_stores';

$Db = Database::getInstance();
$query = "UPDATE {$table} SET `open` = !`open` WHERE `active` = 1";
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/or_logs.txt', DateTime::createFromFormat('U.u',sprintf("%.6F", microtime(true)))->format("m-d-Y H:i:s.u")." : ". print_r(array(
'$query' => $query,
'Here: ' . __LINE__ . ' at ' . __FILE__
), true) . PHP_EOL, FILE_APPEND | LOCK_EX);
$Db->query($query);
$UpdateStatic = new storesLangsUpdateStaticFiles();
$UpdateStatic->updateStatics();

echo "done";

$Cron->end_proccess(1);