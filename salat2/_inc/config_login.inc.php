<?php 

/************************************************************************
   
   This script is used as config.inc.php, but only for the login 
   page becuase it needs to check for logout request
   
***********************************************************************/

// start session
session_start();

// load project settings
include("project.inc.php");

// do logout
if (isset($_GET['logout']) && $_GET['logout']=="yes"){
	$_SESSION['salatUserID'] = "";
	$_SESSION['salatUserUName'] = "";
	$_SESSION['salatUserFName'] = "";
	$_SESSION['salatUserEmail'] = "";
	unset($_SESSION['salatUserID']);
	unset($_SESSION['salatUserUName']);
	unset($_SESSION['salatUserFName']);
	unset($_SESSION['salatUserEmail']);
	header("location:".$_html_nonsecured_path.$_salat_path."login.php?msg=2&skip_auto_login={$_REQUEST['skip_auto_login']}");
	exit();
}

// check for user login
if (isset($_SESSION['salatUserID']) && $_SESSION['salatUserID']!=""){ echo "<script>top.document.location = '".$_html_nonsecured_path.$_salat_path."frames.php';</script>"; exit(); }

// set $vars
$vars = array_merge($_GET,$_POST);

// Test '$vars' For SQL Hacking

if(isset($_POST['txtUser']) && isset($_POST['txtPass'])) {
	
	if ((strpos($_POST['txtUser']," or ")) || (strpos($_POST['txtPass']," or ")) || (strpos($_POST['txtUser'],"group by")) || (strpos($_POST['txtPass'],"group by"))){
		die("<font size=5 font=arial color=red>Hacking Detected !</font>");
	}
	
}

// load and open db connection
include ($_project_server_path.$_salat_path."_inc/dblayer.inc.php");

?>