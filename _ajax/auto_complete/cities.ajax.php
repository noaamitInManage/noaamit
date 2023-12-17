<?

$tb_name='tb_cities';  
$_REQUEST['title']= ((!$_REQUEST['title']) && $_REQUEST['q']) ? $_REQUEST['q'] : $_REQUEST['title'];

$q = (isset($_REQUEST['title']))? $Db->make_escape(trim($_REQUEST['title'])) : '';
/*if(!$q){
	exit();
}*/
include($_SERVER['DOCUMENT_ROOT'].'/_static/areas.inc.php');//$areasArr
include($_SERVER['DOCUMENT_ROOT'].'/_static/citiesFlat.inc.php');//$citiesFlatArr
$seconds=30;
header('Content-type: text/plain; charset=utf-8');
header("Cache-Control: private, max-age=$seconds");
header("Expires: ".gmdate('r', time()+$seconds));
$action=strtolower(trim($_REQUEST['action']));

switch ($action){
   
   case "getid":
		$city_id= array_search($_REQUEST['title'],$citiesFlatArr);
		if($city_id){
			$_SESSION['city_id']=$city_id;
		}else if(isset($_REQUEST['top'])){
			foreach ($areasArr AS $key=>$value){
				if(trim(reset(explode('- {',$value)))==trim($_REQUEST['title'])){
					$query="SELECT `city_id` FROM `tb_areas` WHERE `id`='{$key}'";
					$result=$Db->query($query);
					$row=$Db->get_stream($result);
					$city_id=''.$row['city_id'].'&t_area='.$key;
					$_SESSION['t_area']=$key;
					$_SESSION['city_name']=$_REQUEST['title'];
				}
			} 
		}

		if(!$city_id){
			if(isset($_REQUEST['top'])){

				$area_id = array_search(trim($_REQUEST['title']),$main_areasArr);
				$city_id = '&area_id='.$area_id; 
				
				if($area_id){
					$_SESSION['area_id']=$area_id;
					unset($_SESSION['city_id']);
					unset($_SESSION['t_area']);
				}
			}
		}
         echo $city_id;
   break;
   
   case "getitems": //site
   	if($q){
   		
   		foreach ($citiesFlatArr AS $key=>$title){
   			if(strstr($title,$q,0)){
   				echo $title."\n";
   			}
   		}
		if(isset($_REQUEST['top'])){
			foreach ($areasArr AS $key=>$title){
	   			if(strstr($title,$q,0)){
	   				echo reset(explode('- {',$title))."\n";
	   			}
   			}   	
   			
   			foreach ($main_areasArr AS $key=>$title){
   					if(strstr($title,$q,0)){
   						echo $title."\n";
   					}
   			}
		}
		
   	}   
   break;   
   
   case "getitemsmo": // mobile

   $resultArr=array();
   	if($q){
   		
   		foreach ($citiesFlatArr AS $key=>$title){
   			if(strstr($title,$q,0)){
   				$resultArr[]=array('id'=>$key,'title'=>$title,'type'=>'city_id');

   			}
   		}
		if(isset($_REQUEST['top'])){
			foreach ($areasArr AS $key=>$title){
	   			if(strstr($title,$q,0)){
   					$resultArr[]=array('id'=>$key,'title'=>reset(explode('- {',$title)),'type'=>'t_area');	   				
	   			}
   			}   	
   			
   			foreach ($main_areasArr AS $key=>$title){
   					if(strstr($title,$q,0)){
   					$resultArr[]=array('id'=>$key,'title'=>$title,'type'=>'area_id');	   				
   					}
   			}
		}
		
   	}   
   	exit(json_encode($resultArr));
   break;     
   
   case "gettitle":
	    $city_id=$_REQUEST['city_id'];
	   	if($city_id){
	   		echo $citiesFlatArr[$city_id]."\n";
	   	}   
   	break;     
   	
   	case "saveid":
   	
   		$params=explode('&',$_REQUEST['ex']);
   		if(isset($_SESSION['t_area'])){
   			unset($_SESSION['t_area']);
   		}
		 
   		list($_,$city_id)=explode('=',reset($params));

   		$_SESSION['city_id']=$city_id; 
   		if(trim($_)=='area_id'){
   			$_SESSION['area_id']=$city_id;
   		/*	foreach ($top_main_areasArr[1]['items'] AS $key=>$value){
   				if(strstr($value['extrea_param'],$_.'='.$_SESSION['area_id'])){
   					$_SESSION['city_name']=$value['title'];
   				}
   			}
   		*/
   		}else if(isset($_SESSION['area_id'])){
   			$_SESSION['area_id']=0;
   			unset($_SESSION['area_id']);
   		}

   		if(isset($params[1])){//t_area
   			list($_,$area_id)=explode('=',($params[1])); 
   			if($area_id){
   				$_SESSION[$_]=$area_id;
   			}
   		}
   	break;  	
   	/* if city dont have street rmove class "must" from streets fields */
   	case 'check_city_streets':
   		$city_id = (isset($_REQUEST['city_id']) && $_REQUEST['city_id']) ? ($_REQUEST['city_id']) : 0;
   		if(!$city_id){
   			exit(json_encode(array('total'=>0)));
   		}
   			include($_SERVER['DOCUMENT_ROOT'].'/_static/streetGroup/streetGroup-'.$city_id.'.inc.php'); //$streetGroupArr
   			$total=(count($streetGroupArr) < 5 )? 0 : 1;
   			exit(json_encode(array('total'=>$total ))); 
   			
   			
   		break;
   	
   default:
   	
	break;
}


?>