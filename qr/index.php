<?php
$_SESSION['lang']='he';
$_SESSION['lang_id']=1;
$mobile_path=$_SERVER['DOCUMENT_ROOT'].'/mobile/';
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."mobile_html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
include_once($_project_server_path.'/salat2/'.$_includes_path."metaindex.php"); // load meta functions
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions   TODO: [CHANGE THE 'site' TO SITE NAME]

include($_project_server_path."index.class.include.php");  // load class index
include_once($_SERVER['DOCUMENT_ROOT']."/_static/static_modules.inc.php"); // $staticModuleNameArr
include_once($_SERVER['DOCUMENT_ROOT']."/_static/links.inc.php"); // load Array of links

include_once($_SERVER['DOCUMENT_ROOT']."/_static/modules.inc.php"); // load Array of modules

require 'shared.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/class/browser.class.inc.php'); //Browser


/* --------------------------------------------------------- */
/* ----------- Custom application variables ---------------- */
/* --------------------------------------------------------- */

/* application url scheme name */
$appUrlScheme = ''; //'imcdonalds';
/* store link for android */
$fallbackAndroidLink = ''; //'market:\/\/details?id=il.co.inmanage.mcdonalds';
/* store link for iphone */
$fallbackIphoneLink = ''; //'https:\/\/itunes.apple.com\/us\/app\/mqdwnlds-mcdonalds-israel\/id967762450?ls=1&mt=8';
/* store application package id */
$appPackage = ''; //'il.co.inmanage.mcdonalds';

/* --------------------------------------------------------- */
/* ---------- End custom appliction variables ---------------*/
/* --------------------------------------------------------- */


if($appUrlScheme == '' || $fallbackAndroidLink == '' || $fallbackIphoneLink=='' || $appPackage==''){
	die("Please define the custom application variables");
}

if(isset($_REQUEST['c']) && $_REQUEST['c']){
	$campaignCode=intval(trim($_REQUEST['c']));
}

$Campaign = null;
if($campaignCode){
	$Campaign = customer_campaignManager::get_campaign_by_code($campaignCode);
	if($Campaign->active == 0){
		$Campaign = null;
	}
}

$qs = $_SERVER['REQUEST_URI'];
$qs = explode("/",$qs);

$referer = (isset($_REQUEST['reffrer'])) ? ($_REQUEST['reffrer']) :  '';

$eventName = (isset($_REQUEST['event'])) ? ($_REQUEST['event']) :  '';
$appReferrer = ($qs[4]) ? ($qs[4]) :  $appReferrer;
$referer=str_replace('?','&',$referer);

if($campaignCode){
	$cookieData = "{$appUrlScheme}://{$campaignCode}/{$appReferrer}";
}else{
	$cookieData = "{$appUrlScheme}://{$appReferrer}";
}

$ex_paramArr=array();
$ex_param =  (isset($_REQUEST['ex']) && $_REQUEST['ex']) ? "?".trim($_REQUEST['ex']) : '';
foreach($_GET AS $key=>$value){
	$value = ($key == "c" ? intval($value) : $value);
	$ex_paramArr[]="{$key}={$value}";
}

if(count($ex_paramArr)){
	$ex_param .='?'.implode('&',$ex_paramArr);
}
$cookieData='gal_test';
setCookieData($cookieKey,$cookieData.'###'.$eventName);


$iphone_caseArr=array(
	'iphone',
	//'ios',
	'ipad'
);
$isiOS=false;
foreach($iphone_caseArr AS $key=>$value){
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']),'iphone')){
		$isiOS = true;
	}
}
?>

<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=300,user-scalable=no, target-densitydpi=600">
	<meta charset="utf-8">
	<title>App Redirection</title>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<style>
		body { margin: 0; padding: 0; }
		.imgWrapper { margin: 0 auto; width: 100%; max-width: 1000px; }
		.imgWrapper img{ width: 100%;}
		.container{
			direction: rtl;
			text-align: center;
			padding-top: 3em;
			width: 80%;
			margin: 0 auto;
			font-family: arial;
		}
		h2{
			font-size: 1.2em;
		}
		a{
			font-size: 1em;
			color: #000000;
			margin-top: 2em;
			max-width: 45%;
		}
		a:nth-of-type(1){
			float: right;
		}
		a:nth-of-type(2){
			float: left;
		}
	</style>
</head>
<body>

<!-- iframe used for attempting to load a custom protocol -->
<iframe style="display:none" height="0" width="0" id="loader"></iframe>

