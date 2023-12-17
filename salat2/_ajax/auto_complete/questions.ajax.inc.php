<?
$tb_name='tb_questions';
header('Content-type: text/plain; charset=utf-8');


$action=$_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action){
   
   case "getQuestionsId":
         $q = $Db->make_escape(trim($_REQUEST['title']));
         $query="SELECT id FROM `{$tb_name}` WHERE `title` LIKE '%{$q}%'";
         $result=$Db->query($query) or die($query);
         $row=$Db->get_stream($result);
         echo $row['id'];
   break;
   
   
   case "getQuestions":
    if(strlen(trim($q))== 0){
      $query="SELECT `title`  FROM `{$tb_name}`";
      $result=$Db->query($query) or die($query);
      while($row = $Db->get_stream($result)) {
      	echo $row['title']. "\n";
      }
      }else{
         $query="SELECT `id`,`title` FROM `{$tb_name}` WHERE `title` LIKE '%{$q}%' ";
         $result=$Db->query($query) or die($query);
         while($row = $Db->get_stream($result)) {
         	echo  "  ".$row['title']. "\n";
         } 
      }
   break;   
   

}




 



?>