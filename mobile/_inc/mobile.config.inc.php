<?

/**
 * 	citywall mobile config
 * 
 */



$jquery_mobile_version='1.3.0';
$jquery_version='1.9.1';
$data_theme='c';
$data_transition='slide';
$video_link='/_media/images/mobile/video_preview.jpg';


$items_per_page=25;


$mobile_navigationArr=array(
	'sort_frm'=>'home',
	'sort_frm_search'=>'home',
	
	'category'=>'sort_frm',
	
	'post'=>'category',
	'search_post'=>'category',
	
	'report'=>'post',
	
	'history'=>'home',
	'favorites'=>'home',

);

if(isset($_REQUEST['serach_post']) && ($_REQUEST['serach_post'])){
	$mobile_navigationArr['report']='search_post';
}


function save_history($url){
	global $mdlName;
	$excludedArr=array('/','/404','');
	if(!in_array($url,$excludedArr)){
		if(count($_SESSION['mobile_history']) >12){
			$_SESSION['mobile_history']=array();
		}
		$_SESSION['mobile_history'][$mdlName]=$url;
	}
	if(!isset($_SESSION['mobile_history']['home'])){
		$_SESSION['mobile_history']['home']='/';
	}
}

function load_back_url($cur_url){
	$linksArr=array_reverse($_SESSION['mobile_history']);

	foreach ($linksArr AS $key=>$value){
		if($value!=$cur_url){
			return $value;
		}
	}
	return false;
}
function load_back_module_url ($mdlName){
	global $mobile_navigationArr;
		
	if($mdlName=='post'){
		
	}
	$url= isset($_SESSION['mobile_history'][$mobile_navigationArr[$mdlName]]) ? $_SESSION['mobile_history'][$mobile_navigationArr[$mdlName]] : '';
	if(($mdlName=='post') && (!$url)){
		$url='http://m.citywall.co.il/';
	}else if(($mdlName=='search_post') && (!$url)){//search_post
		$url='http://m.citywall.co.il/';
	}
	
	return $url;
}
?>