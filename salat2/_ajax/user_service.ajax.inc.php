<?
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");

$responseArr = array("err"=>'',"status"=>0,'html'=>'');
$err='';
$msg='';
$html='';
$status = false;
$act= $_REQUEST['act'] ? $_REQUEST['act'] : $_REQUEST['action'];

switch ($act){
   case 'delete_rows':
	   $query="DELETE FROM `tb_salat_examples_link` WHERE id={$_REQUEST['id']}";
	   $res=$Db->query($query);
	   $msg='OK';
   break;
	case 'new_order':
		$query="UPDATE `tb_salat_examples` SET order_num={$_REQUEST['order_num']} WHERE `id`={$_REQUEST['id']}";
		$result=$Db->query($query);
		$msg='OK';
	break;

	case 'add_tag_user':

		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] && isset($_REQUEST['tag_id']) && $_REQUEST['tag_id']){
			$query = "SELECT *
                        FROM `tb_users__tags`
                            WHERE `user_id` = {$_REQUEST['user_id']} AND `tag_id` = {$_REQUEST['tag_id']}";
			$res = $Db->query($query);
			if(!$res->num_rows){
				$db_fields = array(
					'user_id' => $_REQUEST['user_id'],
					'tag_id' => $_REQUEST['tag_id'],
					'last_update' => time()
				);
				foreach ($db_fields AS $key => $value) {
					$db_fields[$key] = $Db->make_escape($value);
				}
				$query = "INSERT INTO `tb_users__tags` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
				$Db->query($query);
				$html = draw_connected_user_tags($_REQUEST['user_id']);
				$status = 1;
			}else{
				$status = 0;
				$err = 'קטגוריה זו כבר משויכת';
			}
		}
		break;


	case 'delete_tag_user':

		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] && isset($_REQUEST['tag_id']) && $_REQUEST['tag_id']){
			$query = "DELETE FROM `tb_users__tags` WHERE `user_id` = {$_REQUEST['user_id']} AND `tag_id` = {$_REQUEST['tag_id']}";
			$Db->query($query);
			$html = draw_connected_user_tags($_REQUEST['user_id']);
			$status = 1;
		}

		break;
}
$responseArr['html'] = $html;
$responseArr['err'] = $err;
$responseArr['msg'] = $msg;
$responseArr['status'] = $status;
exit(json_encode($responseArr));
?>