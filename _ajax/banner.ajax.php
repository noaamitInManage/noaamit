<?

$act = ($_REQUEST['act'])? $_REQUEST['act'] : 'add';
$id = intval($Db->make_escape($_REQUEST['id']));
$id -= 333; // const num for not reveal the real banner id
switch($act){

	case 'add':
		advertisersManager::clickCounter($id);
		break;

}

?>