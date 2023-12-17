<?
/*
* sync logs tables to DB logs once a day
* User: Galevy
* Date: 22/06/2016
* Time: 14:00
*/

$cp = 'qq978ARXjVyMdd';

if($_REQUEST['cp'] != $cp){
	die('...');
}

include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path."index.class.include.php");  // load class index
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions


//cronsManager::run_on_stage();

$Cron = new cronsManager($cron_name);
if($Cron->is_running(1)) {
	echo "<pre>IN PROGRESS</pre>";
	exit();
}

$Cron->start_proccess();

$local_db = true;
$db_informationArr = ($local_db) ? $db_logs_informationArr['local'] : $db_logs_informationArr['external'];
$RecordDBLogs = new RecordDBLogs($db_informationArr);

//$RecordDBLogs->deleteDuplicateRowsFromBackupAndMain();
//die('<hr /><pre>' . print_r('Here: ' . __LINE__ . ' at ' . __FILE__, true) . '</pre><hr />');
foreach($RecordDBLogs->logTablesArr AS $table) {
	echo "{$table} - sync structure...";
	$RecordDBLogs->syncTables($table, $local_db);
	echo "DONE<br />";
	echo "{$table} - sync data...";
	$RecordDBLogs->syncData($table);
	echo "DONE<br />";
	echo "{$table} - delete source...";
	$RecordDBLogs->truncateLogTable($table);
	echo "DONE<br /><br />";
}

echo "done";

$Cron->end_proccess(1);

?>