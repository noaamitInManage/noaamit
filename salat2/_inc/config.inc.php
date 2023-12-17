<?php

ob_start();
/*********************************************************************************************

This script is used as config_login.inc.php, but for all files other then login

 *********************************************************************************************/

// start session
session_start();

// load project settings
include_once("project.inc.php");

// check for user login
if ($_SESSION['salatUserID']==""){ echo "<script>top.document.location = '".$_html_nonsecured_path.$_salat_path."login.php';</script>"; exit(); }

// load system functions
include_once($_project_server_path.$_salat_path."_inc/functions.inc.php");
include_once($_project_server_path."_inc/class/configManager.class.inc.php");

// load and open db connection
include_once($_project_server_path.$_salat_path."_inc/dblayer.inc.php");
include_once($_project_server_path . $_includes_path . 'class/BaseManager.class.inc.php');//BaseManager


?>