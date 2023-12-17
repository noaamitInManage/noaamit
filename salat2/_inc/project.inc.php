<?php
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&  $_SERVER['HTTP_X_FORWARDED_FOR']){
	if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false){
		$HTTP_X_FORWARDED_FORArr = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
		$_SERVER['REMOTE_ADDR'] = $HTTP_X_FORWARDED_FORArr[0];
	}else{
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
}
include_once ($_SERVER['DOCUMENT_ROOT'] .'/_inc/site.array.inc.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
// inManages's office IPs
$officeIPArr = array(
    '207.232.22.164', // main office IP
    '62.219.212.139', // second office IP
	'81.218.173.175',
	'2.54.28.18'
);
register_shutdown_function(function () {
    if (!$error = error_get_last()) {
        return;
    }
    $error_to_records = array_sum([
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR
    ]);

    if (! (($error["type"] & $error_to_records) > 0) ) {
        return false;
    }

    Database::getInstance()->insert("tb_application_errors", [
        "server_ip" => $_SERVER["SERVER_ADDR"],
        "error_type" => $error["type"],
        "message" => $error["message"],
        "file" => $error["file"],
        "line" => $error["line"],
        "last_update" => time(),
    ]);

});

define('_project_logo_big', '/salat2/_public/logo.png');
define('_project_title_main', '' . $_SERVER['HTTP_HOST']);

define('_inmanage_ip', '207.232.22.164');
define('_sysadmin_username', 'sysadmin');
define('memcached_is_on', true);
define('memcached_default_time', 63072000); // 2 years

if (in_array($_SERVER['REMOTE_ADDR'], $officeIPArr)) {
    define('_officeIP', true);
} else {
    define('_officeIP', false);
}

//---------------------------------------------------------------------------------------------------

define('_salat_in_sub_dir', '');
define('_salat_is_multilang', true);
define('_salat_new_show', true); // to make new SALAT show with new icons

define('_project_domain_path', $_SERVER['HTTP_HOST'] . '/' . _salat_in_sub_dir);
define('_project_server_path', $_SERVER['DOCUMENT_ROOT'] . '/' . _salat_in_sub_dir);

define('_system_url', 'http://' . _project_domain_path);
define('_media_path', '_media/');
define('_images_path', _media_path . 'images/');
define('_salat_path', 'salat2/');
define('_salat_static_path', _project_server_path . _salat_path . '_static');
define('_modules_path', '_modules/');
define('_includes_path', '_inc/');
define('_layout_path', '_layout/');
define('_css_path', _media_path . 'css/');
define('_js_path', _media_path . 'js/');

define('_project_media_cdn_path', 'http://' . $_SERVER['DOCUMENT_ROOT'] . '/_media/');

define('_html_nonsecured_path', _system_url);
define('_html_secured_path', 'https://' . _project_domain_path);

define('_html_path', (isset($_SERVER['HTTPS']) ? _html_secured_path : _html_nonsecured_path));

//---------------------------------------------------------------------------------------------------

$_project_domain_path = _project_domain_path;
$_project_server_path = _project_server_path;

$_system_url = _system_url;
$_media_path = _media_path;
$_images_path = _images_path;
$_salat_path = _salat_path;
$_salat_static_path = _salat_static_path;
$_modules_path = _modules_path;
$_includes_path = _includes_path;
$_layout_path = _layout_path;
$_css_path = _css_path;
$_js_path = _js_path;

$_project_logo_big = _project_logo_big;

$_html_nonsecured_path = _html_nonsecured_path;
$_html_secured_path = _html_secured_path;

$_html_path = _html_path;

$_project_title_main = _project_title_main;

$write_logs = true;
//---------------------------------------------------------------------------------------------------


$page_keywords = $page_keywords = $page_description = '';
// load current language file

if (!isset($_SESSION['salatLangID'])) {
    $_SESSION['salatLangID'] = 2; // cms lang 1.eng 2.heb
}
include_once(_project_server_path . _salat_path . '_static/langs/' . $_SESSION['salatLangID'] . '.inc.php');
include_once(_project_server_path . _salat_path . '_inc/prj_settings.inc.php');
include_once(_project_server_path . '_static/languages.inc.php');//$languagesArr
if (isset($_REQUEST['amp;lang_id']) && ($_REQUEST['amp;lang_id'])) {
    $_REQUEST['lang_id'] = $_REQUEST['amp;lang_id'];
    unset($_REQUEST['amp;lang_id']);
}

$default_lang_id = 1; // salat
$module_lang_id = isset($_REQUEST['lang_id']) ? intval($_REQUEST['lang_id']) : $default_lang_id;  // salat

if (!isset($_REQUEST['lang_id'])) {
    $_REQUEST['lang_id'] = $module_lang_id;
}

$metaTags = array(
    'title' => '',
    'keywords' => '',
    'description' => '',
);

if(!function_exists('recArr'))
{
    function recArr(&$arr)
    {
        foreach ($arr as $key => $val)
        {
            if (is_array($val))
            {
                recArr($val);
            }
            else
            {
                $arr[$key] = stripslashes($val);
            }
        }
    }
}

recArr($_REQUEST);
recArr($_GET);
recArr($_POST);

define("LOG_FILE_PATH", $_SERVER['DOCUMENT_ROOT'] . '/_static/log_domain.txt');

//ini_set('error_reporting', E_ALL);
//error_reporting(E_ALL);
ini_set('log_errors', TRUE);
ini_set('html_errors', FALSE);
ini_set('error_log', LOG_FILE_PATH);
ini_set('display_errors', FALSE);
if (is_file(LOG_FILE_PATH) && filesize(LOG_FILE_PATH) / 1024 / 1024 > 1) {
    $logfp = fopen(LOG_FILE_PATH, "w+");
    fclose($logfp);
}

define('default_lang', 'he'); // front
define('default_lang_id', 1); // front
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'he';
    $_SESSION['lang_id'] = 1;
}
?>