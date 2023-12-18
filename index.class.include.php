<?php

$remote_addressArr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
if (count($remote_addressArr) > 1) {
    $_SERVER['REMOTE_ADDR'] = $remote_addressArr[0];
} else {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (!class_exists('moduleUpdateStaticFiles')) {
    include_once($_project_server_path . $_salat_path . $_includes_path . 'moduleUpdateStaticFiles.class.inc.php');
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/UpdateStaticFiles/categoriesLangsUpdateStaticFiles.class.php');
//include_once($_project_server_path . $_includes_path . 'class/Csrf.class.inc.php');//Csrf
//include_once($_project_server_path . $_includes_path . 'class/BaseManager.class.inc.php');//BaseManager
//include_once($_project_server_path . $_includes_path . 'class/MetaTagsManager.class.inc.php');//MetaTags
//
//include_once($_project_server_path . $_includes_path . 'class/Seo.class.inc.php');//Seo
//include_once($_project_server_path . $_includes_path . 'class/CookieManager.class.inc.php');//cookieManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/advertisersManager.class.inc.php');//advertisersManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/Browser.class.inc.php');//Browser
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/siteFunctions.inc.class.php');//Browser
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/generalSettingsManager.class.inc.php');//generalSettingsManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_static/errors.' . $_SESSION['lang'] . '.inc.php');//$errorsArr
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/errorManager.class.inc.php'); //errorManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/User.class.inc.php'); //User
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/Encryption.class.inc.php'); //Encryption
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/applicationConfig.class.inc.php'); //applicationConfig
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/configManager.class.inc.php'); //configManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/QueueManager.class.inc.php'); // QueueManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/GoogleMapsManager.class.inc.php'); // GoogleMapsManager
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/secureToken.class.inc.php'); // secureToken
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/cache/drivers/CacheDriver.interface.inc.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/cache/drivers/FileCacheDriver.class.inc.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/cache/drivers/MemcacheCacheDriver.class.inc.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/cache/CacheManager.class.inc.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/AppleSignInManager.class.inc.php');

//$itemsArr = scandir($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/module/');
//$excludeArr = array('.', '..');
//
//foreach ($itemsArr AS $key => $value) {
//    if (in_array($value, $excludeArr)) {
//        continue;
//    }
//    include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/module/' . $value);
//}

?>