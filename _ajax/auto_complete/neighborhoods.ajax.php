<?

$tb_name='tb_cities';  
$_REQUEST['title']= ((!$_REQUEST['title']) && $_REQUEST['q']) ? $_REQUEST['q'] : $_REQUEST['title'];
$q = (isset($_REQUEST['title']))? $Db->make_escape(trim($_REQUEST['title'])) : '';
/*if(!$q){
	exit();
}*/

//include($_SERVER['DOCUMENT_ROOT'].'/_static/citiesFlat.inc.php');//$citiesFlatArr
include($_SERVER['DOCUMENT_ROOT'].'/_static/neighborhoodCityGroup.inc.php');//$neighborhoodCityGroupArr

header('Content-type: text/plain; charset=utf-8');
$action=strtolower(trim($_REQUEST['action']));

switch ($action){
   
   case "getid":
   		include($_SERVER['DOCUMENT_ROOT'].'/_static/neighborhoodCityFlatGroup.inc.php');//$neighborhoodCityGroupArr
	   	if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']){
	   		$city_id=$_REQUEST['city_id'];
	   		$neighborhoodCityGroupArr[$city_id];
	   	}   		
   		$neighborhoodCityFlatGroupArr=$neighborhoodCityFlatGroupArr[$city_id];
		$query="SELECT id FROM `tb_neighborhoods`
					WHERE `title` = '{$q}'
			   ";
		$result=$Db->query($query);

		if(($result->num_rows)){
			$row=$Db->get_stream($result);
			echo $row['id'];
		}
		
   break;
   
   
   case "getitems":

   	if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']){
   		$city_id=$_REQUEST['city_id'];
   		$neighborhoodCityGroupArr=$neighborhoodCityGroupArr[$city_id];

   	}
   	
   	if($q){
   		foreach ($neighborhoodCityGroupArr AS $key=>$title){
   			if(strstr($title['title'],$q)){
   				echo $title['title']."\n";
   			}
   		}
   	}   
   break;   
   
   default:
   	
	break;
}


?>