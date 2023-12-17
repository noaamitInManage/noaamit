<?php
$clientLibraryPath = $_SERVER['DOCUMENT_ROOT'].'/_inc/vendors/';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
$mobile_path=$_SERVER['DOCUMENT_ROOT'].'/mobile/';
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
include_once($_project_server_path.$_salat_path.$_includes_path."metaindex.php"); // load meta functions
include_once($_project_server_path."index.class.include.php");

header('Content-type: application/json');
$db_success = 1;
error_reporting(E_ALL);
ini_set('display_errors', '1');
$query = "SELECT TRUE";
$Db= Database::getInstance();
$handler = $Db->query($query) or $db_success=0;
echo json_encode(array('success'=>1,'db_success'=>$db_success));
die();
?>