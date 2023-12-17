<?php
error_reporting(0);
if (!class_exists('configManager')) {
	include($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/configManager.class.inc.php'); // configManager
}
$json = $_REQUEST['json'] ? urldecode($_REQUEST['json']) : "" ;

$gpsNavigation = $_REQUEST['gpsNavigation'] ? $_REQUEST['gpsNavigation'] : "32.23232, 34.232323" ; // israel

if(isset($_REQUEST['type']) && $_REQUEST['type'] == "iframe") {
	$type = "iframe";
} else {
	$type = "edited";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Drawing tools</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<style>
	/*
	#map-canvas, html, body {
		padding: 0;
		margin: 0;
		height: 100%;

	}

	#panel {
		width: 330px;
		font-family: Arial, sans-serif;
		font-size: 13px;
		float: right;
		margin: 10px;
	}
	*/

	html, body {
		padding: 0;
		margin: 0;
		height: 100%;
		width: 100%;
		position: absolute;
	}
	#map-canvas{
		padding: 0;
		margin: 0;
		height: 100%;
		width: 80%;
		position: relative;
	}

	#panel {
		width: 20%;
		font-family: Arial, sans-serif;
		font-size: 13px;
		float: right;
		margin: 10px;
		position: relative;
	}

	#color-palette {
		clear: both;
	}

	.polygon_div {
		margin-bottom: 5px;
		text-align: center;
		border: 1px black solid;
	}
	.toggleTablePolygon {
		cursor: pointer;
		float: right;
		margin-right: 5px;
	}
	.coordinatesInput {
	}

	#general_buttons {
		bottom: 100px;
		text-align: center;
		/*position: absolute;*/
		width: 330px;
		margin: auto;
	}

	button {
		margin-top: 5px;
	}
</style>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=<?= configManager::$google_maps_api_key ?>&libraries=drawing,geometry"></script>
<script>

drawingManager = null;
var coordinates = [];
var polygonsArr = [];
var polygonsOutputArr = [];
var colorsArr = ['#1E90FF','#FF5757','#32CD32','#808080','#FF8C00'];
var namesBycolorsArr = Array();
namesBycolorsArr['#1E90FF'] = '1';
namesBycolorsArr['#FF5757'] = '2';
namesBycolorsArr['#32CD32'] = '3';
namesBycolorsArr['#808080'] = '4';
namesBycolorsArr['#FF8C00'] = '5';
var selectedShape;
PolygonObj = new Polygon();


google.maps.event.addDomListener(window, 'load', PolygonObj.initialize);

// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};

