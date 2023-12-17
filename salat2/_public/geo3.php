<?php 
error_reporting(0);

if (!class_exists('configManager')) {
    include($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/configManager.class.inc.php'); // configManager
}

$point = iconv('windows-1255', 'utf-8', $_REQUEST['point']); 
$point = $_REQUEST['point'];

$_REQUEST['show_link']  = ($_REQUEST['show_link']==0) ? $_REQUEST['show_link'] : 1 ;

if(!isset($_REQUEST['show_link'])){
   $_REQUEST['show_link']=1;
}
$lat=0;
$lng=0;

if($_REQUEST['point']){$_REQUEST['point']=urlencode($_REQUEST['point']);
   $httpRequest="http://maps.googleapis.com/maps/api/geocode/json?address={$_REQUEST['point']}&sensor=true&key=<?= configManager::$google_maps_api_key ?>";
   $locationLatLng=file_get_contents($httpRequest);
   $locationLatLng=json_decode($locationLatLng,true);
   $locationLatLng=$locationLatLng['results'][0]['geometry']['location'];

 $lat=$locationLatLng['lat'];
 $lng=$locationLatLng['lng'];

}
if($_REQUEST['points']){
   
 list($lat,$lng)=explode(',',$_REQUEST['points']);
   $lat=($lat) ? $lat :2;
   $lng=($lng) ? $lng :2;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<title>מצא נקודה</title>
		<?// old KEY = ABQIAAAAlpRTdPIaC-IPt39LoNx14hQyXt_S02zRMJtuRvw1TzKt9amCnBQ_HMabK7cyw7gQh8WuPbntMnZr5g  ?>
      <style type="text/javascript">
         #window_wrapper{width:600px; height:400px;}
      </style>
      <script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=true" type="text/javascript"></script>
		<script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>
		<script type="text/javascript" src="/salat2/_public/json.js"></script>
		<script type="text/javascript">
	   var gps_point =[<?=$lat;?>,<?=$lng;?>];
	   
		function initialize() {
	        var myLatlng = new google.maps.LatLng(<?=$lat;?>, <?=$lng;?>);
	     
	        var myOptions = {
	          zoom: 14,
	          center: myLatlng,
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	        }
	        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	        $("#map_canvas").css({width:'600px',height:"400px"});
	        
	        var marker = new google.maps.Marker({
	            position: myLatlng, 
	            map: map, 
	            title:"<?=$_REQUEST['point'];?>"
	        });   
      }
      function loadScript() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize&language=he&key=<?= configManager::$google_maps_api_key ?>";
        document.body.appendChild(script);
      }
	function sendBack(data) {
		if(data) {
			data = JSON.stringify(data);
		} else {
			data = '';
		}
		window.opener.document.getElementById('gps').value = data;
	}
  	$(function() {
  		 loadScript();
  		 		$('#close').click(function(event) {
					sendBack(gps_point);
					window.close();
				});
				
				$('#error').click(function() {
					sendBack(false);
					alert('אנא נסה לכתוב את הכתובת מחדש');
					window.close();
				});
  		 
  
  		 
  		 
  	});


		
		</script>
		
 </head>
  <body>
   <div id="window_wrapper">
    <div id="map_canvas"></div>
    
     <? if ($_REQUEST['show_link']==1){?>
  		<div>
  			האם זו הנקודה?
	  		<a href="#" id="close">
	  			כן (סגור)
	  		</a> | 
	  		<a href="#" id="error">
	  			לא
	  		</a>
  		</div>
  		<? } ?>
    </div>
  </body>
</html>
		
