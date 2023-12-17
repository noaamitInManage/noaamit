<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 24/06/13
 * Time: 13:44
 * @desc : genric site api
 */

header("Access-Control-Allow-Origin: *");

session_start();
$qArr=explode('/',$_SERVER['REQUEST_URI']);
include_once ($_SERVER['DOCUMENT_ROOT'] .'/_static/react_modules.inc.php'); //$react_modulesArr
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/static_modules.inc.php"); // $staticModuleNameArr
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_SERVER['DOCUMENT_ROOT'] . "/_inc/class/api/ApiHelpers.inc.php"); // $staticModuleNameArr
include_once($_SERVER['DOCUMENT_ROOT'] . "/_inc/class/api/SendsApiResponses.inc.php"); // $staticModuleNameArr
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
//include($_project_server_path.$_salat_path.'_inc/moduleUpdateStaticFiles.php'); //load update static class
//include_once($_project_server_path."index.class.include.php");  // load class index
//include($_project_server_path.$_includes_path.'class/apiManager.class.inc.php');//apiManager
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
$Browser= new Browser();
/**
 * $platform =  iphone | android
 * $version =  1.0 | 1.1 ...
 * $method_name =  getContent | setBranch
 */

list($_,$_,$platform,$version,$method_name)=$qArr;
$Api = new apiManager($platform,$version,$method_name);
$answer = $Api->execute();
exit($answer);
?>