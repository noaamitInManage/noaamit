<?
$act= (isset($_REQUEST['act'])) ?trim($_REQUEST['act']) : trim($_REQUEST['action']);
$id=$Db->make_escape($_REQUEST['id']);
$question_id= (isset($_REQUEST['question_id']) && $_REQUEST['question_id']) ?$Db->make_escape($_REQUEST['question_id']) : '';
$active=$Db->make_escape($_REQUEST['active']);

// web_service ans
$answer=array("err"=>"","msg"=>"","relocation"=>"","html"=>"","reload");
$classArr=array(
   0=>'no',
   1=>'yes',
);
$colorArr=array(
   0=>'red',
   1=>'green',
);
$_yesNo_arr = array(
  '0' => 'לא',
  '1' => 'כן',
);

switch (strtolower($act)){
  
	case 'deals_ac':
		  $q=($_REQUEST['q']) ? trim($Db->make_escape($_REQUEST['q'])) : '';
		  $query="SELECT `id`,`title` FROM `tb_items` WHERE `title` LIKE '%{$q}%'";

		  $result=mysql_query($query) or die(mysql_error());
		  while($row = mysql_fetch_assoc($result)) {
		  	echo $row['title'].' ('.$row['id'].") \n";
		  }
		
		break;
		
	case 'get_deal_id':
		
			$title=($_REQUEST['title']) ? $Db->make_escape(trim($_REQUEST['title'])) : ''; //
			 
			$query="SELECT `id` FROM `tb_items` WHERE CONCAT(`title`,' (',`id`,')') = '{$title}' LIMIT 1";

			$result=mysql_query($query)or die(mysql_error());

		    $row=mysql_fetch_assoc($result);

		    echo $row['id'];
		break;	
	
	case 'add_upsale_link':
 
			$title=$_REQUEST['title'];
			$menu_id=$_REQUEST['upsale_id'];			
			$item_id=$_REQUEST['deal_id'];			
			/*validation - if there is already 10 like question dont allow anymore */ 
			$tb_name='tb_menu_items';
			


			
			$query="SELECT `item_id` FROM `{$tb_name}` WHERE `menu_id`='{$menu_id}' AND `item_id`='{$item_id}'";
			$result=mysql_query($query);
			
			$r=mysql_query($q);
/*			if(mysql_num_rows($r)>=2){
				$answer['err']='עד 2 מבצעים המקושרים לupsale';
				exit(json_encode($answer));
			}*/
			if(mysql_num_rows($result)){
				$answer['err']='פריט זה כבר מקושר לתפריט, אנא בחר פריט אחר';
				exit(json_encode($answer));				
			}
			
			
			$q="SELECT MAX(`order_num`) AS 'max_num' FROM `tb_menu_items` WHERE `menu_id`='{$menu_id}'" ;
			$r=mysql_fetch_assoc(mysql_query($q))	;	
			$db_fields=array(
				"menu_id"=>trim($menu_id),
				"item_id"=>trim($item_id),
				"order_num"=>++$r['max_num']
			);
			
			foreach($db_fields AS $key=>$value){
				$db_fields[$key]=$Db->make_escape($value);
			}
			
			$query = "INSERT INTO `{$tb_name}` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
			$res = mysql_query($query) ;
			if(mysql_error()){
				
					$answer['err']='פריט זה כבר מקושר לתפריט, אנא בחר פריט אחר'.mysql_error();
					exit(json_encode($answer));				
			}			 
			$answer['html']=get_link_upsale($menu_id);
			exit(json_encode($answer));			
		break;
		    	
	case 'del_upsale_link':
		$title=trim($_REQUEST['title']);
		list($menu_id,$item_id)=explode('_',$title);

		
		$query="SELECT * FROM `tb_menu_items` WHERE `item_id`='{$item_id}' AND `menu_id`='{$menu_id}'";
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);

		if($menu_id){
			mysql_unbuffered_query("DELETE FROM `tb_menu_items` WHERE `item_id`='{$item_id}' AND `menu_id`='{$menu_id}'");
			mysql_unbuffered_query("UPDATE `tb_menu_items`  SET `order_num`=`order_num`-1 WHERE `order_num` > '{$row['order_num']}' AND `menu_id`='{$menu_id}'");			
		}
			exit(json_encode($answer));
			//updateStatic file
		break;
		
	case 'get_questions_synonyms':
			if(!$question_id){
				$answer['err']='ארעה שגיאה';
				exit(json_decode($answer));
			}
			
			$answer['html']=get_questions_synonyms($question_id);
			exit(json_encode($answer));
			
		break;	

		
	case 'order':

            parse_str(str_replace('#','&',$_REQUEST['params']),$paramsArr);
			
            $curr_order=$new_order=$paramsArr['order_num'];

            switch (trim(strtolower($paramsArr['dir']))){
            	case 'up':
            		$new_order--;
            		break;
            		
            	case 'down':

            		$new_order++;            	            		
            		break;	
            }
           // $new_order=($paramsArr['dir']=='up')? ($paramsArr['order_num']--):($paramsArr['order_num']++);
            $query="UPDATE `tb_menu_items` 
            							SET `order_num`='{$curr_order}' 
            								WHERE `order_num`='{$new_order}' 
            											AND 
            									  `menu_id`='{$paramsArr['menu_id']}' 
            					   ";
             
         //   mysql_query($query) or die(mysql_error().$query);

            mysql_unbuffered_query("UPDATE `tb_menu_items` 
            							SET `order_num`='999' 
            								WHERE `order_num`='{$new_order}' 
            											AND 
            									  `menu_id`='{$paramsArr['menu_id']}' 
            					   ");
                        
            mysql_unbuffered_query("UPDATE `tb_menu_items` 
            							SET `order_num`='{$new_order}' 
            								WHERE `order_num`='{$curr_order}' 
            											AND 
            									  `menu_id`='{$paramsArr['menu_id']}' 
            					   ");
            mysql_unbuffered_query("UPDATE `tb_menu_items` 
            							SET `order_num`='{$curr_order}' 
            								WHERE `order_num`='999' 
            											AND 
            									  `menu_id`='{$paramsArr['menu_id']}' 
            					   ");            
            
            
			$answer['html']=get_link_upsale($paramsArr['menu_id']);
			exit(json_encode($answer));			
		break;	
}


?>