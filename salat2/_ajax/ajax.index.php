<?php

$ajaxModulesArr = array(
	"excel_web_service",
	"auto_complete/media_category",
	"auto_complete/mediaItems",
	"auto_complete/cities",
	"auto_complete/category",
	"auto_complete/questions",
	"auto_complete/user",
	"auto_complete/company",
	"user_contact",
	"content_management_service",
	"db_sync",
	"home_page_category_order",
	"getGalleryItems",
	"category",
	"post.service",
	"media",
	"agencies",
	"translations",
	"errors",
	"cg_errors",
	"auto_complete/agencies",
	"auto_complete/popular",
	"footer_items",
	"auto_complete/tour-destinations",
	"urlencode",
	"example_service",
	"user_service",
	"feature_flags",
	"company",
	"meeting_rooms",
	"sites",
	"users",
	"module_management_service",
	"get_methods",
);

session_start();
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  			// load module array
include_once($_project_server_path.$_includes_path."citywall.array.inc.php");  			// load module array
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  		// load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); 	// load various functions
include_once($_project_server_path."_static/settings.inc.php");
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
// load various settings
error_reporting(E_ALL);
ini_set('display_errors', '1');

if(in_array($_REQUEST['file'],$ajaxModulesArr)){
	header("content-type: text/html; charset=utf-8");
    include_once($_SERVER['DOCUMENT_ROOT'].'/'.$_salat_path.'_ajax/'.$_REQUEST['file'].'.ajax.inc.php');
}
exit();

?>
