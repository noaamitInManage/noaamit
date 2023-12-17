<?
include($_SERVER['DOCUMENT_ROOT'].'/mobile/_inc/Mobile_Detect_Viewport.php');
$from_color='#42CCFA';
$to_color='#F5F9FB';
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
	<?//=$viewport?>

		<? if($ua = strtolower($_SERVER['HTTP_USER_AGENT'])){ //android
		if(stripos($ua,'android') !== false) { // && stripos($ua,'mobile') !== false) {
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=false" />

<script type="text/javascript">
(function(doc) {

    var addEvent = 'addEventListener',
        type = 'gesturestart',
        qsa = 'querySelectorAll',
        scales = [1, 1],
        meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

    function fix() {
        meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
        doc.removeEventListener(type, fix, true);
    }

    if ((meta = meta[meta.length - 1]) && addEvent in doc) {
        fix();
        scales = [.25, 1.6];
        doc[addEvent](type, fix, true);
    }

}(document));
</script>
<?

		}else{//iphone
?>

<?		
		}
	}
	?>
	<meta charset="utf-8">
	<title>CityWall</title>
	<style type="text/css">
		body{ background:url(/mobile/media/images/enteranceBg.jpg) top center no-repeat #f5f9fc; text-align:center; font-family:sans-serif; direction:rtl; }
		#logo{ background:url(/mobile/media/images/logo.png) top center no-repeat; background-size:contain; /*margin:100px auto;*/ margin-right:auto; margin-left:auto; /*height:340px; width:455px;*/ width:70%; }
		a{ /*background-image:url(/mobile/media/images/buttons.png);*/ background-repeat:no-repeat; background-size:contain; display:block; /*height:110px;*/ margin:0 auto 30px; /*width:480px;*/ width:80%; }	
		a.adapted{ background:url(/mobile/media/images/cityMobileBlueB.png) center top no-repeat; background-size:contain; /*background-position:0 0;*/ }
		a.regular{ background:url(/mobile/media/images/cityMobileGreenB.png) center top no-repeat; background-size:contain; /*background-position:0 -114px;*/ }
		label{ color:#868787; font-size:40px; text-align:right; display:block; width:480px; margin:30px auto 30px; }
		input[type="checkbox"]{ -webkit-appearance:none; display: inline-block; width:1.25em; height:1.25em; font-size:1em; padding:0; vertical-align:text-top; background-position:center center; background-repeat:no-repeat; -webkit-background-size:100%; background-image:url(/mobile/media/images/checkbox.png); }
		input[type="checkbox"]:checked{ background-image: url(/mobile/media/images/checkbox-checked.png); }
		.pointer{cursor:pointer;}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script type="text/javascript" src="/mobile/media/js/platform.js"></script>
</head>
<body onload="">
  <div id="logo"></div>
  <a onclick="javascript: setCookie('user_platform','mobile',3); location.href='http://m.citywall.co.il'; return false;" class="adapted pointer"></a>
  <a  onclick="javascript: setCookie('user_platform','pc',3); location.href='http://www.citywall.co.il/'; return false;" class="regular"></a>
<!--  <label>
  	<input type="checkbox" name="showAgain" id="showAgain" />
  	אל תציג מסך זה שוב
  </label>-->
  <script type="text/javascript">

  	$(function() {
  		
  	

		setTimeout(function () {
		  window.scrollTo(0, 1);
		}, 1000);
		
		$(function(){
			var heightResize = parseInt( $('#logo').width() / 1.3 );
			var buttonHeightResize = parseInt( $('a').width() / 4.3 );
			var setMargins = parseInt( heightResize / 3 );
			$('#logo').css('height', heightResize);
			$('#logo').css({ 'margin-top':setMargins, 'margin-bottom':setMargins } );
			$('a').css('height', buttonHeightResize);
			$(window).on('resize',function(){
				$('#logo').css('height', heightResize);
				$('#logo').css({ 'margin-top':setMargins, 'margin-bottom':setMargins } );
				$('a').css('height', buttonHeightResize);
			});
		});
	});
	</script>
</body>
</html>