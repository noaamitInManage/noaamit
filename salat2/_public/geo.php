<?php
if (!class_exists('configManager')) {
	include($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/configManager.class.inc.php'); // configManager
}

$point = iconv('windows-1255', 'utf-8', $_REQUEST['point']); 
$point = $_REQUEST['point'];

$_REQUEST['show_link']  = ($_REQUEST['show_link']==0) ? $_REQUEST['show_link'] : 1 ;

if(!isset($_REQUEST['show_link'])){
   $_REQUEST['show_link']=1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<title>מצא נקודה</title>
		
		<?// old KEY = ABQIAAAAlpRTdPIaC-IPt39LoNx14hQyXt_S02zRMJtuRvw1TzKt9amCnBQ_HMabK7cyw7gQh8WuPbntMnZr5g  ?>

		<script src="http://maps.google.com/maps?file=api&v=2&sensor=true<?= configManager::$google_maps_api_key ?>" type="text/javascript"></script>
		<script type="text/javascript" src="/_media/js/jquery.js"></script>
		<script type="text/javascript" src="/salat2/_public/json.js"></script>
		<script type="text/javascript">
		(function($) {
			$(function() {
				<?php if(!empty($_REQUEST['fast'])): ?>
		        window.opener.focus();
		        <?php else: ?>
		        $(window).bind('load', function() {
		        	window.focus();
		        });
		        <?php endif; ?>
				var map, geocoder, point = '<?= json_encode($point); ?>', gps_point;
				if(GBrowserIsCompatible()) {
					map = new GMap2(document.getElementById("map_canvas"));
					map.addControl(new GLargeMapControl());
		    		geocoder = new GClientGeocoder();
				}
				
				function getText(text, point) {
					return '<div style="'
						+ 'direction: rtl;'
						+ 'text-align: right;'
						+ 'padding: 0 15px;'
						+ 'font-weight: bold;'
						+ '">' + text
							+ '<br />'
							+ '<em>(' + point.lat() + ' ; ' + point.lng() + ')'
						+ '</div>';
				}
				
				function sendBack(data) {
					if(data) {
						data = JSON.stringify(data);
					} else {
						data = '';
					}
					window.opener.document.getElementById('gps').value = data;
				}
				
				function showAddress(address) {
					if(geocoder) {
						 geocoder.getLatLng(
		      				address,
		      				function(point) {
		            			if(!point) {
		              				alert('הכתובת ' + address + ' לא נמצאה');
		            			} else {
		              				map.setCenter(point, 15);
		              				var marker = new GMarker(point);
		              				map.addOverlay(marker);
		              				marker.openInfoWindowHtml(getText(address.slice(1, -1), point));
		              				gps_point = [point.lat(), point.lng()];
		              				<?php if(!empty($_REQUEST['fast'])): ?>
		              				window.opener.document.getElementById('gps_loader').style.display = 'none';
		              				sendBack(gps_point);
		              				window.close();
		              				<?php endif; ?>
		            			}
		      				}
		    			);
					}
				}
				showAddress(point);
				
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
		}(jQuery));
		</script>
		<style type="text/css">
		h1 { 
			font-size: 16px;
		}
		</style>
  	</head>
  	<body onunload="GUnload()" dir="rtl">
  		<h1><?= $point; ?></h1>
  		<div id="map_canvas" style="width: 500px; height: 300px"></div>
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
  		<? }

  		
  		 ?>
	</body>
</html>