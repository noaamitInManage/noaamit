<?php

$campaignCode 		= 	''; // loaded dynamiclly
$cookieKey 			= 	'auto-redirect';
$eventName			=	'isracard_mobile'; // used for GA only
$appUrlScheme 	= 	'';
$appReferrer   =   '';


function getCookieData($key) {
	return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : NULL);
}

function setCookieData($key,$value,$expire=0) {
	$_COOKIE[$key] = $value;
	setcookie($key, $value, ($expire==0 ? time()+ 10000000 : $expire), '/',$_SERVER['HTTP_HOST']);
}

function getDeviceTypeFromUserAgent(){
		$device = 'unknown';
 
		if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad')
			|| stristr($_SERVER['HTTP_USER_AGENT'],'iphone')
			|| strstr($_SERVER['HTTP_USER_AGENT'],'iphone')) {
			$device = "ios";
		} 
		else if(stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
			$device = "android";
		}
	return $device; 
}