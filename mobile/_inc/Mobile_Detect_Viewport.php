<?php

require($_SERVER['DOCUMENT_ROOT'].'/mobile/_inc/Mobile_Detect.php');
$detect = new Mobile_Detect();
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if ($detect->isMobile()) {


	if($detect->isBlackBerry()){
		$viewport = '';
	}
	else if($detect->isiPhone()){
		//$viewport = '<meta name="viewport" content="width=device-width,initial-scale=0.5,maximum-scale=0.5">';
		$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no">';

	}
	else if($detect->isHTC()){

	}

	else if($detect->isNexus()){
		$viewport = '<meta name="viewport" content="width=300,user-scalable=no, target-densitydpi=800" />';
		$viewport = '<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=800,user-scalable=yes" />';

		//$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
	}

	else if($detect->isDellStreak()){

	}

	else if($detect->isMotorola()){

	}

	else if($detect->isSamsung()){

		if(strstr($user_agent,'GT-I910')) // samsung s2
		{
			//$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

			$viewport = '<meta name="viewport" content="width=300,user-scalable=no, target-densitydpi=600" />';
		}
		else
		{
			//$viewport = '<meta name="viewport" content="width=480px,heigh=800px,initial-scale=0.5,maximum-scale=1.0">'; //original
			$viewport = '<meta name="viewport" content="width=300,user-scalable=no, target-densitydpi=400" />';
		}


	}

	else if($detect->isSony()){

	}

	else if($detect->isAsus()){

	}

	else if($detect->isPalm()){

	}

	else if($detect->isGenericPhone()){

	}
	else{

		if(strstr($user_agent,'GT-I930')){ //samsung s3
			$viewport = '<meta name="viewport" content="width=400,user-scalable=no, target-densitydpi=800" />';
		}
		else{
			$viewport = '<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />';

		}

	}
}else{
	// redirect to desktop site
	$viewport = "";

}
if(!$viewport){
	$viewport='<meta id="viewport" name="viewport">';
	$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no">';
}
//$viewport = '<meta name="viewport" content="width=device-width,initial-scale=0.5,maximum-scale=0.5">';
//mail('netanel@inmanage.net','mail - '.__FILE__,$viewport,'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
//<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no">
?>