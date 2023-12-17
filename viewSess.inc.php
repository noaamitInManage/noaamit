<?

//phpinfo();
//exit();
// gal test
   ////

session_start();
 //gal test
//aaa
$ipWhiteList = array(
	'62.219.212.139', // office
	'207.232.22.164', // office
	'81.218.173.175', // wireless
);

if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
    $HTTP_X_FORWARDED_FORArr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $_SERVER['REMOTE_ADDR'] = $HTTP_X_FORWARDED_FORArr[0];
} else {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if(in_array($_SERVER['REMOTE_ADDR'],$ipWhiteList)){
	echo '<pre>SESSION:';
	print_r($_SESSION);
	echo '</pre>';
	echo '<hr /><pre>' . print_r($_SERVER, true) . '</pre><hr />';

	echo '<pre>SERVER:';
	print_r($_SERVER);
	echo '</pre>';


	phpinfo();
}