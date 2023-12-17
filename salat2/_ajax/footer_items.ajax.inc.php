<?php

set_time_limit(0);
$act=($_REQUEST['action']) ? $_REQUEST['action'] : 'show' ;
$err='';
$html='';
$status = 'false';

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
$UpdateStatic = new footerParagraphLangsUpdateStaticFiles;

switch ($act){

case "show":
	$html = var_export($_REQUEST,true);
	break;
case "add":
	$paragraph_id = intval($_REQUEST['paragraph_id']);
	$item_id = intval($_REQUEST['item_id']);
	// check that item is NOT already in the group
	$sql = "SELECT `id` FROM `tb_footer_ordering` WHERE `item_id`={$item_id}";
	$res = $Db->query($sql);
	if ($res->num_rows){
		$err = "Can't have same item appearing twice!";
		break;
	}else{
		// get next order_num value
		$sql = "SELECT MAX(order_num) AS max_order_num FROM `tb_footer_ordering`";
		$res = $Db->query($sql);
		if ($res){ 
			$result = $Db->get_stream($res);
		}else{
			// this is the first item in group
			$result['max_order_num'] = 1;
		}
		$next_order_num = $result['max_order_num']+1;
		$sql = "INSERT INTO `tb_footer_ordering` (item_id,group_id,order_num) VALUES ({$item_id},{$paragraph_id},{$next_order_num})";
		$res = $Db->query($sql);
		if (!$res){
			$err = mysql_error($res);
		}else{
			$status = true;
			$html = drawGroupItems($paragraph_id);
		}
	}

	$UpdateStatic->updateStatics();
	break;
case "move":
	$paragraph_id = intval($_REQUEST['paragraph_id']);
	$item_index = intval($_REQUEST['item_index']);
	$direction = $_REQUEST['direction'];
	$other_index = '';
	if ($direction == 'down'){
		$other_index = $item_index+1;
	}else{
		$other_index = $item_index-1;
	}
	$sql = "
			UPDATE `tb_footer_ordering` SET `order_num` =
			CASE `order_num`
				WHEN {$item_index} THEN {$other_index}
				WHEN {$other_index} THEN {$item_index}
				ELSE `order_num`
			END 
		WHERE `group_id`={$paragraph_id}";
		$res = $Db->query($sql);
		if (!$res){
			$err = mysql_error($res);
			break;
		}
		$status = true;
		$html = drawGroupItems($paragraph_id);
		$UpdateStatic->updateStatics();
	break;
case "delete":
	$paragraph_id = intval($_REQUEST['paragraph_id']);
	$item_index = intval($_REQUEST['item_index']);
	// first delete item
	$sql = "DELETE FROM `tb_footer_ordering` WHERE `group_id`={$paragraph_id} AND `order_num`={$item_index}";
	$res = $Db->query($sql);
	if (!$res){ 
		$status = false; 
		$err = mysql_error($res);
	}
	// update other records to correct order_num
	$sql = "UPDATE `tb_footer_ordering` SET `order_num`=order_num-1 WHERE `order_num`>{$item_index} AND `group_id`={$paragraph_id}";
	$res = $Db->query($sql);
	if (!$res){ 
		$status = false; 
		$err = mysql_error($res);
	}
	$status = true;
	$html = drawGroupItems($paragraph_id);
	$UpdateStatic->updateStatics();
	break;
case "autocomplete":
	$q = $Db->make_escape(trim($_REQUEST['q']));
	$sql = "SELECT * FROM `tb_footer_lang` WHERE `lang_id`={$module_lang_id} AND `content` LIKE '%{$q}%'";  
	$res = $Db->query($sql);
	while($item = $Db->get_stream($res)){
		echo $item['content']  .'|'.  $item['obj_id']."\n";
	}
	exit();
default:
	break;
}


echo json_encode(array("err"=>$err,"status"=>$status,'html'=>$html));


?>

