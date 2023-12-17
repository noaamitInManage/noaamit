<?
$tb_name='tb_streests';  
$_REQUEST['title']= ((!$_REQUEST['title']) && $_REQUEST['q']) ? $_REQUEST['q'] : $_REQUEST['title'];
//$q = (isset($_REQUEST['title']))? $Db->make_escape(trim($_REQUEST['title'])) : '';
$q = (isset($_REQUEST['title']))? (trim($_REQUEST['title'])) : ''; 		
$city_id = $Db->make_escape(trim($_REQUEST['city_id']));
if(!$q){
	exit();
}
include($_SERVER['DOCUMENT_ROOT'].'/_static/streetGroup/streetGroup-'.$city_id.'.inc.php');//$streetGroupArr
include($_SERVER['DOCUMENT_ROOT'].'/_static/neighborhood.inc.php');//$neighborhoodsArr
$seconds=30;
header('Content-type: text/plain; charset=utf-8');
header("Cache-Control: private, max-age=$seconds");
header("Expires: ".gmdate('r', time()+$seconds));

$action=strtolower($_REQUEST['action']);

switch ($action){
   
   case "getid":
         $val=$streetGroupArr[array_mixed_search(array("title"=>$_REQUEST['title']),$streetGroupArr)];
         $tmp=explode('- {',$neighborhoodsArr[$val['neighborhood_id']]);

         $val['neighborhood']=trim($tmp[0]);
         echo json_encode($val);
         
   break;
   
   case "getitems":
   	if(strlen($q)>2){
   		$find=false;
   		$qArr=explode(' ',$q);
 /*  		if(count($qArr) >= 2){
	   		foreach ($streetGroupArr AS $key=>$value){
	   			
	   			foreach ($qArr AS $sub_key){
		   			if(strstr($value['title'],$sub_key)){
		   				echo $value['title']."\n";
		   				$find=true;
		   				break;
		   			}	   				
	   			}

	   		}   			
   			
   		}*/
   		//if(!$find) {
	   		foreach ($streetGroupArr AS $key=>$value){
	   			
	   			if(strpos($value['title'],' ')){
	   				$value_rev=trim(implode(' ',array_reverse(explode(' ',$value['title']),true)));
		   			if(strstr($value_rev,$q)){
		   				echo $value['title']."\n";
		   			}else if(strstr($value['title'],$q)){
		   				echo $value['title']."\n";	
		   			}
		   			   				
	   			}
	   			 else if(strstr(trim($value['title']),$q)){
	   				echo $value['title']."\n";
	   			}
	   		}
   		//} 
   	} /*else{
	   		foreach ($streetGroupArr AS $key=>$value){

	   			 if(strstr($value['title'],$q)){
	   				echo $value['title']."\n";
	   			}
	   		}   		
   	}*/
   break;   
   

}




 



?>