<script type="text/javascript">
	var fallbackLink;
	var applictionUrl = "<?=$appUrlScheme;?>://<?=$ex_param;?>";
	var g_intent = "intent://scan/<?=$ex_param;?>#Intent;scheme=<?=$appUrlScheme;?>;package=<?=$appPackage?>;end";
	var timer;
	var heartbeat;
	var iframe_timer;
	var timer2;

	/*redirect for iPhone */
	function redirect_immediately(fallbackLink){
		window.location.replace("<?=$appUrlScheme;?>://<?=$ex_param;?>");
		var z2=setTimeout(function(){
			window.location.replace(fallbackLink);
		},2000);
	}

	/* cleear all timers */
	function clearTimers() {
		clearTimeout(timer);
		clearTimeout(heartbeat);
		clearTimeout(iframe_timer);
		clearTimeout(timer2);
	}

	/* check if document is hidden */
	function intervalHeartbeat() {
		if (document.webkitHidden || document.hidden ) {
			clearTimers();
		}
	}

	/* open application or store by appending an iframe */
	function tryIframeApproach() {
		timer2 = setTimeout(function(){
			document.location = fallbackLink;
		},5000);
		var iframe = document.createElement("iframe");
		iframe.setAttribute('id', 'app_iframe');
		iframe.style.border = "none";
		iframe.style.width = "1px";
		iframe.style.height = "1px";
		iframe.src = applictionUrl;
		document.body.appendChild(iframe);
	}

	/* open application or store by document.location */
	function tryWebkitApproach() {
		document.location = applictionUrl;
		timer = setTimeout(function () {
			document.location = fallbackLink;
		}, 3000);
	}

	/* open application or store by intent if not supported showes a message */
	function useIntent() {
		document.location = g_intent;
		iframe_timer = setTimeout(function(){

			/*for intent not supported */
			$("body").html('<div class="container"><h2>בחר אחת מהאפשרויות הבאות:</h2><a href="'+applictionUrl+'">לאפליקציה מותקנת</a><a href="'+fallbackLink+'">לחנות האפליקציות</a></div>');

		}, 1500);
	}

	/* try to open application or store by different ways */
	function launch_app_or_alt_url(el) {
		heartbeat = setInterval(intervalHeartbeat, 200);
		if (navigator.userAgent.match(/Chrome/)) {
			useIntent();
		} else if (navigator.userAgent.match(/Firefox/)) {
			tryWebkitApproach();
			iframe_timer = setTimeout(function () {
				tryIframeApproach();
			}, 1500);
		} else {
			//tryIframeApproach();
			useIntent();
		}
	}

	(function(){
		// For desktop browser, remember to pass though any metadata on the link for deep linking
		//fallbackLink = 'http://order.mcdonalds.co.il/mobile/';

		var isiOS = <?=($isiOS) ?'true' : 'false';?>;
		var   isAndroid = '';
		if(!isiOS){
			isAndroid = true;
			isiOS= false;
		}


		// Mobile
		if (isiOS || isAndroid) {
			fallbackLink = (isiOS)   ? '<?=$fallbackIphoneLink?>'  : '<?=$fallbackAndroidLink?>'  ;

			/* android redirects */
			if (isAndroid) {
				<? if(!isset($Campaign->id) || !$Campaign){?>
					launch_app_or_alt_url(null);
				<? } ?>

				<? if($Campaign->open_direct){ ?>
					setTimeout(function(){
						launch_app_or_alt_url(null);
					},3000);
				<? } else { ?>
					$("body").on("click",".imgWrapper",function(event){
						event.preventDefault();
						launch_app_or_alt_url(null);
					});
				<? } ?>


				/*iphone redirects*/
			} else {
				<? if(!isset($Campaign->id) || !$Campaign){ ?>
					redirect_immediately(fallbackLink);
				<? } ?>

				<? if($Campaign->open_direct){ ?>
					var z=setTimeout(function(){
						window.location.replace("<?=$appUrlScheme;?>://<?=$ex_param;?>");
					},1000);

					var z2=setTimeout(function(){
						window.location.replace(fallbackLink);
					},3000);
				<? } else { ?>
					$("body").on("click",".imgWrapper",function(){
						redirect_immediately(fallbackLink);
					});
				<? } ?>
			}
		}


		<? if($Campaign->google_analytics_code != "" && isset($Campaign->google_analytics_code)){ ?>
			//console.log(<?=$Campaign->google_analytics_code?>);
		<? } ?>

	})();
</script>
<script>
	/*(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-59839990-2', 'auto');
	ga('send', 'pageview');*/

</script>
<div class="imgWrapper">
	<? if(isset($Campaign->mediaObj->path)){ ?>
		<img src="<?=$Campaign->mediaObj->path?>" alt="<?=$Campaign->mediaObj->alt?>" >
	<? } ?>
</div>
</body>
</html>