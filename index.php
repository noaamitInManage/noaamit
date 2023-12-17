<?php
date_default_timezone_set('Asia/Jerusalem');
/*
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized';
    exit;
} else {
    if(empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!='inmanage' ||empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!='inmanage' ){
    	header('WWW-Authenticate: Basic realm="My Realm"');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Unauthorized';
	    exit;
    }
}
*/
// dont start the session for BOTs
$bots = array('bot', 'yahoo', 'msn', 'google', 'rss', 'feed', 'crawl', 'slurp', 'monitis', 'synapse', 'picmole', 'toata', 'java\/1.6.0_23', 'morfeus', 'findlinks', 'mon.itor.us', 'facebookexternalhit', 'netnir.co.il');
if (!preg_match('/' . implode("|", $bots) . '/', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    session_start();
} else {
    // bot is here
}
unset($_SESSION['api']['methods_limit']);
/*$_SESSION['lang']='he';
$_SESSION['lang_id']=1;*/

$mobile_path = $_SERVER['DOCUMENT_ROOT'] . '/mobile/';

include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection

include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions

include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
include_once($_project_server_path . $_salat_path . $_includes_path . "metaindex.php"); // load meta functions
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions   TODO: [CHANGE THE 'site' TO SITE NAME]
//include_once($_project_server_path . "index.class.include.php");  // load class index


include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/static_modules.inc.php"); // $staticModuleNameArr
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/links.inc.php"); // load Array of links
include_once($_SERVER['DOCUMENT_ROOT'] . "/_static/modules.inc.php"); // load Array of modules
// 11/01/2017 - netanel: redirect links. manage from salat
apply301();

Csrf::init();

$Seo = new Seo();
$Broswer = new Browser();

$Setting = new generalSettingsManager(1);
$enable_mobile_site = ($Setting->active) ? true : false;
if (($Broswer->isMobile()) && (@$_REQUEST['platform'] != 'pc') && ($enable_mobile_site)) {
    $SeoMobile = new SeoMobile();
    $SeoMobile->load_mobile_extra_url();
    $user_platform = (cookieManager::getCookie('user_platform')) ? (cookieManager::getCookie('user_platform')) : '';
}

$res = meta_checkURLAlias();
$resSeo = $Seo->parseUrl($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);

//die('<hr /><pre>' . print_r(array($res,$resSeo), true) . '</pre><hr />');// debug
if ($resSeo) {
    $res['result'] = 200;
    $res['module_id'] = $resSeo['mdlId'];
    $res['inner_id'] = $resSeo['objID'];
    if ($resSeo['additionalData']['redirect']['404'] == true) {
        $res['result'] = 400;
    }
}
if ($res['result'] == '301') {
    header_301($_system_url . str_replace("_SLASH_", "/", urlencode(str_replace("/", "_SLASH_", $res['href']))));
} else if ($res['result'] == '400') {
    //header("Location:/404?l=1");
    if (!headers_sent()) {
        Seo::header_404('404');
        //exit(header("location: /404"));
    }
    $res['module_id'] = 4;
    $res['inner_id'] = 404;
}

$mdlID = $res['module_id'];
$objID = $res['inner_id'];
$mdlName = ($resSeo) ? $resSeo['mdlName'] : getModuleName($mdlID, $objID);

$print_link = 'http://' . $_SERVER['HTTP_HOST'] . '/' . getMetaLink(4, 300) . "?mdl_id=$mdlID&post_id=$objID";
if ($mdlName == '') {
    $redirect = getMetaLink(0, 0);
    header_301($redirect);
}
// create media static files
if ($caching_isOn) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_media/css/index.css.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_media/js/index.js.php');
}
//[action]


//die('<hr /><pre>' . print_r($Seo->getUrl(array('mdl_id'=>22),2), true) . '</pre><hr />');  load tour id = 2
//$User = new User(); // if user not login $User->id=0

if ($enable_mobile_site) {
    if (isset($_REQUEST['platform']) && ($_REQUEST['platform'])) {
        cookieManager::setCookie('user_platform', trim($_REQUEST['platform']), 86400);
    }

    if (((($Broswer->isMobile())) && ($user_platform != 'pc') && (!in_array($mdlName, $not_allowed_mobile_moduleArr)))) {
        include($mobile_path . 'index.php');
        exit();
    }
}
$User = User::getInstance();
$modules_folder = siteFunctions::get_modules_folder();
header('Content-type: text/html; charset=utf-8');
include_once($_project_server_path . $modules_folder . '/' . $mdlName . ".mdl.php");
?>