function Polygon() {

	var Scope = this;
	this.amountPolygon = 1;
	this.map;
	this.drawingManager;
	this.drawingJsonManager;

	/* set of Polygon and map */
	this.initialize = function() {
		var mapOptions = {
			center: new google.maps.LatLng(<?=$gpsNavigation?>), /* Israel */
			zoom: 8
		};

		Scope.map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

		Scope.drawingManager = new google.maps.drawing.DrawingManager({
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_CENTER,
				drawingModes: [
					google.maps.drawing.OverlayType.POLYGON
				]
			},
			polygonOptions: {
				fillColor: colorsArr[0],
				editable: true,
				strokeWeight: 0,
				clickable: false
			}

		});

		var branchPosition = new google.maps.LatLng(<?=$gpsNavigation?>);
		var marker = new google.maps.Marker({
			position: branchPosition,
			draggable:true,
			animation: google.maps.Animation.DROP,
			title:"נקודת הסניף",
			icon: 'http://tran.mcdonalds.co.il/_media/images/mcdonalads_point.png'
		});
		// To add the marker to the map, call setMap();
		marker.setMap(Scope.map);

		/* Drawing Polygons */
		Scope.drawingManager.setMap(Scope.map);

		/* Listener. If Polygon ready. */
		google.maps.event.addListener(Scope.drawingManager, 'overlaycomplete', function(event) {
			var overlayObj = event.overlay;
			Scope.makeOverlay(overlayObj);
		});

		// Clear the current selection when the drawing mode is changed, or when the
		// map is clicked.
		google.maps.event.addListener(Scope.drawingManager, 'drawingmode_changed', PolygonObj.clearSelection);
		google.maps.event.addListener(Scope.map, 'click',	PolygonObj.clearSelection);

	}

	this.makeOverlay = function(overlayObj) {
		var d = new Date();
		var id = d.getTime(); // overlayObj.__gm_id;
		var color = overlayObj.fillColor;
		var coordinates = overlayObj.getPath();
		var newShape = overlayObj;

		google.maps.event.addListener(coordinates, 'set_at', function() {
			Scope.build_html_coordinates(id,coordinates);
			Scope.saveCoordinatesToArray(id,newShape,color,coordinates);
			PolygonObj.setSelection(newShape);
		});

		google.maps.event.addListener(coordinates, 'insert_at', function() {
			Scope.build_html_coordinates(id,coordinates);
			Scope.saveCoordinatesToArray(id,newShape,color,coordinates);
			PolygonObj.setSelection(newShape);
		});

		google.maps.event.addListener(newShape, 'click', function() {
			PolygonObj.setSelection(newShape);
		});
		PolygonObj.setSelection(newShape);

		Scope.saveCoordinatesToArray(id,newShape,color,coordinates);

		PolygonObj.build_html_menu(id,color,coordinates);
		PolygonObj.amountPolygon++;
		PolygonObj.PolygonCounter();
		var keyColorToDelete = colorsArr.indexOf(color);
		colorsArr.splice(keyColorToDelete, 1);
		PolygonObj.selectColor();
	}

	this.saveCoordinatesToArray = function(id,newShape,color,coordinates) {
		var encodedPath = google.maps.geometry.encoding.encodePath( coordinates ).replace(/\\/g,'\\\\');
		polygonsArr[id] = newShape;
		polygonsOutputArr[namesBycolorsArr[color]] = {'color': color,'encodedPath': encodedPath};
		PolygonObj.drawingManager.setDrawingMode(null);

	}

	this.clearSelection = function() {
		if (selectedShape) {
			selectedShape.setEditable(false);
			//selectedShape = null;
		}
	}

	this.setSelection = function(shape) {
		PolygonObj.clearSelection();
		selectedShape = shape;
		shape.setEditable(true);
	}

	this.deleteSelectedShape = function() {
		if (selectedShape) {
			var color = selectedShape.fillColor;
			delete polygonsOutputArr[namesBycolorsArr[color]];
			Scope.addColor(color);

			selectedShape.setMap(null);
			Scope.amountPolygon--;
			//PolygonObj.clearSelection();
		}
		Scope.PolygonCounter();
	}

	this.addColor = function(color) {
		colorsArr.push(color);
		Scope.selectColor();
	}
	this.selectColor = function() {
		var color =  colorsArr[0]; // colorsArr.shift();
		if(typeof color !== 'undefined'){
			Scope.setColor(color);
		}
	}

	this.setColor = function(color) {
		var polygonOptions = Scope.drawingManager.get('polygonOptions');
		polygonOptions.fillColor = color;
		Scope.drawingManager.set('polygonOptions', polygonOptions);
	}

	this.changeCoordinates = function(triangleCoords) {
		if (selectedShape) {
			selectedShape.setPaths(triangleCoords);
		}
	}

	this.build_html_menu = function(id,color,coordinates) {
		$('<div/>', {
			id: "pol_"+id,
			class: 'polygon_div',
			style: 'background-color: '+color+';width: 100%;'
		}).append(
				$('<span/>', {
					text: "פוליגון מספר "+namesBycolorsArr[color]
				}),
				$('<span/>', {
					class: "toggleTablePolygon",
					text: " + "
				}),
				$('<table/>', {
					//text: "פוליגון מספר "+this.amountPolygon,
					id: "pol_table_"+id,
					class: 'table_coordinates',
					style: 'display: none;'
				})
			).appendTo('#polygon_wrapper_'+namesBycolorsArr[color]);

			Scope.build_html_coordinates(id,coordinates);

			$('<div/>').append(
				$('<button/>', {
					id: 'pol_'+id+'_'+'saveButton',
					class: 'saveButton '+'pol_'+id,
					text: 'שמור'
				}),
				$('<button/>', {
					id: 'pol_'+id+'_'+'deleteButton',
					class: 'deleteButton '+'pol_'+id,
					text: 'מחק'
				})
			).appendTo('#pol_'+id);


	}
	this.build_html_coordinates = function(id,coordinates) {
		$("#pol_table_"+id).html("");
		$("<tr><td>#</td><td>lat</td><td>lng</td></tr>").appendTo("#pol_table_"+id);
		for ( var i = 0; i < coordinates.length; i++ ) {
			var lat = coordinates.getAt(i).lat();
			var lng = coordinates.getAt(i).lng();
			$('<tr/>',{class: "coordinatesClass"}).append(
				$('<td/>').append(i+1),
				$('<td/>').append(
					$('<input/>', {
						id: 'pol_'+id+'_'+i+'_'+'lat',
						type: 'text',
						class: 'coordinatesInput lat '+'pol_'+id,
						value: lat,
						name: 'pol_'+id+'_'+i+'_'+'lat'
					})
				)
				,
				$('<td/>').append(
					$('<input/>', {
						id: 'pol_'+id+'_'+i+'_'+'lng',
						type: 'text',
						class: 'coordinatesInput lng '+'pol_'+id,
						value: lng,
						name: 'pol_'+id+'_'+i+'_'+'lng'
					})
				)
			).appendTo("#pol_table_"+id);
		}

	}

	/* Count amount of Polygons */
	this.PolygonCounter = function() {
		if(Scope.amountPolygon > 5) {
			Scope.drawingManager.setMap(null);
			//alert("You drawed 5 Polygons");
		} else {
			Scope.drawingManager.setMap(Scope.map);
		}
	}

	this.output_array = function() {
//		polygonsOutputArr[id] = {'color': color,'encodedPath': encodedPath};
		return polygonsOutputArr;

	}

	this.output_json = function() {
		var arr = Scope.output_array();
		return JSON.stringify(arr);
	}

	this.build_from_json = function(json){
		var arr = json;
		setTimeout(function() {
			Scope.build_from_array(arr);
		},1000);
	}

	this.build_from_array = function(arr) {
		$.each(arr, function (key, value) {
			if(value) {
				polygon = Scope.get_polygonObj_by_string(value.color,value.encodedPath);
				Scope.makeOverlay(polygon);
			}
		});
	}

	this.get_polygonObj_by_string = function(color,decodePath) {
		var mpaths = [];
		mpaths.push(google.maps.geometry.encoding.decodePath(""+decodePath));
		var polygonOptions = Scope.drawingManager.get('polygonOptions');
		polygonOptions.fillColor = color;
		polygonOptions.paths = mpaths;

		drawingJsonManager = new google.maps.Polygon(polygonOptions);
		drawingJsonManager.setMap(Scope.map);

		return drawingJsonManager;
	}
}

