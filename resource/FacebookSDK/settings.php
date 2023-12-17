<?php

if(!isset($_SESSION)) session_start();

define('_FACEBOOK_APP_ID_', '1034489929982839');
define('_FACEBOOK_APP_SECRET_', 'f42db7654bc0e58016a1bf1da9d9d67f');
define('_FACEBOOK_APP_VERSION_', 'v2.7');

global $facebook_api_response_fieldsArr;

//I the FacebookSDK not in root, please define the constant  _CUSTOM_FACEBOOK_SDK_PATH_
require_once $_SERVER['DOCUMENT_ROOT'] . (defined('_CUSTOM_FACEBOOK_SDK_PATH_') ? _CUSTOM_FACEBOOK_SDK_PATH_ : '') . '/FacebookSDK/autoloader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . (defined('_CUSTOM_FACEBOOK_SDK_PATH_') ? _CUSTOM_FACEBOOK_SDK_PATH_ : '') . '/FacebookSDK/vendor/autoload.php';

function sendFacebookSDKRequest($fb, $FbApi) {

    global $facebook_api_response_fieldsArr;

    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get($FbApi->getFbUserUrl() . '?fields=' . $FbApi->getFacebookSDKRequestFields(), $FbApi->getAccessToken());
    } catch(Facebook\Exceptions\FacebookResponseException $e) {

        $facebook_api_response_fieldsArr = 'Graph returned an error: ' . $e->getMessage();

        return $facebook_api_response_fieldsArr;
        exit();
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $facebook_api_response_fieldsArr = 'Facebook SDK returned an error: ' . $e->getMessage();

        return $facebook_api_response_fieldsArr;
        exit();
    }

    $user = $response->getGraphUser();//getting user info from facebook

    $FbApi->fb_user_response_arr = $user;//pushing to user object respinse_fields_arr, response that we got from facebook

    $FbApi->parseFacebookSDKResponseFields($user);//parsing returned request

    $FbApi->setFbUserResponseArr($user);//converting returned Object to an array

    if(is_array($FbApi->returnRequestFieldsArray()) && !empty($FbApi->returnRequestFieldsArray())) {
        $facebook_api_response_fieldsArr = $FbApi->returnRequestFieldsArray();//getting parsed User Info as associative array
    }

}