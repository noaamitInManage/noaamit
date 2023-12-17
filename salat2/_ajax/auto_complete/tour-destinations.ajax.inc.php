<?
header('Content-type: text/plain; charset=utf-8');


$action=$_REQUEST['action'];
$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action){

case "getItems":

	include($_SERVER['DOCUMENT_ROOT'].'/_static/cities.search.inc.php'); // $citiesSearch

	$searchPattern = "/{$q}/i";
	foreach ($citiesSearch as $cityId => $cityText) {
		if (strpos(strtolower($cityText),strtolower($q)) > -1){
			echo $cityText .'|'. $cityId."\n";
		}
	}
	break;
case "removeTourDestination":

	$status = false;
	$cityId = intVal($_POST['city_id']);
	$tourId = intVal($_POST['tour_id']);
	$destinationRemovalSql = "DELETE FROM `tb_tours_destinations` WHERE `city_id`={$cityId} AND `tour_id`={$tourId}";
	$destinationRemovalRes = mysql_query($destinationRemovalSql);
	if ($destinationRemovalRes){
		$status = true;
	}
	echo json_encode(array('status'=>$status,'sql'=>$destinationRemovalSql));
	break;
}

?>
