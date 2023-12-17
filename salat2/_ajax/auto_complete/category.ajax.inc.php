<?
$tb_name='tb_category';
header('Content-type: text/plain; charset=utf-8');


$action=$_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action){
   
   case "getId":

         $q = $Db->make_escape(trim($_REQUEST['title']));
         $sub_post=strpos($q,'{');//tell us if this a sub category if yes we need to clean the string

         $parent_title = ($sub_post) ? substr($q,$sub_post,strpos($q,'}')) : '';
         $q=($sub_post>1) ? substr($q,0,$sub_post) : $q ;
         $q= trim(str_replace('&nbsp;','',$q));

         $query="SELECT id,title 
         					FROM `{$tb_name}` 
         						WHERE `title` LIKE '{$q}'";
         if($parent_title){
         	
			 $parent_title=trim(str_replace(array('{','}'),'',$parent_title));
	         $query="SELECT Cat.id,Cat.title 
	         					FROM `{$tb_name}` AS Cat
	         					
	         					LEFT JOIN `{$tb_name}` AS Parent ON (
	         						Parent.id=Cat.parent_id
	         					)
	         						WHERE Cat.`title` LIKE '{$q}' AND
	         							Parent.`title` LIKE '{$parent_title}'
	         						
	         		";         	
         }
         
         $result=$Db->query($query) or die($query.mysql_error());
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
         $query="SELECT DISTINCT(Cat.id),IF(Cat.level=1,Cat.title, CONCAT (Cat.title, ' { ',Parent.title,' } ' )) AS 'title',Cat.level
         					FROM `{$tb_name}` AS Cat 
         						LEFT JOIN `{$tb_name}` AS Parent ON(
         							Cat.`parent_id`=Parent.`id`
         						)
         							WHERE Cat.`title` LIKE '{$q}%'
         								ORDER BY `title` ASC
         		"
         ;
//die('<hr /><pre>' . print_r($query, true) . '</pre><hr />');
         $result=$Db->query($query) ;
         while($row = $Db->get_stream($result)) {
         	//echo ($row['level'] >1) ? str_repeat(" ",($row['level']-1)).$row['title']. "\n" : $row['title']. "\n";
         	echo $row['title']. "\n";
         } 
      }
   break;   
   
   case "getItemsDynamicValues":
    if(strlen(trim($q))== 0){
      $query="SELECT id,title FROM `{$tb_name}`";
      $result=$Db->query($query);
      
      while($row = $Db->get_stream($result)) {
      	echo $row['title']. "\n";
      }
      }else{
         //$query="SELECT id,title FROM `{$tb_name}` WHERE `title` LIKE '{$q}%'";
         $query="SELECT DISTINCT(Cat.id),IF(Cat.level=1,Cat.title, CONCAT (Cat.title, ' { ',Parent.title,' } ' )) AS 'title',Cat.level
         					FROM `{$tb_name}` AS Cat 
         						LEFT JOIN `{$tb_name}` AS Parent ON(
         							Cat.`parent_id`=Parent.`id`
         						)
         							WHERE Cat.`title` LIKE '{$q}%'
         								ORDER BY `title` ASC
         		"
         ;
         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	echo $row['title']. "\n";
         } 
      }
   break; 
}

?>