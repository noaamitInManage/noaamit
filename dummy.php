<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 25/06/13
 * Time: 11:45
 * To change this template use File | Settings | File Templates.
 */


include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
//include_once($_project_server_path.$_salat_path."_static/mainsettings.inc.php"); // load various functions
//include($_project_server_path.$_salat_path.'_inc/moduleUpdateStaticFiles.php'); //load update static class
//include($_project_server_path.$_includes_path.'UpdateStaticFiles/media.class.inc.php'); //load user class

include_once($_project_server_path.$_salat_path.'_inc/metaindex.php'); //metaindex.php
include_once($_project_server_path.'_static/links.inc.php');//$urlAliasArr
//include_once($_project_server_path."index.class.include.php");  // load class index
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions

siteFunctions::send_mail("gal@inmanage.co.il","test","aa");
ini_set('display_errors', '1');
$email = 'gal@inmanage.co.il';
echo preg_match("/^[a-z0-9_-]+[a-z0-9_.-]*@[a-z0-9_-]+[a-z0-9_.-]*\.[a-z]{2,5}$/", $email);



?>