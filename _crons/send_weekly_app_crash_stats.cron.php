<?php

/**
 * @interval every sunday at 09:00
 */

if (!isset($_REQUEST["cp"]) || $_REQUEST["cp"] != "qq978ARXjVyMdd") {
    die("No permission!");
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
//include_once($_project_server_path . $_salat_path . '_inc/moduleUpdateStaticFiles.php'); //load update static class
include_once($_project_server_path . "index.class.include.php");  // load class index
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
//include_once($_project_server_path . '/_crons/cronsManager.class.inc.php');//cronsManager

$cron_name = "send_weekly_app_crash_stats";

$Cron = new cronsManager($cron_name);
if ($Cron->is_running(1)) {
    echo "<pre>IN PROGRESS</pre>";
    exit();
}

$Cron->start_proccess();

$sent = siteFunctions::send_weekly_app_crash_stats();

echo "done, " . ($sent ? 'success' : 'error');

$Cron->end_proccess(1);