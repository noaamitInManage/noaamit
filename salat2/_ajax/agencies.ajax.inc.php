<?
set_time_limit(0);
$act=($_REQUEST['act']) ? $_REQUEST['act'] : 'show' ;
$err='';
$status = 'false';

switch ($act){

case "changePassword":

	$status = "fail";
	if (userHasPermissions(62)){ // agencies processid is 62
		$agent_id = intval($_REQUEST['agency']);
		$rawPassword = $_REQUEST['password'];
		$cleanPassword = $Db->make_escape($rawPassword);
		$newPassword = md5('gene'.$cleanPassword.'sis');
		$passwordSql = "UPDATE `tb_agents` SET `password`='{$newPassword}' WHERE `id`={$agent_id}";
		$passwordRes = $Db->query($passwordSql);
		if ($passwordRes){
			$status = "ok";
		}
	}else{
		$err = 'No permissions';
	}
	
	break;
default:
	break;
}


echo json_encode(array("err"=>$err,"status"=>$status));


?>

