<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 08/08/13
 * Time: 13:03
 * MCDONALDS  parse data
 *
 * this cron delete data from  /_static/data/parsed and save only 10 last recoreds
 *
 * @frqancy : EVERY 24 Hours at 3:00 pm ;
 */
if (!isset($_REQUEST["cp"]) || $_REQUEST["cp"] != "qq978ARXjVyMdd") {
    die("No permission!");
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "mobile_html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions


$last_3_month = strtotime('-3 month');
$res = $Db->delete('tb_api_log', 'last_update', '<', $last_3_month);
$effected_rows = $Db->get_affected_rows();
echo "done. <br> {$effected_rows} rows was deleted.";
?>


