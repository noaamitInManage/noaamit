<?php

//project urls.
$project_live_url   =   $_SERVER['HTTP_HOST'];
$project_dev_url    =   $_SERVER['HTTP_HOST'];
$project_cdn_api    =   $_SERVER['HTTP_HOST'];

$website_url = 'http://'.$_SERVER['HTTP_HOST']; //used in mail function
$width_thumb=200;
$no_replay= "noReply@{$_SERVER['HTTP_HOST']}";
//$cityWallAbuseEmail='abuse@citywall.co.il';

$allowFileExt=array('doc','docx','pdf','tif','png','jepg','jpg','txt','zip','rar');
$allowVideoExt=array('flv','avi','mp4','wmv','mov','mpv');
$allowImgExt=array('jpg','png','bmp','jpeg','gif');

$upload_file_limit =12288000; //1024 * 1000 * 12

$user_pic_width=50;
$user_pic_height=50;
$date_string='M dS';

$monthArr = array(
	1 => 'January',
	2 => 'February',
	3 => 'March',
	4 => 'Aptil',
	5 => 'May',
	6 => 'June',
	7 => 'July',
	8 => 'August',
	9 => 'September',
	10 => 'October',
	11 => 'November',
	12 => 'December'
);

$daysArr = array(
		'1' => 'Sun',
		'2' => 'Mon',
		'3' => 'Tue',
		'4' => 'Wed',
		'5' => 'Thu',
		'6' => 'Fri',
		'7' => 'Sat',
	);

$throw_errorArr = array(
	150,// unlogin user
	250,// dont have store id
	600,// try to change order status  and rest is closed

    30, // טוקן לא קיים. יש ליצור טוקן חדש
);

$throw_to_registrationArr=array(
	430, // summer sale coupon , if user is not connect and try to scan the coupon , make him to connect and after make re call to add Coupon method
);
// on this error code throw user to menu
$throw_to_menu_errorArr = array(
	//371,// unkosher error // avner 28.4.2015
);

$phone_prefixArr = array(
    '050',
    '052',
    '053',
    '054',
    '055',
    '056',
    '057',
    '058',
    //'059',
    '077',
    '072',
);

$home_phone_prefix = array(
    '02',
    '03',
    '04',
    '08',
    '09',
    '077',
);

$dev_site_mode = false;
// todo: uncomment
/*if($_SERVER['HTTP_HOST'] === $development_url){
    $dev_site_mode = true;
}*/

$salat_view_list_limit = 100;
?>
