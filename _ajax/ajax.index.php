<?php
$ajaxModulesArr=scandir((getcwd())); // gal 31/01/2012
session_start();

//  save 
$dirFilesModulesArr = array(
	'auto_complete/cities.ajax.php',
	'auto_complete/area.ajax.php',
	'auto_complete/streets.ajax.php',
	'auto_complete/neighborhoods.ajax.php',
);

$ajaxModulesArr=array_merge($ajaxModulesArr,$dirFilesModulesArr);
/*
if(!$_SESSION['lang_id']){
	$_SESSION['lang_id'] = 1;
}
$lang_id = $_SESSION['lang_id'];
*/

$clientLibraryPath = $_SERVER['DOCUMENT_ROOT'].'/_inc/vendors/';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);

include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
//include_once($_project_server_path.$_salat_path."_static/mainsettings.inc.php"); // load various functions
//include($_project_server_path.$_salat_path.'_inc/moduleUpdateStaticFiles.php'); //load update static class
//include($_project_server_path.$_includes_path.'UpdateStaticFiles/media.class.inc.php'); //load user class

include_once($_project_server_path.$_salat_path.'_inc/metaindex.php'); //metaindex.php
include_once($_project_server_path.'_static/links.inc.php');//$urlAliasArr
include_once($_project_server_path."index.class.include.php");  // load class index
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions

Csrf::init();

if (!Csrf::verify()) {
	exit(json_encode(["status" => 0, "data" => [], "err" => "Unauthorized", "msg" => "", "relocation" => "", "html" => ""]));
}

$Seo = new Seo();
$User= new User();

if(in_array($_REQUEST['file'].'.ajax.php',$ajaxModulesArr)){
	header("content-type: text/html; charset=utf-8");
	include_once($_SERVER['DOCUMENT_ROOT'].'/_ajax/'.$_REQUEST['file'].'.ajax.php');
}
exit(); 

?>  