function send_polygons_to_parent() {

	// save to db array
	var array = PolygonObj.output_array();
	var inputString = "";

	$.each(array, function (key, value) {
		if(value) {
			polygon = PolygonObj.get_polygonObj_by_string(value.color,value.encodedPath);
			var polygonNumber = namesBycolorsArr[value.color];

			//----- Update By David 19.06.16 - fix, polygon saved with wrong points -----
			$.each($("#polygon_wrapper_"+polygonNumber).find('table').find("tr.coordinatesClass"), function (key, input) {

				var lat = $(input).find("input.lat").val();
				var lng = $(input).find("input.lng").val();

				inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lat][]" value="'+lat+'" >';
				inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lng][]" value="'+lng+'" >';
			});
			//---------------------------------------------------------------------------

//			var vertices = polygon.getPath();
//			// Iterate over the vertices.
//			for (var i =0; i < vertices.getLength(); i++) {
//				var xy = vertices.getAt(i);
//				if(xy) {
//					inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lat][]" value="'+xy.lat()+'" >';
//					inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lng][]" value="'+xy.lng()+'" >';
//				}
//			}

			/*
			old way
			var latLngArr = polygon.getPath().getArray();

			$.each(latLngArr, function (i, latLng) {
				console.log(latLng);

				if(latLng) {
					//console.log(latLng['k']+latLng['A']);
					inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lat][]" value="'+latLng['k']+'" >';
					inputString += '<input type="hidden" name="coordinates['+polygonNumber+'][lng][]" value="'+latLng['B']+'" >';
				}
			});
			*/
		}
	});
	$(window.opener.document.getElementById('inputsToGlobalTable')).html(inputString);

	var data = PolygonObj.output_json();
    $.post("/salat2/_ajax/ajax.index.php",'&file=urlencode&act=urlencode&data=' + data,function(result){
		window.opener.document.getElementById('json_polygon').innerHTML = result.output;
		window.opener.document.getElementById('iframe_polygon').src = "/salat2/_public/polygon.php?type=iframe&gpsNavigation=<?=$gpsNavigation?>&json="+result.output;
        window.close();
	},"json");

}

