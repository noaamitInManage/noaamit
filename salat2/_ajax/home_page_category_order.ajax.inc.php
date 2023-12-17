<?

$ids=$_REQUEST['ids'];
$act=$_REQUEST['act'];
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

switch ($act) {



   case 'update':
      foreach ($ids AS $key=>$value){
         $order=$key+1;
        $query="UPDATE `tb_category`  SET  `home_page_order_num`='{$order}'  WHERE `id`='{$value}'" ;
        $Db->query($query);
      }
   break;
   
   
   case "static":

       $UpdateStatic = new categoryUpdateStaticFiles();
   	   $UpdateStatic->updateStatics();
      
      break;
   
}



?>