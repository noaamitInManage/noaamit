<?php
if(!isset($_GET['path']))
    $_GET['path'] = '/resource/';

if(isset($_GET['path']) && !empty($_GET['path'])) {
    $path = trim($_GET['path']);
    define('_CUSTOM_FACEBOOK_SDK_PATH_', $path);
}

if (is_readable($_SERVER['DOCUMENT_ROOT'] . (defined('_CUSTOM_FACEBOOK_SDK_PATH_') ? '/'._CUSTOM_FACEBOOK_SDK_PATH_ : '') . '/FacebookSDK/settings.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . (defined('_CUSTOM_FACEBOOK_SDK_PATH_') ? '/'._CUSTOM_FACEBOOK_SDK_PATH_ : '') . '/FacebookSDK/settings.php';
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/resource/FacebookSDK/settings.php';
}


$fake_access_token = 'CAAXhboZArwn8BACPaFsByEFuUT2QlaUpOA1P1ZCHXwrR6WkZBGOIramEk1u9pizMpV5wLr988WEUBx43pWH0d5gg46ByHCy0vvzyYb5FxXficdZB8aZABWyGYWkvHwN3OJZAXHCmZADZCZBCTkE0OqJXhbSn5Yj9rzDToOC2ZBAUSoBHbnoRbG15lFEtdzD0rProHF3uie0OLQpkVeTzoZCZACVB';

//$user_access_token = 'CAAXhboZArwn8BAAcJxbuzETJwUrFKZAshimfwf3UebFDTZC4xynZAzwqERFyZAPJZCuUS7ZBtqmXl8mwK2UzM4ZBV1nfzXcAuF5NaiDqaTZCTyYC6ZBByQT8c9eye8zbTwmQ9ayY7zATXLD01pv5b2DFP9T1gcFCDxQYiEOuxtqCAeStENEUidEY18ZAgwj94rZAbo4QzopjlsHKk1JkFNm2TCSC';

/*
if ((!isset($user_access_token) || empty($user_access_token)) || (isset($user_access_token) && ($user_access_token === $fake_access_token))) {

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    try {
        throw new Exception('Error: User Access Token not SET or EXPIRED.');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    exit();
}*/

$FbApi = new modules\FacebookUser(_FACEBOOK_APP_ID_, _FACEBOOK_APP_SECRET_);
$FbApi->initFacebookSDKRequestFields()->setAccessToken($user_access_token);

if ($FbApi->getAccessToken()) {

    $fb = new Facebook\Facebook([
        'app_id'                => (($FbApi->getFbAppId())      ? $FbApi->getFbAppId()      : ((defined('_FACEBOOK_APP_ID_')      && _FACEBOOK_APP_ID_)      ? _FACEBOOK_APP_ID_      : '')),
        'app_secret'            => (($FbApi->getFbAppSecret())  ? $FbApi->getFbAppSecret()  : ((defined('_FACEBOOK_APP_SECRET_')  && _FACEBOOK_APP_SECRET_)  ? _FACEBOOK_APP_SECRET_  : '')),
        'default_graph_version' => (($FbApi->getFbAppVersion()) ? $FbApi->getFbAppVersion() : ((defined('_FACEBOOK_APP_VERSION_') && _FACEBOOK_APP_VERSION_) ? _FACEBOOK_APP_VERSION_ : '')),
    ]);

    if (!isset($prevent_auto_initialization)) {

        sendFacebookSDKRequest($fb, $FbApi);

        //If you want json
        if(isset($_GET['response']) && $_GET['response'] == 'json')
            echo json_encode($FbApi->getFbUserResponseArr(), true);
    }
}



//TODO create file that initialize the API and his functions and returning response that we want,
//TODO for example http://url_to_this_file?init=initFacebookSDKRequestFields,setAccessToken,getGraphUser
//TODO and also we can request specific fields from facebook, just add them to query params like this:
//TODO example http://url_to_this_file?fields=name,email,gender&init=initFacebookSDKRequestFields,setAccessToken,getGraphUser
//TODO all of this must be done with CURL.

?>