$(function(){

	$(document).on("click",".polygon_div",function() {
		var id = $(this).attr("id"); // 'pol_'+id
		var key = id.substr(4);
		PolygonObj.setSelection(polygonsArr[key]);
	});

	$(document).on("click",".toggleTablePolygon",function() {
		if($(this).text() == ' + ') {
			$(this).text(' - ');
		} else {
			$(this).text(' + ');
		}
		var parentDiv = $(this).closest('.polygon_div');
		$(parentDiv).find('table').slideToggle('slow');
		var id = $(parentDiv).attr("id"); // 'pol_'+id
		var key = id.substr(4);
		PolygonObj.setSelection(polygonsArr[key]);
	});

	$(document).on("click",".saveButton",function() {
		var id = $(this).attr("id"); // 'pol_'+id+'+'saveButton'
		var params = id.split('_');
		var key = params[1];
		var shape = polygonsArr[key];
		PolygonObj.setSelection(shape);

		var triangleCoords = new Array();
		$.each($("#pol_"+key).find("tr.coordinatesClass"), function (key, input) {

			var lat = $(input).find("input.lat").val();
			var lng = $(input).find("input.lng").val();

			triangleCoords.push(new google.maps.LatLng(parseFloat(lat),parseFloat(lng)));
		});
		PolygonObj.changeCoordinates(triangleCoords);
		setTimeout(function() {
			PolygonObj.saveCoordinatesToArray(key,shape,shape.fillColor,triangleCoords);
		},500);
	});

	$(document).on("click",".deleteButton",function() {

		var id = $(this).attr("id"); // 'pol_'+id+'+'saveButton'
		var params = id.split('_');
		var key = params[1];

		PolygonObj.setSelection(polygonsArr[key]);
		PolygonObj.deleteSelectedShape();
		$("#pol_"+key).remove();

		PolygonObj.drawingManager.setDrawingMode(null);
	});

	$("#panel").on("click","#save_polygons",function() {
		send_polygons_to_parent();
	});
<?
	if($json) {
?>
	PolygonObj.build_from_json(<?=$json?>);
<?
	}
	if($type == "iframe") {
?>
	$("#panel").hide();
	PolygonObj.drawingManager.setMap(null);

	<?
		}

	?>

});

</script>
</head>
<body>
<div id="panel">
	<div id="list">
		<div id="polygon_wrapper_1">

		</div>
		<div id="polygon_wrapper_2">

		</div>
		<div id="polygon_wrapper_3">

		</div>
		<div id="polygon_wrapper_4">

		</div>
		<div id="polygon_wrapper_5">

		</div>
	</div>
	<div id="general_buttons">
		<button id="save_polygons" value="true">עדכן את הפוליגונים</button>
	</div>
</div>
<div id="map-canvas"></div>
</body>
</html>