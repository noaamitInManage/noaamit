<?
$tb_name='tb_cities';
header('Content-type: text/plain; charset=utf-8');


$action=$_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action){
   
   case "getId":
         $q = $Db->make_escape(trim($_REQUEST['title']));
         $query="SELECT id,title FROM `{$tb_name}` WHERE `title` LIKE '{$q}'";
         $result=$Db->query($query);
         $row=$Db->get_stream($result);
         echo $row['id'];
   break;
   
   
   case "getItems":
    if(strlen(trim($q))== 0){
      $query="SELECT id,title FROM `{$tb_name}`";
      $result=$Db->query($query);
      
      while($row = $Db->get_stream($result)) {
      	echo $row['title']. "\n";
      }
      }else{
         $query="SELECT id,title FROM `{$tb_name}` WHERE `title` LIKE '{$q}%'";

         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	echo $row['title']. "\n";
         } 
      }
      
   break;   
   

}




 



?>