<?
session_start();
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); 				// load server, domain paths
include_once($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/dblayer.inc.php'); 					// load database connection

include_once($_project_server_path."_static/settings.inc.php"); 
include("_inc/config_login.inc.php");
include($_project_server_path.$_salat_path."_inc/functions.inc.php");
include($_project_server_path.$_salat_path."_inc/captcha.functions.inc.php");
include($_project_server_path.$_salat_path."_static/languages.inc.php");


	$chars = array();
	for($i=0;$i<5;$i++) $chars[] = chr(rand(65,90));
	for($i=0;$i<10;$i++) $chars[] = chr(rand(97,122));
	shuffle($chars);
	$key = implode('',$chars);
	
	$secret_key = str_replace(array('=','/','+'),'',base64_encode(hash('SHA256', $key, true)));
	
	$_SESSION['_salat_v2_hk'] = $secret_key; 



//$text=urldecode($text);
$options = array(
	'background_image'=>'',
	'text_color'=>'white',
	'text_border'=>'black',
	'font'=>'',
	'font_size'=>'55',
);


$numbers = array(
	'heb'=>array(
		0=>'אפס',
		1=>'אחד',
		2=>'שתיים',
		3=>'שלוש',
		4=>'ארבע',
		5=>'חמש',
		6=>'שש',
		7=>'שבע',
		8=>'שמונה',
		9=>'תשע'
	),
	'eng'=>array(
		0=>'zero',
		1=>'one',
		2=>'two',
		3=>'three',
		4=>'four',
		5=>'five',
		6=>'six',
		7=>'seven',
		8=>'eight',
		9=>'nine'
	),
);


$num = rand(0,9);
$rand_2 = rand(1,30);
$captcha_str = $numbers['heb'][$num].' + '.$rand_2;
$captcha_value = $num+$rand_2;




unset($_SESSION['cpt']);
$cpt = array(
	 'w'=>$numbers['heb'][$num],
	 'n'=>$rand_2,
	 'r'=>$captcha_value
);
function mb_strrev($text, $encoding = null)
{
    $funcParams = array($text);
    if ($encoding !== null)
        $funcParams[] = $encoding;
    $length = call_user_func_array('mb_strlen', $funcParams);

    $output = '';
    $funcParams = array($text, $length, 1);
    if ($encoding !== null)
        $funcParams[] = $encoding;
    while ($funcParams[1]--) {
         $output .= call_user_func_array('mb_substr', $funcParams);
    }
    return $output;
}

$_SESSION['cpt'] = captcha_encrypt(json_encode($cpt),$_SESSION['_salat_v2_hk']);

creatCaptchaMultiLnag($captcha_str,1, $width = 168,$height = 84,$options);
?>