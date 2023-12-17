<?php
//Includes ---------------------------------
include_once('../_inc/project.inc.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once ($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/metaindex.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'/_inc/modules.functions.inc.php');
include_once($_SERVER['DOCUMENT_ROOT']."/_static/links.inc.php"); // load Array of links $urlAliasArr
include_once($_SERVER['DOCUMENT_ROOT']."/_static/modules.inc.php"); // load Array of modules 

//------------------------------------------

include_once('../_inc/dblayer.inc.php');
$force_lang = default_lang;
SiteMap::create_site_map(); 

exit(header("Refresh: 2; url=../main/gsm.php"));
?>