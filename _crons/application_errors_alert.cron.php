<?php

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
//include($_project_server_path . $_salat_path . '_inc/moduleUpdateStaticFiles.php'); //load update static class
include_once($_project_server_path . "index.class.include.php");  // load class index
//include($_project_server_path . $_includes_path . 'class/apiManager.class.inc.php');//apiManager
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
//include_once($_project_server_path . "_crons/cronsManager.class.inc.php"); // cronsManager

$cron_name = "application_errors_alert";

$Cron = new cronsManager($cron_name);
if ($Cron->is_running(1)) {
    echo "<pre>IN PROGRESS</pre>";
    exit();
}

$Cron->start_proccess();

$Db = Database::getInstance();
$time = time();
$time_indicator = $time - (60*60*5);

$sql = "SELECT * 
        FROM `tb_application_errors` 
        WHERE notification_sent = 0 
        AND last_update > '{$time_indicator}'
        limit 10";

$res = $Db->query($sql);
while($row = $Db->get_stream($res))
{
    $message = '';
    foreach ($row as $key => $value)
        $message .= "|$key: $value| \n";
    mail('gal@inmanage.co.il', 'Salat Error Notification', $message);
    $sql = "UPDATE `tb_application_errors` SET notification_sent = 1, last_update = '{$time}' WHERE id = '{$row['id']}'";
    $Db->query($sql);
}


$Cron->end_proccess(1);
echo "done";

